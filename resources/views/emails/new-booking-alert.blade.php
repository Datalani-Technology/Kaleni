<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New {{ $booking->order_type === \App\Models\Booking::ORDER_TYPE_QUICK_ORDER ? 'order' : 'booking' }} {{ $booking->booking_number }}</title>
    <style>
        body { font-family: sans-serif; line-height: 1.5; color: #29211F; max-width: 600px; margin: 0 auto; padding: 20px; }
        h1 { font-size: 1.25rem; margin-bottom: 1rem; }
        .btn { display: inline-block; padding: 12px 24px; background: #680B1C; color: white !important; text-decoration: none; border-radius: 8px; margin: 1rem 0; }
        table { width: 100%; border-collapse: collapse; margin: 1rem 0; }
        td { padding: 6px 0; border-bottom: 1px solid #eee; }
        .muted { color: #666; font-size: 0.9rem; }
    </style>
</head>
<body>
    @php $isQuickOrder = $booking->order_type === \App\Models\Booking::ORDER_TYPE_QUICK_ORDER; @endphp
    <h1>New {{ $isQuickOrder ? 'order' : 'booking' }}: {{ $booking->booking_number }}</h1>
    <table>
        <tr><td class="muted">Customer</td><td>{{ $booking->customer_name }}</td></tr>
        <tr><td class="muted">Email</td><td>{{ $booking->customer_email }}</td></tr>
        <tr><td class="muted">Phone</td><td>{{ $booking->customer_phone }}</td></tr>
        @if($isQuickOrder)
            <tr><td class="muted">Fulfillment</td><td>{{ ucfirst($booking->fulfillment_method ?: 'N/A') }}</td></tr>
            @if($booking->fulfillment_method === 'delivery')
                <tr><td class="muted">Delivery address</td><td>{{ $booking->event_address }}</td></tr>
            @endif
        @else
            <tr><td class="muted">On-site contact</td><td>{{ $booking->onsite_contact_name ?: $booking->customer_name }} · {{ $booking->onsite_contact_phone ?: $booking->customer_phone }}</td></tr>
            <tr><td class="muted">Schedule</td><td>{{ $booking->schedule_summary }} · {{ ucfirst(str_replace('_', ' ', $booking->serving_period ?: 'custom')) }} · {{ $booking->event_type ?: 'N/A' }}</td></tr>
            <tr><td class="muted">Guests</td><td>{{ $booking->guest_count ?: 'N/A' }}</td></tr>
            <tr><td class="muted">Address</td><td>{{ $booking->event_address }}</td></tr>
        @endif
        <tr><td class="muted">Total</td><td><strong>N$ {{ number_format((float) $booking->total_amount, 2) }}</strong></td></tr>
        <tr><td class="muted">Payment method</td><td>{{ $booking->payment_method_label }}</td></tr>
        @if($booking->event_notes)<tr><td class="muted">Notes</td><td>{{ $booking->event_notes }}</td></tr>@endif
        @if($booking->special_message)<tr><td class="muted">Special message</td><td>{{ $booking->special_message }}</td></tr>@endif
    </table>
    <p><a href="{{ route('admin.bookings.show', $booking) }}" class="btn">View {{ $isQuickOrder ? 'order' : 'booking' }} in admin</a></p>
</body>
</html>
