<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation</title>
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
    <h1>Thank you, {{ $booking->customer_name }}! 🍽️</h1>
    <p>Your booking with Kaleni Catering Services has been received and is being prepared.</p>
    <table>
        <tr><td class="muted">Booking number</td><td><strong>{{ $booking->booking_number }}</strong></td></tr>
        <tr><td class="muted">Total</td><td><strong>N$ {{ number_format((float) $booking->total_amount, 2) }}</strong></td></tr>
        <tr><td class="muted">Payment method</td><td>{{ ucfirst($booking->payment_method) }}</td></tr>
        <tr><td class="muted">On-site contact</td><td>{{ $booking->onsite_contact_name ?: $booking->customer_name }}</td></tr>
        <tr><td class="muted">Schedule</td><td>{{ $booking->schedule_summary }} · {{ ucfirst(str_replace('_', ' ', $booking->serving_period ?: 'custom')) }} service</td></tr>
        <tr><td class="muted">Guests</td><td>{{ $booking->guest_count ?: 'N/A' }}</td></tr>
        <tr><td class="muted">Event address</td><td>{{ $booking->event_address }}</td></tr>
        @if($booking->special_message)<tr><td class="muted">Special message</td><td>{{ $booking->special_message }}</td></tr>@endif
    </table>
    <p><a href="{{ $receiptUrl }}" class="btn">View / print your receipt</a></p>
    <p class="muted">Questions about your booking? Just reply to this email or reach us on WhatsApp.</p>
</body>
</html>
