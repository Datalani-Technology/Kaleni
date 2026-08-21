<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact form – {{ $subject }}</title>
    <style>
        body { font-family: sans-serif; line-height: 1.5; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        h1 { font-size: 1.25rem; margin-bottom: 1rem; }
        .field { margin-bottom: 1rem; }
        .label { font-weight: 600; color: #555; }
        .value { margin-top: 0.25rem; }
        .message-box { background: #f5f5f5; padding: 1rem; border-radius: 6px; white-space: pre-wrap; }
        hr { border: none; border-top: 1px solid #ddd; margin: 1.5rem 0; }
        .footer { font-size: 0.85rem; color: #888; }
    </style>
</head>
<body>
    <h1>New message from Contact Us (namsa.com.na)</h1>
    <p>Someone submitted the contact form. Details below.</p>

    <div class="field">
        <div class="label">From (name)</div>
        <div class="value">{{ $name }}</div>
    </div>
    <div class="field">
        <div class="label">Email</div>
        <div class="value"><a href="mailto:{{ $email }}">{{ $email }}</a></div>
    </div>
    <div class="field">
        <div class="label">Subject</div>
        <div class="value">{{ $subject }}</div>
    </div>
    <div class="field">
        <div class="label">Message</div>
        <div class="value message-box">{{ $message }}</div>
    </div>

    <hr>
    <p class="footer">Reply directly to the sender using the email above. This was sent from your /Namsa Florals contact form.</p>
</body>
</html>
