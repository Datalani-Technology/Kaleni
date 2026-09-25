<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $booking->booking_number }} - Kaleni Catering Services</title>
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.css') }}">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #29211F; padding: 40px; max-width: 800px; margin: 0 auto; }
        .invoice-header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 3px solid #680B1C; padding-bottom: 20px; margin-bottom: 30px; }
        .invoice-brand { display: flex; align-items: center; gap: 12px; }
        .invoice-brand img { height: 56px; width: 56px; object-fit: cover; border-radius: 50%; border: 2px solid #680B1C; }
        .invoice-header h1 { font-size: 1.6rem; margin: 0 0 4px; }
        .invoice-meta { text-align: right; }
        .invoice-meta .order-number { font-size: 1.3rem; font-weight: 700; color: #680B1C; }
        .invoice-section { margin-bottom: 24px; }
        .invoice-section h3 { font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; color: #888; margin-bottom: 8px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { padding: 10px 8px; border-bottom: 1px solid #e5e5e5; text-align: left; }
        th { background: #f8f8f8; font-size: 0.85rem; text-transform: uppercase; color: #666; }
        .text-end { text-align: right; }
        .totals { width: 260px; margin-left: auto; }
        .totals tr td { border: none; padding: 6px 8px; }
        .totals .grand-total td { border-top: 2px solid #29211F; font-weight: 700; font-size: 1.2rem; }
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
        $logoText = \App\Models\Setting::get('logo_text', 'Kaleni Catering Services');
        $isQuickOrder = $booking->order_type === \App\Models\Booking::ORDER_TYPE_QUICK_ORDER;
    @endphp
    <div class="invoice-header">
        <div class="invoice-brand">
            @if($logoPath)
                <img src="{{ asset('storage/' . $logoPath) }}" alt="{{ $logoText }}">
            @else
                <img src="{{ asset('images/kaleni/brand/kaleni-logo.jpg') }}" alt="{{ $logoText }}">
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
            <div class="order-number">{{ $booking->booking_number }}</div>
            <div class="text-muted">{{ $booking->created_at?->format('d M Y, H:i') }}</div>
        </div>
    </div>

    <div class="row invoice-section" style="display: flex; gap: 40px;">
        <div style="flex: 1;">
            <h3>Bill To</h3>
            <div><strong>{{ $booking->customer_name }}</strong></div>
            <div>{{ $booking->customer_email }}</div>
            <div>{{ $booking->customer_phone }}</div>
        </div>
        <div style="flex: 1;">
            @if($isQuickOrder)
                <h3>Fulfillment</h3>
                <div><strong>{{ ucfirst($booking->fulfillment_method ?: 'N/A') }}</strong></div>
                @if($booking->fulfillment_method === 'delivery')
                    <div>{{ $booking->event_address }}</div>
                @endif
            @else
                <h3>Event Details</h3>
                <div><strong>{{ $booking->onsite_contact_name ?: $booking->customer_name }}</strong></div>
                <div>{{ $booking->onsite_contact_phone ?: $booking->customer_phone }}</div>
                <div>{{ $booking->event_address }}</div>
                <div>{{ $booking->schedule_summary }} · {{ ucfirst(str_replace('_', ' ', $booking->serving_period ?: 'custom')) }} service</div>
                <div>{{ $booking->guest_count ? $booking->guest_count . ' guests' : '' }}</div>
            @endif
        </div>
        <div style="flex: 1;">
            <h3>Payment</h3>
            <div>Method: {{ $booking->payment_method_label }}</div>
            <div>Status: {{ ucfirst($booking->payment_status) }}</div>
            <div>{{ $isQuickOrder ? 'Order status' : 'Booking status' }}: {{ ucfirst($booking->booking_status) }}</div>
        </div>
    </div>

    @if($paymentAccountDetails = \App\Models\Setting::get('invoice_payment_account'))
        <div class="invoice-section" style="margin-top: 8px; padding: 14px 16px; background: #f8f8f8; border-radius: 8px;">
            <h3 style="margin-bottom: 4px;">Pay to</h3>
            <div style="white-space: pre-line;">{{ $paymentAccountDetails }}</div>
        </div>
    @endif

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
            @foreach($booking->items as $item)
                <tr>
                    <td>{{ $item->menuItem->name ?? 'Menu item #' . $item->menu_item_id }}</td>
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
            <td class="text-end">N$ {{ number_format($booking->total_amount, 2) }}</td>
        </tr>
    </table>

    <p class="text-muted" style="font-size: 0.85rem;">Thank you for {{ $isQuickOrder ? 'ordering from' : 'booking with' }} Kaleni Catering Services, home-style catering in Windhoek, Namibia.</p>

    <div class="no-print">
        <button onclick="window.print()" class="btn btn-dark" style="background:#222;color:#fff;border:none;padding:10px 20px;border-radius:6px;cursor:pointer;">
            <i class="bi bi-printer"></i> Print / Save as PDF
        </button>
    </div>
</body>
</html>
