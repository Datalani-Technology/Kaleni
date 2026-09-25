<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quote SR-{{ str_pad($specialRequest->id, 5, '0', STR_PAD_LEFT) }} - Kaleni Catering Services</title>
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
        .quote-status { display: inline-block; padding: 4px 12px; border-radius: 999px; font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.03em; background: #F7ECD3; color: #4A0814; }
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
            <div class="order-number">Quote SR-{{ str_pad($specialRequest->id, 5, '0', STR_PAD_LEFT) }}</div>
            <div class="text-muted">{{ $specialRequest->created_at->format('d M Y, H:i') }}</div>
            <div style="margin-top: 6px;"><span class="quote-status">{{ ucfirst(str_replace('_', ' ', $specialRequest->status)) }}</span></div>
        </div>
    </div>

    <div class="row invoice-section" style="display: flex; gap: 40px;">
        <div style="flex: 1;">
            <h3>Quote For</h3>
            <div><strong>{{ $specialRequest->name }}</strong></div>
            <div>{{ $specialRequest->email }}</div>
            <div>{{ $specialRequest->phone }}</div>
        </div>
        <div style="flex: 1;">
            <h3>Event Details</h3>
            <div>{{ $specialRequest->occasion ?: 'Occasion not specified' }}</div>
            <div>
                {{ optional($specialRequest->event_date)->format('D, j M Y') ?: 'Date not specified' }}
                @if($specialRequest->is_recurring && $specialRequest->recurring_end_date)
                    &ndash; {{ $specialRequest->recurring_end_date->format('D, j M Y') }} (recurring)
                @endif
            </div>
            <div>{{ $specialRequest->guest_count ? $specialRequest->guest_count . ' guests' : 'Guest count not specified' }}</div>
        </div>
    </div>

    <div class="invoice-section">
        <h3>Request Details</h3>
        <p style="white-space: pre-line; margin: 0;">{{ $specialRequest->details }}</p>
    </div>

    @if($specialRequest->quoted_amount !== null)
        <table class="totals">
            <tr class="grand-total">
                <td>Quoted amount</td>
                <td class="text-end">N$ {{ number_format((float) $specialRequest->quoted_amount, 2) }}</td>
            </tr>
        </table>
    @else
        <p class="text-muted" style="font-size: 0.85rem;">No amount has been quoted yet.</p>
    @endif

    <p class="text-muted" style="font-size: 0.85rem;">Thank you for considering Kaleni Catering Services, home-style catering in Windhoek, Namibia. This quote is provided as an estimate and may be adjusted once final details are confirmed.</p>

    <div class="no-print">
        <button onclick="window.print()" class="btn btn-dark" style="background:#222;color:#fff;border:none;padding:10px 20px;border-radius:6px;cursor:pointer;">
            <i class="bi bi-printer"></i> Print / Save as PDF
        </button>
    </div>
</body>
</html>
