<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Low stock alert</title>
    <style>
        body { font-family: sans-serif; line-height: 1.5; color: #29211F; max-width: 600px; margin: 0 auto; padding: 20px; }
        h1 { font-size: 1.25rem; margin-bottom: 1rem; }
        .btn { display: inline-block; padding: 12px 24px; background: #680B1C; color: white !important; text-decoration: none; border-radius: 8px; margin: 1rem 0; }
        table { width: 100%; border-collapse: collapse; margin: 1rem 0; }
        th, td { padding: 8px; border-bottom: 1px solid #eee; text-align: left; }
        .muted { color: #666; font-size: 0.9rem; }
    </style>
</head>
<body>
    <h1>Low stock alert</h1>
    <p class="muted">The following menu items are at or below the low-stock threshold of {{ $threshold }} units:</p>
    <table>
        <tr><th>Menu item</th><th>Stock left</th></tr>
        @foreach($menuItems as $item)
            <tr><td>{{ $item->name }}</td><td>{{ $item->stock }}</td></tr>
        @endforeach
    </table>
    <p><a href="{{ route('admin.stock.index') }}" class="btn">View stock in admin</a></p>
</body>
</html>
