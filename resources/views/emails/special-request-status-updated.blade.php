<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>An update on your special request</title>
    <style>
        body { font-family: sans-serif; line-height: 1.5; color: #29211F; max-width: 600px; margin: 0 auto; padding: 20px; }
        h1 { font-size: 1.25rem; margin-bottom: 1rem; }
        .muted { color: #666; font-size: 0.9rem; }
        table { width: 100%; border-collapse: collapse; margin: 1rem 0; }
        td { padding: 6px 0; border-bottom: 1px solid #eee; }
    </style>
</head>
<body>
    <h1>Hi {{ $specialRequest->name }},</h1>

    @if($specialRequest->status === 'quoted')
        <p>Good news, Chef K has put together a quote for your special request.</p>
        <table>
            <tr><td class="muted">Reference</td><td>SR-{{ str_pad($specialRequest->id, 5, '0', STR_PAD_LEFT) }}</td></tr>
            @if($specialRequest->occasion)
                <tr><td class="muted">Occasion</td><td>{{ $specialRequest->occasion }}</td></tr>
            @endif
            @if($specialRequest->event_date)
                <tr>
                    <td class="muted">{{ $specialRequest->is_recurring ? 'Dates' : 'Event date' }}</td>
                    <td>
                        {{ $specialRequest->event_date->format('D, j M Y') }}
                        @if($specialRequest->is_recurring && $specialRequest->recurring_end_date)
                            &ndash; {{ $specialRequest->recurring_end_date->format('D, j M Y') }} (recurring)
                        @endif
                    </td>
                </tr>
            @endif
            @if($specialRequest->guest_count)
                <tr><td class="muted">Guests</td><td>{{ $specialRequest->guest_count }}</td></tr>
            @endif
            <tr><td class="muted">What you asked for</td><td>{{ $specialRequest->details }}</td></tr>
            @if($specialRequest->quoted_amount !== null)
                <tr><td class="muted">Quoted amount</td><td><strong>N$ {{ number_format((float) $specialRequest->quoted_amount, 2) }}</strong></td></tr>
            @endif
        </table>
        <p>This quote is an estimate and may be adjusted once final details are confirmed. Reply to this email or reach us on WhatsApp to accept or ask questions.</p>
    @elseif($specialRequest->status === 'accepted')
        <p>Your special request has been confirmed. We look forward to preparing it for you.</p>
    @else
        <p>Thanks for your interest. Unfortunately we're not able to take on this request at this time.</p>
    @endif

    @if($specialRequest->admin_notes)
        <p class="muted">Note from our team:</p>
        <p>{{ $specialRequest->admin_notes }}</p>
    @endif

    <p class="muted">Questions? Just reply to this email or reach us on WhatsApp.</p>
</body>
</html>
