<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>We received your special request</title>
    <style>
        body { font-family: sans-serif; line-height: 1.5; color: #29211F; max-width: 600px; margin: 0 auto; padding: 20px; }
        h1 { font-size: 1.25rem; margin-bottom: 1rem; }
        .muted { color: #666; font-size: 0.9rem; }
        .message-box { background: #f5f5f5; padding: 1rem; border-radius: 6px; white-space: pre-wrap; }
    </style>
</head>
<body>
    <h1>Thanks, {{ $specialRequest->name }}! 🍽️</h1>
    <p>We've received your special request and Chef K will follow up with a quote by email or phone shortly.</p>
    <p class="muted">What you asked for:</p>
    <div class="message-box">{{ $specialRequest->details }}</div>
    <p class="muted">Questions in the meantime? Just reply to this email or reach us on WhatsApp.</p>
</body>
</html>
