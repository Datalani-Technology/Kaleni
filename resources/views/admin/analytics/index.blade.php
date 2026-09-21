@extends('admin.layout')

@section('title', 'Analytics')
@section('sidebar_active', 'analytics')

@push('styles')
<style>
    .analytics-stat { border: 0; border-radius: 12px; }
    .analytics-stat .stat-icon {
        width: 44px; height: 44px; border-radius: 10px;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 1.25rem; margin-bottom: 10px;
    }
    .analytics-stat.tone-pink .stat-icon { background: rgba(104, 11, 28, .12); color: #680B1C; }
    .analytics-stat.tone-green .stat-icon { background: rgba(25, 135, 84, .12); color: #198754; }
    .analytics-stat.tone-blue .stat-icon { background: rgba(13, 110, 253, .12); color: #0d6efd; }
    .analytics-stat.tone-amber .stat-icon { background: rgba(248, 173, 39, .18); color: #a8710a; }
    .status-badge-row .badge { font-weight: 600; }
    @media (max-width: 575.98px) {
        .analytics-period-form { width: 100%; }
        .analytics-period-form .form-select { flex: 1; min-width: 0; }
    }
</style>
@endpush

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <h1 class="mb-0"><i class="bi bi-graph-up" style="color: #680B1C;"></i> Analytics</h1>
    <form action="{{ route('admin.analytics.index') }}" method="GET" class="d-flex gap-2 analytics-period-form">
        <select name="period" class="form-select form-select-sm" style="min-width: 140px;" onchange="this.form.submit()">
            <option value="today" {{ $period === 'today' ? 'selected' : '' }}>Today</option>
            <option value="7d" {{ $period === '7d' ? 'selected' : '' }}>Last 7 days</option>
            <option value="30d" {{ $period === '30d' ? 'selected' : '' }}>Last 30 days</option>
            <option value="90d" {{ $period === '90d' ? 'selected' : '' }}>Last 90 days</option>
        </select>
    </form>
</div>

<h2 class="h6 text-uppercase text-muted mb-3" style="letter-spacing: .04em;">Sales</h2>
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card analytics-stat tone-pink h-100">
            <div class="card-body">
                <div class="stat-icon"><i class="bi bi-cash-coin"></i></div>
                <div class="text-muted small">Revenue</div>
                <div class="h4 mb-0">N$ {{ number_format($revenue, 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card analytics-stat tone-blue h-100">
            <div class="card-body">
                <div class="stat-icon"><i class="bi bi-cart-check"></i></div>
                <div class="text-muted small">Bookings</div>
                <div class="h4 mb-0">{{ number_format($bookingCount) }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card analytics-stat tone-green h-100">
            <div class="card-body">
                <div class="stat-icon"><i class="bi bi-receipt"></i></div>
                <div class="text-muted small">Avg. booking value</div>
                <div class="h4 mb-0">N$ {{ number_format($avgBookingValue, 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card analytics-stat tone-amber h-100">
            <div class="card-body">
                <div class="stat-icon"><i class="bi bi-signpost-split"></i></div>
                <div class="text-muted small">Visit → booking rate</div>
                <div class="h4 mb-0">{{ number_format($conversionRate, 1) }}%</div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title mb-3">Revenue trend</h5>
        <canvas id="analyticsRevenueChart" height="90"></canvas>
    </div>
</div>

<div class="row mb-4 g-3">
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header"><strong>Bookings by status</strong></div>
            <div class="card-body d-flex flex-wrap gap-2 status-badge-row">
                @forelse(['pending' => 'warning text-dark', 'processing' => 'info', 'completed' => 'success', 'cancelled' => 'secondary'] as $status => $badgeClass)
                    @php $count = $bookingsByStatus[$status] ?? 0; @endphp
                    @if($count > 0)
                        <span class="badge bg-{{ $badgeClass }} py-2 px-3">{{ ucfirst($status) }}: {{ $count }}</span>
                    @endif
                @empty
                @endforelse
                @if($bookingsByStatus->sum() === 0)
                    <span class="text-muted small">No bookings in this period.</span>
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header"><strong>Payment method</strong></div>
            <div class="card-body">
                @forelse($bookingsByPaymentMethod as $method => $count)
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span><i class="bi {{ $method === 'dpo' ? 'bi-credit-card' : 'bi-whatsapp' }}"></i> {{ $method === 'dpo' ? 'DPO (card)' : 'WhatsApp' }}</span>
                        <strong>{{ $count }}</strong>
                    </div>
                @empty
                    <span class="text-muted small">No bookings in this period.</span>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header"><strong>Site traffic</strong></div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span><i class="bi bi-eye"></i> Page views</span>
                    <strong>{{ number_format($totalViews) }}</strong>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-people"></i> Unique visitors</span>
                    <strong>{{ number_format($uniqueVisitors) }}</strong>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header"><strong>Top-selling menu items</strong></div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>Menu item</th><th class="text-end">Units sold</th><th class="text-end">Revenue</th></tr></thead>
                <tbody>
                    @forelse($topMenuItems as $row)
                        <tr>
                            <td>
                                @if($row->menuItem)
                                    @include('admin.partials.menu-item-image', ['menuItem' => $row->menuItem, 'size' => 36])
                                    {{ $row->menuItem->name }}
                                @else
                                    <span class="text-muted">Deleted item</span>
                                @endif
                            </td>
                            <td class="text-end">{{ number_format($row->units) }}</td>
                            <td class="text-end">N$ {{ number_format($row->revenue, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-muted">No sales in this period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header"><strong>Views by page type</strong></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>Page</th><th class="text-end">Views</th></tr></thead>
                        <tbody>
                            @forelse($byPage as $r)
                                <tr><td>{{ $r->page_type ?: 'other' }}</td><td class="text-end">{{ number_format($r->hits) }}</td></tr>
                            @empty
                                <tr><td colspan="2" class="text-muted">No data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header"><strong>Top pages</strong></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>Path</th><th class="text-end">Views</th></tr></thead>
                        <tbody>
                            @forelse($topPaths as $r)
                                <tr><td><code class="small">{{ $r->path }}</code></td><td class="text-end">{{ number_format($r->hits) }}</td></tr>
                            @empty
                                <tr><td colspan="2" class="text-muted">No data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
    (function () {
        var ctx = document.getElementById('analyticsRevenueChart');
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
