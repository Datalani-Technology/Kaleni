<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $order->order_number }} - /Namsa Florals</title>
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.css') }}">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #222; padding: 40px; max-width: 800px; margin: 0 auto; }
        .invoice-header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 3px solid #d63384; padding-bottom: 20px; margin-bottom: 30px; }
        .invoice-brand { display: flex; align-items: center; gap: 12px; }
        .invoice-brand img { height: 48px; width: auto; object-fit: contain; }
        .invoice-brand .bi-flower1 { font-size: 2.2rem; color: #d63384; }
        .invoice-header h1 { font-size: 1.6rem; margin: 0 0 4px; }
        .invoice-meta { text-align: right; }
        .invoice-meta .order-number { font-size: 1.3rem; font-weight: 700; color: #d63384; }
        .invoice-section { margin-bottom: 24px; }
        .invoice-section h3 { font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; color: #888; margin-bottom: 8px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { padding: 10px 8px; border-bottom: 1px solid #e5e5e5; text-align: left; }
        th { background: #f8f8f8; font-size: 0.85rem; text-transform: uppercase; color: #666; }
        .text-end { text-align: right; }
        .totals { width: 260px; margin-left: auto; }
        .totals tr td { border: none; padding: 6px 8px; }
        .totals .grand-total td { border-top: 2px solid #222; font-weight: 700; font-size: 1.2rem; }
        .no-print { margin-top: 30px; }
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    @php
        $logoPath = \App\Models\Setting::get('logo_path');
        $logoText = \App\Models\Setting::get('logo_text', '/Namsa Florals');
    @endphp
    <div class="invoice-header">
        <div class="invoice-brand">
            @if($logoPath)
                <img src="{{ asset('storage/' . $logoPath) }}" alt="{{ $logoText }}">
            @else
                <i class="bi bi-flower1"></i>
            @endif
            <div>
                <h1>{{ $logoText }}</h1>
                <div class="text-muted" style="font-size: 0.85rem;">
                    {{ config('contact.email_info') }} &middot; {{ config('contact.phone') }}<br>
                    {{ config('contact.address.city') }}, {{ config('contact.address.country') }}
                </div>
            </div>
        </div>
        <div class="invoice-meta">
            <div class="order-number">{{ $order->order_number }}</div>
            <div class="text-muted">{{ $order->created_at?->format('d M Y, H:i') }}</div>
        </div>
    </div>

    <div class="row invoice-section" style="display: flex; gap: 40px;">
        <div style="flex: 1;">
            <h3>Bill To</h3>
            <div><strong>{{ $order->customer_name }}</strong></div>
            <div>{{ $order->customer_email }}</div>
            <div>{{ $order->customer_phone }}</div>
        </div>
        <div style="flex: 1;">
            <h3>Deliver To</h3>
            <div><strong>{{ $order->recipient_name ?: $order->customer_name }}</strong></div>
            <div>{{ $order->recipient_phone ?: $order->customer_phone }}</div>
            <div>{{ $order->delivery_address }}</div>
            <div>{{ $order->delivery_date?->format('d M Y') ?: 'Date to be confirmed' }} · {{ ucfirst($order->delivery_window ?: 'anytime') }}</div>
        </div>
        <div style="flex: 1;">
            <h3>Payment</h3>
            <div>Method: {{ ucfirst($order->payment_method) }}</div>
            <div>Status: {{ ucfirst($order->payment_status) }}</div>
            <div>Order status: {{ ucfirst($order->order_status) }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th class="text-end">Qty</th>
                <th class="text-end">Price</th>
                <th class="text-end">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->product->name ?? 'Product #' . $item->product_id }}</td>
                    <td class="text-end">{{ $item->quantity }}</td>
                    <td class="text-end">N$ {{ number_format($item->price, 2) }}</td>
                    <td class="text-end">N$ {{ number_format($item->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr class="grand-total">
            <td>Total</td>
            <td class="text-end">N$ {{ number_format($order->total_amount, 2) }}</td>
        </tr>
    </table>

    <p class="text-muted" style="font-size: 0.85rem;">Thank you for ordering with /Namsa Florals — Namibia's online flower shop.</p>

    <div class="no-print">
        <button onclick="window.print()" class="btn btn-dark" style="background:#222;color:#fff;border:none;padding:10px 20px;border-radius:6px;cursor:pointer;">
            <i class="bi bi-printer"></i> Print / Save as PDF
        </button>
    </div>
</body>
</html>
