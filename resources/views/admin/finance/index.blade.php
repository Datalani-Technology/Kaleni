@extends('admin.layout')

@section('title', 'Finance')
@section('sidebar_active', 'finance')

@section('content')
<h1 class="mb-1">Finance</h1>
<p class="text-muted mb-4" style="max-width: 640px;">
    <strong>Revenue</strong> below is money actually collected &mdash; a booking or order only counts once its payment is marked
    <em>completed</em>. Cancelled bookings are never counted, and confirmed-but-unpaid business is shown separately as
    <strong>awaiting payment</strong> so it's never hidden inside the revenue figure.
</p>

@if($awaitingBookingCount > 0 || $awaitingOrderCount > 0 || $failedPaymentCount > 0)
<div class="card mb-4 border-warning">
    <div class="card-body">
        <h5 class="card-title mb-3"><i class="bi bi-hourglass-split text-warning"></i> Awaiting payment</h5>
        <p class="text-muted small mb-3">Confirmed bookings and orders that haven't been marked as paid yet &mdash; not counted as revenue until they are.</p>
        <div class="d-flex flex-wrap gap-4">
            @if($awaitingBookingCount > 0)
                <div>
                    <div class="fw-bold fs-5">N$ {{ number_format($awaitingBookingTotal, 2) }}</div>
                    <a href="{{ route('admin.bookings.index', ['payment' => 'pending']) }}" class="small">{{ $awaitingBookingCount }} {{ \Illuminate\Support\Str::plural('booking', $awaitingBookingCount) }} →</a>
                </div>
            @endif
            @if($awaitingOrderCount > 0)
                <div>
                    <div class="fw-bold fs-5">N$ {{ number_format($awaitingOrderTotal, 2) }}</div>
                    <a href="{{ route('admin.orders.index', ['payment' => 'pending']) }}" class="small">{{ $awaitingOrderCount }} {{ \Illuminate\Support\Str::plural('order', $awaitingOrderCount) }} →</a>
                </div>
            @endif
            @if($failedPaymentCount > 0)
                <div>
                    <div class="fw-bold fs-5 text-danger">{{ $failedPaymentCount }}</div>
                    <span class="small text-muted">failed {{ \Illuminate\Support\Str::plural('payment', $failedPaymentCount) }} &mdash; needs follow-up</span>
                </div>
            @endif
        </div>

        <div class="table-responsive mt-3">
            <table class="table table-sm mb-0">
                <thead>
                    <tr class="text-muted small">
                        <th>Customer</th>
                        <th>Type</th>
                        <th>Reference</th>
                        <th>Placed</th>
                        <th class="text-end">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($awaitingList as $item)
                        <tr>
                            <td>{{ $item->customer_name }}</td>
                            <td>{{ $item->order_type === \App\Models\Booking::ORDER_TYPE_QUICK_ORDER ? 'Order' : 'Booking' }}</td>
                            <td><a href="{{ route('admin.bookings.show', $item->id) }}">{{ $item->booking_number }}</a></td>
                            <td>{{ $item->created_at->format('M j, Y') }}</td>
                            <td class="text-end">N$ {{ number_format((float) $item->total_amount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($awaitingTotalCount > count($awaitingList))
            <p class="small text-muted mt-2 mb-0">Showing {{ count($awaitingList) }} of {{ $awaitingTotalCount }} &mdash; see the links above for the full list.</p>
        @endif
    </div>
</div>
@endif

<h2 class="h5 text-muted mb-3"><i class="bi bi-calendar-month"></i> This month</h2>
<div class="row mb-4 g-3 admin-stat-cards">
    <div class="col-6 col-md-4">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h5 class="card-title text-white-50">Revenue</h5>
                <h2 class="mb-0">N$ {{ number_format($salesMonth, 2) }}</h2>
                <span class="small text-white-50">Payments received this month</span>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="card admin-stat-white">
            <div class="card-body">
                <h5 class="card-title text-muted">Expenses</h5>
                <h2 class="mb-0 text-primary">N$ {{ number_format($expensesMonth, 2) }}</h2>
                <a href="{{ route('admin.expenses.index') }}" class="small">View all →</a>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card h-100 border-primary">
            <div class="card-body">
                <h5 class="card-title text-muted">Net profit</h5>
                <h2 class="mb-0 {{ $netMonth >= 0 ? 'text-primary' : 'text-danger' }}">N$ {{ number_format($netMonth, 2) }}</h2>
                <span class="small text-muted">Revenue minus expenses</span>
            </div>
        </div>
    </div>
</div>

<h2 class="h5 text-muted mb-3"><i class="bi bi-graph-up"></i> All-time</h2>
<div class="row mb-4 g-3 admin-stat-cards">
    <div class="col-6 col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h5 class="card-title text-white-50">Revenue</h5>
                <h2 class="mb-0">N$ {{ number_format($salesAllTime, 2) }}</h2>
                <span class="small text-white-50">All payments received</span>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card admin-stat-white">
            <div class="card-body">
                <h5 class="card-title text-muted">Expenses</h5>
                <h2 class="mb-0 text-primary">N$ {{ number_format($expensesAllTime, 2) }}</h2>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100 border-primary">
            <div class="card-body">
                <h5 class="card-title text-muted">Net profit</h5>
                <h2 class="mb-0 {{ $netAllTime >= 0 ? 'text-primary' : 'text-danger' }}">N$ {{ number_format($netAllTime, 2) }}</h2>
                <a href="{{ route('admin.reports.index') }}" class="small">Full report →</a>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100 border-secondary">
            <div class="card-body">
                <h5 class="card-title text-muted">Avg. paid value</h5>
                <h2 class="mb-0 text-dark">N$ {{ number_format($avgBookingValue, 2) }}</h2>
                <span class="small text-muted">{{ $bookingCount }} paid {{ \Illuminate\Support\Str::plural('booking or order', $bookingCount) }}</span>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title mb-1">Revenue (last 30 days)</h5>
        <p class="text-muted small mb-3">Money collected per day &mdash; not the value of bookings placed.</p>
        <canvas id="revenueTrendChart" height="90"></canvas>
    </div>
</div>

@push('styles')
<style>
    @media (max-width: 575.98px) {
        .admin-stat-cards .card-body { padding: 0.75rem !important; }
        .admin-stat-cards .card-title { font-size: 0.8rem; margin-bottom: 0.25rem; }
        .admin-stat-cards h2 { font-size: 1.35rem; }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
    (function () {
        var ctx = document.getElementById('revenueTrendChart');
        if (!ctx || typeof Chart === 'undefined') return;
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($trendLabels) !!},
                datasets: [{
                    label: 'Revenue (N$)',
                    data: {!! json_encode($trendData) !!},
                    borderColor: '#680B1C',
                    backgroundColor: 'rgba(104, 11, 28, 0.1)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 0,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { callback: function (v) { return 'N$ ' + v; } } }
                }
            }
        });
    })();
</script>
@endpush
@endsection
