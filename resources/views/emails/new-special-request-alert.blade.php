<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New special request from {{ $specialRequest->name }}</title>
    <style>
        body { font-family: sans-serif; line-height: 1.5; color: #29211F; max-width: 600px; margin: 0 auto; padding: 20px; }
        h1 { font-size: 1.25rem; margin-bottom: 1rem; }
        .btn { display: inline-block; padding: 12px 24px; background: #680B1C; color: white !important; text-decoration: none; border-radius: 8px; margin: 1rem 0; }
        table { width: 100%; border-collapse: collapse; margin: 1rem 0; }
        td { padding: 6px 0; border-bottom: 1px solid #eee; vertical-align: top; }
        .muted { color: #666; font-size: 0.9rem; }
        .message-box { background: #f5f5f5; padding: 1rem; border-radius: 6px; white-space: pre-wrap; }
    </style>
</head>
<body>
    <h1>New special item request</h1>
    <table>
        <tr><td class="muted">Name</td><td>{{ $specialRequest->name }}</td></tr>
        <tr><td class="muted">Email</td><td>{{ $specialRequest->email }}</td></tr>
        <tr><td class="muted">Phone</td><td>{{ $specialRequest->phone }}</td></tr>
        <tr><td class="muted">Event date</td><td>{{ $specialRequest->event_date?->format('D, j M Y') ?: 'Not specified' }}</td></tr>
        <tr><td class="muted">Guests</td><td>{{ $specialRequest->guest_count ?: 'Not specified' }}</td></tr>
        <tr><td class="muted">Occasion</td><td>{{ $specialRequest->occasion ?: 'Not specified' }}</td></tr>
        <tr><td class="muted">Budget</td><td>{{ $specialRequest->budget_range ?: 'Not specified' }}</td></tr>
    </table>
    <p class="muted">Details:</p>
    <div class="message-box">{{ $specialRequest->details }}</div>
    <p><a href="{{ route('admin.special-requests.show', $specialRequest) }}" class="btn">View request in admin</a></p>
</body>
</html>
