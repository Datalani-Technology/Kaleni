<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset your admin password</title>
    <style>
        body { font-family: sans-serif; line-height: 1.5; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        h1 { font-size: 1.25rem; margin-bottom: 1rem; }
        .btn { display: inline-block; padding: 12px 24px; background: #680B1C; color: white !important; text-decoration: none; border-radius: 8px; margin: 1rem 0; }
        .btn:hover { opacity: 0.9; }
        hr { border: none; border-top: 1px solid #ddd; margin: 1.5rem 0; }
        .footer { font-size: 0.85rem; color: #888; }
        .muted { color: #666; font-size: 0.9rem; }
    </style>
</head>
<body>
    <h1>Reset your Kaleni Catering Services admin password</h1>
    <p>You requested a password reset. Click the button below to set a new password. This link expires in 60 minutes.</p>
    <p><a href="{{ $url }}" class="btn">Reset password</a></p>
    <p class="muted">If the button doesn’t work, copy and paste this link into your browser:</p>
    <p class="muted" style="word-break: break-all;">{{ $url }}</p>
    <hr>
    <p class="footer">If you didn’t request this, you can ignore this email. Your password will stay the same.</p>
</body>
</html>
