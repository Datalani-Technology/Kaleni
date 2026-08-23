<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <style>
        body { font-family: sans-serif; line-height: 1.5; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        h1 { font-size: 1.25rem; margin-bottom: 1rem; }
        .btn { display: inline-block; padding: 12px 24px; background: #d63384; color: white !important; text-decoration: none; border-radius: 8px; margin: 1rem 0; }
        table { width: 100%; border-collapse: collapse; margin: 1rem 0; }
        td { padding: 6px 0; border-bottom: 1px solid #eee; }
        .muted { color: #666; font-size: 0.9rem; }
    </style>
</head>
<body>
    <h1>Thank you, {{ $order->customer_name }}! 🌸</h1>
    <p>Your order has been received and is being prepared.</p>
    <table>
        <tr><td class="muted">Order number</td><td><strong>{{ $order->order_number }}</strong></td></tr>
        <tr><td class="muted">Total</td><td><strong>N$ {{ number_format((float) $order->total_amount, 2) }}</strong></td></tr>
        <tr><td class="muted">Payment method</td><td>{{ ucfirst($order->payment_method) }}</td></tr>
        <tr><td class="muted">Recipient</td><td>{{ $order->recipient_name ?: $order->customer_name }}</td></tr>
        <tr><td class="muted">Delivery date</td><td>{{ $order->delivery_date?->format('D, j M Y') ?: 'To be confirmed' }} · {{ ucfirst($order->delivery_window ?: 'anytime') }}</td></tr>
        <tr><td class="muted">Delivery address</td><td>{{ $order->delivery_address }}</td></tr>
        @if($order->gift_message)<tr><td class="muted">Card message</td><td>{{ $order->gift_message }}</td></tr>@endif
    </table>
    <p><a href="{{ $receiptUrl }}" class="btn">View / print your receipt</a></p>
    <p class="muted">Questions about your order? Just reply to this email or reach us on WhatsApp.</p>
</body>
</html>
