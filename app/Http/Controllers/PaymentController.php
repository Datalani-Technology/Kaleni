<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function initiateDPO(Request $request, $order = null)
    {
        $orderId = $order ?? $request->order;
        $order = Order::findOrFail($orderId);

        if ($order->payment_method !== 'dpo') {
            return redirect()->route('checkout.index')->with('error', 'Invalid payment method.');
        }

        $companyToken = config('services.dpo.company_token');
        if (empty($companyToken)) {
            Log::warning('DPO payment attempted without a configured company token', ['order' => $order->order_number]);
            return redirect()->route('checkout.index')
                ->with('error', 'Card payment isn\'t available yet. Please choose "Pay via WhatsApp" instead.');
        }

        if ($order->payment_status === 'completed') {
            return redirect()->route('checkout.success', $order->order_number);
        }

        $xml = '<?xml version="1.0" encoding="utf-8"?>' . "\n"
            . '<API3G>' . "\n"
            . '<CompanyToken>' . $this->escapeXml($companyToken) . '</CompanyToken>' . "\n"
            . '<Request>createToken</Request>' . "\n"
            . '<Transaction>' . "\n"
            . '<PaymentAmount>' . number_format((float) $order->total_amount, 2, '.', '') . '</PaymentAmount>' . "\n"
            . '<PaymentCurrency>' . $this->escapeXml(config('services.dpo.currency', 'NAD')) . '</PaymentCurrency>' . "\n"
            . '<CompanyRef>' . $this->escapeXml($order->order_number) . '</CompanyRef>' . "\n"
            . '<RedirectURL>' . $this->escapeXml(route('payment.dpo.callback')) . '</RedirectURL>' . "\n"
            . '<BackURL>' . $this->escapeXml(route('checkout.index')) . '</BackURL>' . "\n"
            . '<CompanyRefUnique>0</CompanyRefUnique>' . "\n"
            . '<PTL>5</PTL>' . "\n"
            . '<PnURL>' . $this->escapeXml(route('payment.dpo.notify')) . '</PnURL>' . "\n"
            . '</Transaction>' . "\n"
            . '<Services>' . "\n"
            . '<Service>' . "\n"
            . '<ServiceType>' . $this->escapeXml(config('services.dpo.service_type', '1')) . '</ServiceType>' . "\n"
            . '<ServiceDescription>Flower Order - ' . $this->escapeXml($order->order_number) . '</ServiceDescription>' . "\n"
            . '<ServiceDate>' . date('Y/m/d H:i') . '</ServiceDate>' . "\n"
            . '</Service>' . "\n"
            . '</Services>' . "\n"
            . '</API3G>';

        try {
            $response = Http::timeout(20)->connectTimeout(8)
                ->withBody($xml, 'application/xml')
                ->post(config('services.dpo.api_url'));
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('DPO createToken connection failed', ['order' => $order->order_number, 'error' => $e->getMessage()]);
            return redirect()->route('checkout.index')
                ->with('error', 'Could not reach the payment gateway. Please try "Pay via WhatsApp" or try again shortly.');
        }

        if (!$response->successful()) {
            Log::error('DPO createToken HTTP error', ['order' => $order->order_number, 'status' => $response->status()]);
            return redirect()->route('checkout.index')
                ->with('error', 'Failed to initialize payment. Please try again.');
        }

        $data = $this->parseXmlResponse($response->body());

        if (($data['Result'] ?? null) === '000' && !empty($data['TransToken'])) {
            $order->update(['dpo_token' => $data['TransToken']]);

            return redirect(config('services.dpo.pay_url') . '?ID=' . $data['TransToken']);
        }

        Log::error('DPO createToken rejected', ['order' => $order->order_number, 'result' => $data['Result'] ?? null, 'explanation' => $data['ResultExplanation'] ?? null]);

        return redirect()->route('checkout.index')
            ->with('error', 'Payment initialization failed: ' . ($data['ResultExplanation'] ?? 'Please try again.'));
    }

    /**
     * Customer's browser bounces back here after paying (or cancelling) on DPO.
     */
    public function dpoCallback(Request $request)
    {
        $transToken = $request->input('TransactionToken') ?? $request->input('ID');

        if (!$transToken) {
            return redirect()->route('checkout.index')
                ->with('error', 'Payment verification failed. No transaction token received.');
        }

        $result = $this->verifyAndFinalize($transToken);

        if (!$result['order']) {
            return redirect()->route('checkout.index')->with('error', 'Order not found.');
        }

        return match ($result['status']) {
            'completed' => redirect()->route('checkout.success', $result['order']->order_number)
                ->with('success', 'Payment completed successfully!'),
            'pending' => redirect()->route('checkout.index')
                ->with('error', 'Payment is still pending. Please complete the payment process.'),
            default => redirect()->route('checkout.index')
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
     * @return array{order: ?Order, status: string, message: string}
     */
    private function verifyAndFinalize(string $transToken): array
    {
        $order = Order::where('dpo_token', $transToken)->first();

        if (!$order) {
            Log::warning('DPO verify: no order matches transaction token');
            return ['order' => null, 'status' => 'failed', 'message' => 'Order not found.'];
        }

        // Already settled by the other channel (callback vs webhook) — don't re-verify.
        if ($order->payment_status === 'completed') {
            return ['order' => $order, 'status' => 'completed', 'message' => 'Already confirmed.'];
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
            Log::error('DPO verifyToken connection failed', ['order' => $order->order_number, 'error' => $e->getMessage()]);
            return ['order' => $order, 'status' => 'pending', 'message' => 'Could not reach payment gateway.'];
        }

        if (!$response->successful()) {
            Log::error('DPO verifyToken HTTP error', ['order' => $order->order_number, 'status' => $response->status()]);
            return ['order' => $order, 'status' => 'pending', 'message' => 'Gateway error.'];
        }

        $data = $this->parseXmlResponse($response->body());
        $resultCode = $data['Result'] ?? null;

        if ($resultCode === '000' || $resultCode === '001') {
            // Defence in depth: flag (but don't silently trust) an amount mismatch.
            $paidAmount = isset($data['TransactionAmount']) ? (float) $data['TransactionAmount'] : null;
            if ($paidAmount !== null && abs($paidAmount - (float) $order->total_amount) > 0.01) {
                Log::error('DPO amount mismatch', [
                    'order' => $order->order_number,
                    'expected' => (float) $order->total_amount,
                    'paid' => $paidAmount,
                ]);
                $order->update(['payment_status' => 'failed']);
                return ['order' => $order, 'status' => 'failed', 'message' => 'Amount mismatch — contact support.'];
            }

            $order->update(['payment_status' => 'completed', 'order_status' => 'processing']);
            Log::info('DPO payment confirmed', ['order' => $order->order_number]);
            return ['order' => $order, 'status' => 'completed', 'message' => 'Paid.'];
        }

        if ($resultCode === '900') {
            return ['order' => $order, 'status' => 'pending', 'message' => 'Payment still pending.'];
        }

        $order->update(['payment_status' => 'failed']);
        $explanation = $data['ResultExplanation'] ?? 'Payment verification failed';
        Log::warning('DPO payment failed/declined', ['order' => $order->order_number, 'result' => $resultCode, 'explanation' => $explanation]);

        return ['order' => $order, 'status' => 'failed', 'message' => $explanation];
    }

    public function whatsappPayment(Request $request, $order = null)
    {
        $orderId = $order ?? $request->order;
        $order = Order::findOrFail($orderId);

        if ($order->payment_method !== 'whatsapp') {
            return redirect()->route('checkout.index')->with('error', 'Invalid payment method.');
        }

        // Get WhatsApp number from environment or use default
        $whatsappNumber = env('WHATSAPP_PAYMENT_NUMBER', '264815574680'); // Namibia format
        $whatsappNumber = preg_replace('/[^0-9]/', '', $whatsappNumber);

        // Format order details for WhatsApp message
        $message = "🌸 *New Order: {$order->order_number}*\n\n";
        $message .= "👤 *Customer Details:*\n";
        $message .= "Name: {$order->customer_name}\n";
        $message .= "Email: {$order->customer_email}\n";
        $message .= "Phone: {$order->customer_phone}\n";
        $message .= "\n*Delivery:*\n";
        $message .= "Recipient: " . ($order->recipient_name ?: $order->customer_name) . "\n";
        $message .= "Recipient phone: " . ($order->recipient_phone ?: $order->customer_phone) . "\n";
        $message .= "Date: " . optional($order->delivery_date)->format('D, j M Y') . "\n";
        $message .= "Window: " . ucfirst($order->delivery_window ?: 'anytime') . "\n";
        $message .= "Address: {$order->delivery_address}\n";
        if ($order->gift_message) {
            $message .= "Gift message: {$order->gift_message}\n";
        }
        if ($order->delivery_instructions) {
            $message .= "Instructions: {$order->delivery_instructions}\n";
        }
        $message .= "\n";
        $message .= "💰 *Total Amount: N$ " . number_format($order->total_amount, 2) . "*\n\n";
        $message .= "📦 *Order Items:*\n";

        foreach ($order->items as $item) {
            $message .= "• {$item->product->name} x{$item->quantity} = N$ " . number_format($item->subtotal, 2) . "\n";
        }

        $message .= "\n✅ Please confirm payment and delivery details.";

        // Update order with WhatsApp number
        $order->update(['whatsapp_number' => $whatsappNumber]);

        // Create WhatsApp link
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
