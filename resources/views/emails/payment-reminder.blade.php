<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete your payment</title>
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
    <h1>Still want your flowers, {{ $order->customer_name }}? 🌸</h1>
    <p>We noticed your card payment for order <strong>{{ $order->order_number }}</strong> wasn't completed — no charge was made. If you ran into trouble or changed your mind partway through, you can finish paying below, or reply to this email and we'll help directly.</p>
    <table>
        <tr><td class="muted">Order number</td><td><strong>{{ $order->order_number }}</strong></td></tr>
        <tr><td class="muted">Total</td><td><strong>N$ {{ number_format((float) $order->total_amount, 2) }}</strong></td></tr>
        <tr><td class="muted">Delivery date</td><td>{{ $order->delivery_date?->format('D, j M Y') ?: 'To be confirmed' }}</td></tr>
    </table>
    <p><a href="{{ $payUrl }}" class="btn">Complete payment</a></p>
    <p class="muted">Having trouble with card payment? Reach us on WhatsApp and we'll sort it out another way.</p>
</body>
</html>
