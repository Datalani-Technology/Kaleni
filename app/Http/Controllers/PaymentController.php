<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function initiateDPO(Request $request, $booking = null)
    {
        $bookingId = $booking ?? $request->booking;
        $booking = Booking::findOrFail($bookingId);

        if ($booking->payment_method !== 'dpo') {
            return redirect()->route('booking.index')->with('error', 'Invalid payment method.');
        }

        $companyToken = config('services.dpo.company_token');
        if (empty($companyToken)) {
            Log::warning('DPO payment attempted without a configured company token', ['booking' => $booking->booking_number]);
            return redirect()->route('booking.index')
                ->with('error', 'Card payment isn\'t available yet. Please choose "Pay via WhatsApp" instead.');
        }

        if ($booking->payment_status === 'completed') {
            return redirect()->route('booking.success', $booking->booking_number);
        }

        $xml = '<?xml version="1.0" encoding="utf-8"?>' . "\n"
            . '<API3G>' . "\n"
            . '<CompanyToken>' . $this->escapeXml($companyToken) . '</CompanyToken>' . "\n"
            . '<Request>createToken</Request>' . "\n"
            . '<Transaction>' . "\n"
            . '<PaymentAmount>' . number_format((float) $booking->total_amount, 2, '.', '') . '</PaymentAmount>' . "\n"
            . '<PaymentCurrency>' . $this->escapeXml(config('services.dpo.currency', 'NAD')) . '</PaymentCurrency>' . "\n"
            . '<CompanyRef>' . $this->escapeXml($booking->booking_number) . '</CompanyRef>' . "\n"
            . '<RedirectURL>' . $this->escapeXml(route('payment.dpo.callback')) . '</RedirectURL>' . "\n"
            . '<BackURL>' . $this->escapeXml(route('booking.index')) . '</BackURL>' . "\n"
            . '<CompanyRefUnique>0</CompanyRefUnique>' . "\n"
            . '<PTL>5</PTL>' . "\n"
            . '<PnURL>' . $this->escapeXml(route('payment.dpo.notify')) . '</PnURL>' . "\n"
            . '</Transaction>' . "\n"
            . '<Services>' . "\n"
            . '<Service>' . "\n"
            . '<ServiceType>' . $this->escapeXml(config('services.dpo.service_type', '1')) . '</ServiceType>' . "\n"
            . '<ServiceDescription>Catering Booking - ' . $this->escapeXml($booking->booking_number) . '</ServiceDescription>' . "\n"
            . '<ServiceDate>' . date('Y/m/d H:i') . '</ServiceDate>' . "\n"
            . '</Service>' . "\n"
            . '</Services>' . "\n"
            . '</API3G>';

        try {
            $response = Http::timeout(20)->connectTimeout(8)
                ->withBody($xml, 'application/xml')
                ->post(config('services.dpo.api_url'));
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('DPO createToken connection failed', ['booking' => $booking->booking_number, 'error' => $e->getMessage()]);
            return redirect()->route('booking.index')
                ->with('error', 'Could not reach the payment gateway. Please try "Pay via WhatsApp" or try again shortly.');
        }

        if (!$response->successful()) {
            Log::error('DPO createToken HTTP error', ['booking' => $booking->booking_number, 'status' => $response->status()]);
            return redirect()->route('booking.index')
                ->with('error', 'Failed to initialize payment. Please try again.');
        }

        $data = $this->parseXmlResponse($response->body());

        if (($data['Result'] ?? null) === '000' && !empty($data['TransToken'])) {
            $booking->update(['dpo_token' => $data['TransToken']]);

            return redirect(config('services.dpo.pay_url') . '?ID=' . $data['TransToken']);
        }

        Log::error('DPO createToken rejected', ['booking' => $booking->booking_number, 'result' => $data['Result'] ?? null, 'explanation' => $data['ResultExplanation'] ?? null]);

        return redirect()->route('booking.index')
            ->with('error', 'Payment initialization failed: ' . ($data['ResultExplanation'] ?? 'Please try again.'));
    }

    /**
     * Customer's browser bounces back here after paying (or cancelling) on DPO.
     */
    public function dpoCallback(Request $request)
    {
        $transToken = $request->input('TransactionToken') ?? $request->input('ID');

        if (!$transToken) {
            return redirect()->route('booking.index')
                ->with('error', 'Payment verification failed. No transaction token received.');
        }

        $result = $this->verifyAndFinalize($transToken);

        if (!$result['booking']) {
            return redirect()->route('booking.index')->with('error', 'Booking not found.');
        }

        return match ($result['status']) {
            'completed' => redirect()->route('booking.success', $result['booking']->booking_number)
                ->with('success', 'Payment completed successfully!'),
            'pending' => redirect()->route('booking.index')
                ->with('error', 'Payment is still pending. Please complete the payment process.'),
            default => redirect()->route('booking.index')
                ->with('error', 'Payment failed: ' . $result['message']),
        };
    }

    /**
     * DPO's server-to-server Payment Notification (PNURL). More reliable than the
     * browser callback alone — fires even if the customer closes the tab before
     * being redirected back. Must return a plain 200 OK; DPO does not need a body.
     */
    public function dpoNotify(Request $request)
    {
        $transToken = $request->input('TransactionToken') ?? $request->input('ID');

        if (!$transToken) {
            Log::warning('DPO PNURL called without a transaction token', ['ip' => $request->ip()]);
            return response('missing token', 400);
        }

        $this->verifyAndFinalize($transToken);

        return response('OK', 200);
    }

    /**
     * Shared by the browser callback and the PNURL webhook so a payment is only
     * ever verified/applied once, regardless of which one arrives first.
     *
     * @return array{booking: ?Booking, status: string, message: string}
     */
    private function verifyAndFinalize(string $transToken): array
    {
        $booking = Booking::where('dpo_token', $transToken)->first();

        if (!$booking) {
            Log::warning('DPO verify: no booking matches transaction token');
            return ['booking' => null, 'status' => 'failed', 'message' => 'Booking not found.'];
        }

        // Already settled by the other channel (callback vs webhook) — don't re-verify.
        if ($booking->payment_status === 'completed') {
            return ['booking' => $booking, 'status' => 'completed', 'message' => 'Already confirmed.'];
        }

        $companyToken = config('services.dpo.company_token');
        $xml = '<?xml version="1.0" encoding="utf-8"?>' . "\n"
            . '<API3G>' . "\n"
            . '<CompanyToken>' . $this->escapeXml($companyToken) . '</CompanyToken>' . "\n"
            . '<Request>verifyToken</Request>' . "\n"
            . '<TransactionToken>' . $this->escapeXml($transToken) . '</TransactionToken>' . "\n"
            . '</API3G>';

        try {
            $response = Http::timeout(20)->connectTimeout(8)
                ->withBody($xml, 'application/xml')
                ->post(config('services.dpo.api_url'));
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('DPO verifyToken connection failed', ['booking' => $booking->booking_number, 'error' => $e->getMessage()]);
            return ['booking' => $booking, 'status' => 'pending', 'message' => 'Could not reach payment gateway.'];
        }

        if (!$response->successful()) {
            Log::error('DPO verifyToken HTTP error', ['booking' => $booking->booking_number, 'status' => $response->status()]);
            return ['booking' => $booking, 'status' => 'pending', 'message' => 'Gateway error.'];
        }

        $data = $this->parseXmlResponse($response->body());
        $resultCode = $data['Result'] ?? null;

        if ($resultCode === '000' || $resultCode === '001') {
            // Defence in depth: flag (but don't silently trust) an amount mismatch.
            $paidAmount = isset($data['TransactionAmount']) ? (float) $data['TransactionAmount'] : null;
            if ($paidAmount !== null && abs($paidAmount - (float) $booking->total_amount) > 0.01) {
                Log::error('DPO amount mismatch', [
                    'booking' => $booking->booking_number,
                    'expected' => (float) $booking->total_amount,
                    'paid' => $paidAmount,
                ]);
                $booking->update(['payment_status' => 'failed']);
                return ['booking' => $booking, 'status' => 'failed', 'message' => 'Amount mismatch. Contact support.'];
            }

            $booking->update(['payment_status' => 'completed', 'booking_status' => 'processing']);
            Log::info('DPO payment confirmed', ['booking' => $booking->booking_number]);
            return ['booking' => $booking, 'status' => 'completed', 'message' => 'Paid.'];
        }

        if ($resultCode === '900') {
            return ['booking' => $booking, 'status' => 'pending', 'message' => 'Payment still pending.'];
        }

        $booking->update(['payment_status' => 'failed']);
        $explanation = $data['ResultExplanation'] ?? 'Payment verification failed';
        Log::warning('DPO payment failed/declined', ['booking' => $booking->booking_number, 'result' => $resultCode, 'explanation' => $explanation]);

        return ['booking' => $booking, 'status' => 'failed', 'message' => $explanation];
    }

    public function whatsappPayment(Request $request, $booking = null)
    {
        $bookingId = $booking ?? $request->booking;
        $booking = Booking::findOrFail($bookingId);

        if ($booking->payment_method !== 'whatsapp') {
            return redirect()->route('booking.index')->with('error', 'Invalid payment method.');
        }

        $whatsappNumber = env('WHATSAPP_PAYMENT_NUMBER', '264813382817'); // Namibia format
        $whatsappNumber = preg_replace('/[^0-9]/', '', $whatsappNumber);

        $message = "🍽️ *New Booking: {$booking->booking_number}*\n\n";
        $message .= "👤 *Customer Details:*\n";
        $message .= "Name: {$booking->customer_name}\n";
        $message .= "Email: {$booking->customer_email}\n";
        $message .= "Phone: {$booking->customer_phone}\n";
        $message .= "\n*Event:*\n";
        $message .= "Type: " . ($booking->event_type ?: 'N/A') . "\n";
        $message .= "On-site contact: " . ($booking->onsite_contact_name ?: $booking->customer_name) . "\n";
        $message .= "Contact phone: " . ($booking->onsite_contact_phone ?: $booking->customer_phone) . "\n";
        $message .= "Schedule: " . $booking->schedule_summary . "\n";
        $message .= "Serving period: " . ucfirst(str_replace('_', ' ', $booking->serving_period ?: 'custom')) . "\n";
        $message .= "Guests: " . ($booking->guest_count ?: 'N/A') . "\n";
        $message .= "Address: {$booking->event_address}\n";
        if ($booking->special_message) {
            $message .= "Special message: {$booking->special_message}\n";
        }
        if ($booking->event_notes) {
            $message .= "Notes: {$booking->event_notes}\n";
        }
        $message .= "\n";
        $message .= "💰 *Total Amount: N$ " . number_format($booking->total_amount, 2) . "*\n\n";
        $message .= "📦 *Order Items:*\n";

        foreach ($booking->items as $item) {
            $message .= "• {$item->menuItem->name} x{$item->quantity} = N$ " . number_format($item->subtotal, 2) . "\n";
        }

        $message .= "\n✅ Please confirm payment and booking details.";

        $booking->update(['whatsapp_number' => $whatsappNumber]);

        $whatsappUrl = "https://wa.me/{$whatsappNumber}?text=" . urlencode($message);

        return redirect($whatsappUrl);
    }

    /**
     * Parse XML response from DPO API
     */
    private function parseXmlResponse($xml)
    {
        $data = [];
        try {
            $xmlObject = simplexml_load_string($xml);
            if ($xmlObject) {
                $data = json_decode(json_encode($xmlObject), true);
            }
        } catch (\Exception $e) {
            Log::error('DPO XML Parse Error: ' . $e->getMessage());
        }
        return $data;
    }

    private function escapeXml(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
