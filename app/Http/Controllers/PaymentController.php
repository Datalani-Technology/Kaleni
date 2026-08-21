<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    public function initiateDPO(Request $request, $order = null)
    {
        $orderId = $order ?? $request->order;
        $order = Order::findOrFail($orderId);
        
        if ($order->payment_method !== 'dpo') {
            return redirect()->route('checkout.index')->with('error', 'Invalid payment method.');
        }

        // DPO Payment Gateway Integration - API v6
        $companyToken = env('DPO_COMPANY_TOKEN', 'your-company-token');
        $serviceType = env('DPO_SERVICE_TYPE', '1'); // Default service type
        $testMode = env('DPO_TEST_MODE', true);

        $dpoUrl = 'https://secure.3gdirectpay.com/API/v6/';

        // Build XML request for createToken
        $xml = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
        $xml .= '<API3G>' . "\n";
        $xml .= '<CompanyToken>' . $companyToken . '</CompanyToken>' . "\n";
        $xml .= '<Request>createToken</Request>' . "\n";
        $xml .= '<Transaction>' . "\n";
        $xml .= '<PaymentAmount>' . number_format($order->total_amount, 2, '.', '') . '</PaymentAmount>' . "\n";
        $xml .= '<PaymentCurrency>NAD</PaymentCurrency>' . "\n";
        $xml .= '<CompanyRef>' . $order->order_number . '</CompanyRef>' . "\n";
        $xml .= '<RedirectURL>' . route('payment.dpo.callback') . '</RedirectURL>' . "\n";
        $xml .= '<BackURL>' . route('checkout.index') . '</BackURL>' . "\n";
        $xml .= '<CompanyRefUnique>0</CompanyRefUnique>' . "\n";
        $xml .= '<PTL>5</PTL>' . "\n"; // Payment Time Limit in days
        $xml .= '</Transaction>' . "\n";
        $xml .= '<Services>' . "\n";
        $xml .= '<Service>' . "\n";
        $xml .= '<ServiceType>' . $serviceType . '</ServiceType>' . "\n";
        $xml .= '<ServiceDescription>Flower Order - ' . $order->order_number . '</ServiceDescription>' . "\n";
        $xml .= '<ServiceDate>' . date('Y/m/d H:i') . '</ServiceDate>' . "\n";
        $xml .= '</Service>' . "\n";
        $xml .= '</Services>' . "\n";
        $xml .= '</API3G>';

        // Send XML request
        $response = Http::withBody($xml, 'application/xml')
            ->post($dpoUrl);

        if ($response->successful()) {
            $responseXml = $response->body();
            $data = $this->parseXmlResponse($responseXml);
            
            if (isset($data['Result']) && $data['Result'] == '000' && isset($data['TransToken'])) {
                $order->update(['dpo_token' => $data['TransToken']]);
                
                // Redirect to DPO payment page
                $paymentUrl = 'https://secure.3gdirectpay.com/payv2.php?ID=' . $data['TransToken'];
                return redirect($paymentUrl);
            } else {
                $errorMsg = $data['ResultExplanation'] ?? 'Failed to create payment token';
                return redirect()->route('checkout.index')
                    ->with('error', 'Payment initialization failed: ' . $errorMsg);
            }
        }

        return redirect()->route('checkout.index')
            ->with('error', 'Failed to initialize payment. Please try again.');
    }

    public function dpoCallback(Request $request)
    {
        $transToken = $request->input('TransactionToken') ?? $request->input('ID');
        
        if (!$transToken) {
            return redirect()->route('checkout.index')
                ->with('error', 'Payment verification failed. No transaction token received.');
        }

        $order = Order::where('dpo_token', $transToken)->first();
        
        if (!$order) {
            return redirect()->route('checkout.index')
                ->with('error', 'Order not found.');
        }

        // Verify payment with DPO using verifyToken
        $companyToken = env('DPO_COMPANY_TOKEN', 'your-company-token');
        $dpoUrl = 'https://secure.3gdirectpay.com/API/v6/';

        // Build XML request for verifyToken
        $xml = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
        $xml .= '<API3G>' . "\n";
        $xml .= '<CompanyToken>' . $companyToken . '</CompanyToken>' . "\n";
        $xml .= '<Request>verifyToken</Request>' . "\n";
        $xml .= '<TransactionToken>' . $transToken . '</TransactionToken>' . "\n";
        $xml .= '</API3G>';

        // Send verification request
        $response = Http::withBody($xml, 'application/xml')
            ->post($dpoUrl);

        if ($response->successful()) {
            $responseXml = $response->body();
            $data = $this->parseXmlResponse($responseXml);
            
            // Check payment status
            // Result codes: 000 = Paid, 001 = Authorized, 900 = Not paid yet, 901 = Declined
            if (isset($data['Result'])) {
                $resultCode = $data['Result'];
                
                if ($resultCode == '000' || $resultCode == '001') {
                    // Payment successful
                    $order->update([
                        'payment_status' => 'completed',
                        'order_status' => 'processing',
                    ]);

                    return redirect()->route('checkout.success', $order->order_number)
                        ->with('success', 'Payment completed successfully!');
                } elseif ($resultCode == '900') {
                    // Payment not completed yet
                    return redirect()->route('checkout.index')
                        ->with('error', 'Payment is still pending. Please complete the payment process.');
                } else {
                    // Payment failed or declined
                    $errorMsg = $data['ResultExplanation'] ?? 'Payment verification failed';
                    $order->update([
                        'payment_status' => 'failed',
                    ]);
                    
                    return redirect()->route('checkout.index')
                        ->with('error', 'Payment failed: ' . $errorMsg);
                }
            }
        }

        // If verification fails, mark as failed
        $order->update([
            'payment_status' => 'failed',
        ]);

        return redirect()->route('checkout.index')
            ->with('error', 'Payment verification failed. Please contact support.');
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
        $message .= "Address: {$order->delivery_address}\n\n";
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
            \Log::error('DPO XML Parse Error: ' . $e->getMessage());
        }
        return $data;
    }
}
