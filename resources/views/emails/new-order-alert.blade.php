<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New order {{ $order->order_number }}</title>
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
    <h1>New order: {{ $order->order_number }}</h1>
    <table>
        <tr><td class="muted">Customer</td><td>{{ $order->customer_name }}</td></tr>
        <tr><td class="muted">Email</td><td>{{ $order->customer_email }}</td></tr>
        <tr><td class="muted">Phone</td><td>{{ $order->customer_phone }}</td></tr>
        <tr><td class="muted">Recipient</td><td>{{ $order->recipient_name ?: $order->customer_name }} · {{ $order->recipient_phone ?: $order->customer_phone }}</td></tr>
        <tr><td class="muted">Delivery</td><td>{{ $order->delivery_date?->format('D, j M Y') ?: 'To be confirmed' }} · {{ ucfirst($order->delivery_window ?: 'anytime') }}</td></tr>
        <tr><td class="muted">Address</td><td>{{ $order->delivery_address }}</td></tr>
        <tr><td class="muted">Total</td><td><strong>N$ {{ number_format((float) $order->total_amount, 2) }}</strong></td></tr>
        <tr><td class="muted">Payment method</td><td>{{ ucfirst($order->payment_method) }}</td></tr>
        @if($order->delivery_instructions)<tr><td class="muted">Instructions</td><td>{{ $order->delivery_instructions }}</td></tr>@endif
        @if($order->gift_message)<tr><td class="muted">Card message</td><td>{{ $order->gift_message }}</td></tr>@endif
    </table>
    <p><a href="{{ route('admin.orders.show', $order) }}" class="btn">View order in admin</a></p>
</body>
</html>
