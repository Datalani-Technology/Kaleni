<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales &amp; Expense Report {{ $from->format('d M Y') }} to {{ $to->format('d M Y') }} - Kaleni Catering Services</title>
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.css') }}">
    <style>
        @page { margin: 16mm 14mm; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #29211F; padding: 40px; max-width: 900px; margin: 0 auto; }
        .doc-header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 3px solid #680B1C; padding-bottom: 20px; margin-bottom: 24px; gap: 20px; }
        .doc-brand { display: flex; align-items: center; gap: 12px; }
        .doc-brand img { height: 52px; width: auto; object-fit: contain; }
        .doc-brand .bi-egg-fried { font-size: 2.4rem; color: #680B1C; }
        .doc-brand h1 { font-size: 1.4rem; margin: 0; }
        .doc-contact { text-align: right; font-size: 0.85rem; color: #555; line-height: 1.6; }
        .doc-title { margin-bottom: 24px; }
        .doc-title h2 { font-size: 1.3rem; margin: 0 0 4px; }
        .doc-title .muted { color: #777; font-size: 0.9rem; }
        .summary-row { display: flex; gap: 16px; margin-bottom: 32px; flex-wrap: wrap; }
        .summary-card { flex: 1; min-width: 160px; background: #f8f8f8; border-radius: 10px; padding: 16px 18px; border-left: 4px solid #ccc; }
        .summary-card.revenue { border-left-color: #198754; }
        .summary-card.expenses { border-left-color: #dc3545; }
        .summary-card.profit { border-left-color: #680B1C; }
        .summary-card .label { font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.04em; color: #888; margin-bottom: 4px; }
        .summary-card .value { font-size: 1.35rem; font-weight: 700; }
        h3.section-title { font-size: 1rem; text-transform: uppercase; letter-spacing: 0.04em; color: #444; border-bottom: 1px solid #ddd; padding-bottom: 6px; margin: 32px 0 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        th, td { padding: 8px; border-bottom: 1px solid #eee; text-align: left; font-size: 0.9rem; }
        th { background: #f8f8f8; font-size: 0.78rem; text-transform: uppercase; color: #666; }
        .text-end { text-align: right; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 20px; font-size: 0.75rem; }
        .badge-completed { background: #d1e7dd; color: #0f5132; }
        .badge-processing { background: #cfe2ff; color: #084298; }
        .badge-pending { background: #fff3cd; color: #664d03; }
        .badge-cancelled { background: #e2e3e5; color: #41464b; }
        .no-print { margin-top: 30px; }
        .doc-footer { margin-top: 40px; padding-top: 16px; border-top: 1px solid #eee; font-size: 0.8rem; color: #888; }
        @media print { .no-print { display: none; } body { padding: 0; } }
    </style>
</head>
<body>
    <div class="doc-header">
        <div class="doc-brand">
            @php
                $logoPath = \App\Models\Setting::get('logo_path');
                $logoText = \App\Models\Setting::get('logo_text', 'Kaleni Catering Services');
            @endphp
            @if($logoPath)
                <img src="{{ asset('storage/' . $logoPath) }}" alt="{{ $logoText }}">
            @else
                <i class="bi bi-egg-fried"></i>
            @endif
            <h1>{{ $logoText }}</h1>
        </div>
        <div class="doc-contact">
            <div>{{ config('contact.email_info') }}</div>
            <div>{{ config('contact.phone') }}</div>
            <div>{{ config('contact.address.city') }}, {{ config('contact.address.country') }}</div>
        </div>
    </div>

    <div class="doc-title">
        <h2>Sales &amp; Expense Report</h2>
        <div class="muted">{{ $from->format('d M Y') }} &ndash; {{ $to->format('d M Y') }} &middot; Generated {{ now()->format('d M Y, H:i') }}</div>
    </div>

    <div class="summary-row">
        <div class="summary-card revenue">
            <div class="label">Revenue ({{ $bookingCount }} bookings)</div>
            <div class="value">N$ {{ number_format($revenue, 2) }}</div>
        </div>
        <div class="summary-card expenses">
            <div class="label">Expenses</div>
            <div class="value">N$ {{ number_format($totalExpenses, 2) }}</div>
        </div>
        <div class="summary-card profit">
            <div class="label">Net Profit</div>
            <div class="value">N$ {{ number_format($netProfit, 2) }}</div>
        </div>
        <div class="summary-card">
            <div class="label">Avg. Booking Value</div>
            <div class="value">N$ {{ number_format($avgBookingValue, 2) }}</div>
        </div>
    </div>

    <h3 class="section-title">Bookings</h3>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Booking #</th>
                <th>Customer</th>
                <th>Payment</th>
                <th>Status</th>
                <th class="text-end">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bookings as $b)
                <tr>
                    <td>{{ $b->created_at->format('d M Y') }}</td>
                    <td>{{ $b->booking_number }}</td>
                    <td>{{ $b->customer_name }}</td>
                    <td>{{ ucfirst($b->payment_method) }}</td>
                    <td><span class="badge badge-{{ $b->booking_status }}">{{ ucfirst($b->booking_status) }}</span></td>
                    <td class="text-end">N$ {{ number_format((float) $b->total_amount, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-3">No bookings in this period.</td></tr>
            @endforelse
        </tbody>
        @if($bookings->isNotEmpty())
        <tfoot>
            <tr>
                <td colspan="5" class="text-end"><strong>Total Revenue</strong></td>
                <td class="text-end"><strong>N$ {{ number_format($revenue, 2) }}</strong></td>
            </tr>
        </tfoot>
        @endif
    </table>

    <h3 class="section-title">Expenses</h3>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Category</th>
                <th>Description</th>
                <th class="text-end">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($expenses as $e)
                <tr>
                    <td>{{ $e->spent_at->format('d M Y') }}</td>
                    <td>{{ $e->category }}</td>
                    <td>{{ $e->description ?: 'N/A' }}</td>
                    <td class="text-end">N$ {{ number_format((float) $e->amount, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center text-muted py-3">No expenses in this period.</td></tr>
            @endforelse
        </tbody>
        @if($expenses->isNotEmpty())
        <tfoot>
            <tr>
                <td colspan="3" class="text-end"><strong>Total Expenses</strong></td>
                <td class="text-end"><strong>N$ {{ number_format($totalExpenses, 2) }}</strong></td>
            </tr>
        </tfoot>
        @endif
    </table>

    <div class="doc-footer">
        This report was generated from live booking and expense records for Kaleni Catering Services. Cancelled bookings are excluded from revenue.
    </div>

    <div class="no-print">
        <button onclick="window.print()" style="background:#222;color:#fff;border:none;padding:10px 20px;border-radius:6px;cursor:pointer;">
            <i class="bi bi-printer"></i> Print / Save as PDF
        </button>
    </div>
</body>
</html>
