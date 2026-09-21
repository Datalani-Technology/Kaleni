@extends('admin.layout')

@section('title', 'Dashboard')
@section('sidebar_active', 'dashboard')

@php
    $now = \Carbon\Carbon::now();
    $baseSales = fn ($from, $to) => (float) \App\Models\Booking::where('booking_status', '!=', 'cancelled')
        ->whereBetween('created_at', [$from, $to])->sum('total_amount');
    $baseExpenses = fn ($from, $to) => (float) \App\Models\Expense::whereBetween('spent_at', [$from->toDateString(), $to->toDateString()])->sum('amount');

    $salesToday = $baseSales($now->copy()->startOfDay(), $now->copy()->endOfDay());
    $salesWeek = $baseSales($now->copy()->startOfWeek(), $now->copy()->endOfWeek());
    $salesMonth = $baseSales($now->copy()->startOfMonth(), $now->copy()->endOfMonth());
    $salesAllTime = (float) \App\Models\Booking::where('booking_status', '!=', 'cancelled')->sum('total_amount');

    $expensesToday = $baseExpenses($now->copy()->startOfDay(), $now->copy()->endOfDay());
    $expensesWeek = $baseExpenses($now->copy()->startOfWeek(), $now->copy()->endOfWeek());
    $expensesMonth = $baseExpenses($now->copy()->startOfMonth(), $now->copy()->endOfMonth());
    $expensesAllTime = (float) \App\Models\Expense::sum('amount');

    $bookingCount = \App\Models\Booking::where('booking_status', '!=', 'cancelled')->count();
    $avgBookingValue = $salesAllTime / max(1, $bookingCount);

    $trendRaw = \App\Models\Booking::where('booking_status', '!=', 'cancelled')
        ->where('created_at', '>=', $now->copy()->subDays(29)->startOfDay())
        ->selectRaw('DATE(created_at) as d, SUM(total_amount) as total')
        ->groupBy('d')
        ->pluck('total', 'd');
    $trendLabels = [];
    $trendData = [];
    for ($i = 29; $i >= 0; $i--) {
        $d = $now->copy()->subDays($i);
        $trendLabels[] = $d->format('M j');
        $trendData[] = round((float) ($trendRaw[$d->toDateString()] ?? 0), 2);
    }
@endphp

@section('content')
<h1 class="mb-4">Admin Dashboard</h1>

<h2 class="h5 text-muted mb-3"><i class="bi bi-cash-coin"></i> Earnings</h2>
<div class="row mb-3 g-3">
    <div class="col-6 col-md-3">
        <div class="card h-100 border-0" style="background: #eef7f0;">
            <div class="card-body">
                <div class="text-muted small">Today</div>
                <div class="h4 mb-0 text-success">N$ {{ number_format($salesToday, 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100 border-0" style="background: #eef7f0;">
            <div class="card-body">
                <div class="text-muted small">This week</div>
                <div class="h4 mb-0 text-success">N$ {{ number_format($salesWeek, 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100 border-0" style="background: #eef7f0;">
            <div class="card-body">
                <div class="text-muted small">This month</div>
                <div class="h4 mb-0 text-success">N$ {{ number_format($salesMonth, 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100 border-0" style="background: #eef7f0;">
            <div class="card-body">
                <div class="text-muted small">All-time revenue</div>
                <div class="h4 mb-0 text-success">N$ {{ number_format($salesAllTime, 2) }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4 g-3">
    <div class="col-md-4">
        <div class="card h-100 border-danger">
            <div class="card-body">
                <h5 class="card-title text-muted">Total expenses (all-time)</h5>
                <p class="mb-0 small text-muted">Money out · <a href="{{ route('admin.expenses.index') }}">view all</a></p>
                <h2 class="text-danger mt-1">N$ {{ number_format($expensesAllTime, 2) }}</h2>
                <a href="{{ route('admin.expenses.create') }}" class="btn btn-sm btn-outline-danger mt-2">Log an expense</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 border-primary">
            <div class="card-body">
                <h5 class="card-title text-muted">Net profit (all-time)</h5>
                <p class="mb-0 small text-muted">Revenue &minus; expenses</p>
                @php $netAllTime = $salesAllTime - $expensesAllTime; @endphp
                <h2 class="mt-1 {{ $netAllTime >= 0 ? 'text-primary' : 'text-danger' }}">N$ {{ number_format($netAllTime, 2) }}</h2>
                <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-outline-primary mt-2">Full report</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 border-secondary">
            <div class="card-body">
                <h5 class="card-title text-muted">Average booking value</h5>
                <p class="mb-0 small text-muted">{{ $bookingCount }} completed/active {{ \Illuminate\Support\Str::plural('booking', $bookingCount) }}</p>
                <h2 class="text-dark mt-1">N$ {{ number_format($avgBookingValue, 2) }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title mb-3">Revenue (last 30 days)</h5>
        <canvas id="revenueTrendChart" height="90"></canvas>
    </div>
</div>

<h2 class="h5 text-muted mb-3"><i class="bi bi-inboxes"></i> Client Requests</h2>
<div class="row mb-4 g-3 admin-stat-cards">
    <div class="col-6 col-md-3">
        <a href="{{ route('admin.bookings.index') }}" class="text-decoration-none">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title text-white-50">Total Bookings</h5>
                    <h2 class="mb-0">{{ \App\Models\Booking::count() }}</h2>
                    <small class="opacity-75">View all →</small>
                </div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('admin.bookings.index', ['status' => 'pending']) }}" class="text-decoration-none">
            <div class="card admin-stat-white">
                <div class="card-body">
                    <h5 class="card-title text-muted">Pending Bookings</h5>
                    <h2 class="mb-0 text-primary">{{ \App\Models\Booking::where('booking_status', 'pending')->count() }}</h2>
                    <small class="text-muted">View bookings →</small>
                </div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('admin.special-requests.index') }}" class="text-decoration-none">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title text-white-50">Total Special Requests</h5>
                    <h2 class="mb-0">{{ \App\Models\SpecialRequest::count() }}</h2>
                    <small class="opacity-75">View all →</small>
                </div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        @php $newSpecialRequestCount = \App\Models\SpecialRequest::where('status', 'new')->count(); @endphp
        <a href="{{ route('admin.special-requests.index', ['status' => 'new']) }}" class="text-decoration-none">
            <div class="card admin-stat-white">
                <div class="card-body">
                    <h5 class="card-title text-muted">New Special Requests</h5>
                    <h2 class="mb-0 {{ $newSpecialRequestCount > 0 ? 'text-primary' : 'text-muted' }}">{{ $newSpecialRequestCount }}</h2>
                    <small class="text-muted">Needs a response →</small>
                </div>
            </div>
        </a>
    </div>
</div>

<h2 class="h5 text-muted mb-3"><i class="bi bi-box-seam"></i> Catalog &amp; Inventory</h2>
<div class="row mb-4 g-3 admin-stat-cards">
    <div class="col-6 col-md-6">
        <a href="{{ route('admin.menu-items.index') }}" class="text-decoration-none">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title text-white-50">Total Menu Items</h5>
                    <h2 class="mb-0">{{ \App\Models\MenuItem::count() }}</h2>
                    <small class="opacity-75">View all →</small>
                </div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-6">
        @php
            $lowThreshold = config('inventory.low_stock_threshold', 5);
            $lowCount = \App\Models\MenuItem::where('stock', '<=', $lowThreshold)->count();
        @endphp
        <a href="{{ route('admin.stock.index') }}" class="text-decoration-none">
            <div class="card admin-stat-white">
                <div class="card-body">
                    <h5 class="card-title text-muted">Low Stock (≤{{ $lowThreshold }})</h5>
                    <h2 class="mb-0 {{ $lowCount > 0 ? 'text-danger' : 'text-muted' }}">{{ $lowCount }}</h2>
                    <small class="text-muted">View stock count →</small>
                </div>
            </div>
        </a>
    </div>
</div>
@push('styles')
<style>
    @media (max-width: 575.98px) {
        .admin-stat-cards .card-body { padding: 0.75rem !important; }
        .admin-stat-cards .card-title { font-size: 0.8rem; margin-bottom: 0.25rem; }
        .admin-stat-cards h2 { font-size: 1.35rem; }
        .admin-stat-cards small { font-size: 0.7rem; }
        .admin-quick-actions .btn { flex: 1 1 100%; }
    }
</style>
@endpush
@php
    $unreadEnquiries = \App\Models\Contact::where('is_read', false)->count();
@endphp
@if($unreadEnquiries > 0)
<div class="row mb-4 g-3">
    <div class="col-12">
        <a href="{{ route('admin.contacts.index', ['unread' => 1]) }}" class="text-decoration-none d-block" style="min-height: 48px; -webkit-tap-highlight-color: transparent;">
            <div class="card border-primary bg-primary bg-opacity-10">
                <div class="card-body py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <span><i class="bi bi-envelope-fill text-primary"></i> <strong>{{ $unreadEnquiries }}</strong> unread {{ $unreadEnquiries === 1 ? 'enquiry' : 'enquiries' }}</span>
                    <span class="text-primary">View →</span>
                </div>
            </div>
        </a>
    </div>
</div>
@endif
@php
    $potentialFromStock = \App\Models\MenuItem::get()->sum(fn ($p) => $p->stock * (float) $p->price);
@endphp
<div class="row mb-4 g-3">
    <div class="col-md-12">
        <div class="card border-secondary">
            <div class="card-body">
                <h5 class="card-title text-muted">Potential from current stock</h5>
                <p class="mb-0 small text-muted">Inventory value if all current stock were sold at listed prices (not earnings)</p>
                <h2 class="text-secondary mt-1">N$ {{ number_format($potentialFromStock, 2) }}</h2>
                <a href="{{ route('admin.stock.index') }}" class="btn btn-sm btn-outline-secondary mt-2">View stock</a>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title">Quick Actions</h5>
        <div class="d-flex flex-wrap gap-2 admin-quick-actions">
            <a href="{{ route('admin.bookings.index') }}" class="btn btn-primary">
                <i class="bi bi-cart-check"></i> View All Bookings
            </a>
            <a href="{{ route('admin.menu-items.create') }}" class="btn btn-outline-primary">
                <i class="bi bi-plus-circle"></i> Add New Menu Item
            </a>
            <a href="{{ route('admin.food-of-the-day.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-stars"></i> Food of the Day
            </a>
            <a href="{{ route('admin.special-requests.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-heart"></i> Special Requests
            </a>
            <a href="{{ route('admin.stock.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-box-seam"></i> Stock Count & Inventory
            </a>
            <a href="{{ route('admin.expenses.create') }}" class="btn btn-outline-primary">
                <i class="bi bi-cash-stack"></i> Log Expense
            </a>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-file-earmark-bar-graph"></i> Reports
            </a>
            <a href="{{ route('admin.analytics.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-graph-up"></i> Analytics
            </a>
            <a href="{{ route('admin.contacts.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-envelope"></i> Enquiries
            </a>
            @if(auth()->user()->role === 'admin')
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary">
                    <i class="bi bi-people"></i> Manage Users
                </a>
            @endif
        </div>
    </div>
</div>

@php
    $recentBookings = \App\Models\Booking::with('items.menuItem')->latest()->take(8)->get();
@endphp
@if($recentBookings->isNotEmpty())
<div class="card mb-4">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Recent bookings</h5>
        <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-outline-primary">View all</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 admin-table-cards">
                <thead>
                    <tr>
                        <th>Booking</th>
                        <th>Customer</th>
                        <th>Cart</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentBookings as $b)
                        <tr>
                            <td data-label="Booking"><strong>{{ $b->booking_number }}</strong></td>
                            <td data-label="Customer">{{ $b->customer_name }}</td>
                            <td data-label="Cart">
                                <span class="badge bg-secondary">{{ $b->items->count() }} {{ Str::plural('item', $b->items->count()) }}</span>
                                <small class="d-block text-muted">{{ Str::limit($b->items->map(fn ($i) => optional($i->menuItem)->name ?? 'Deleted item')->implode(', '), 40) }}</small>
                            </td>
                            <td data-label="Total">N$ {{ number_format((float) $b->total_amount, 2) }}</td>
                            <td data-label="Status">
                                @if($b->booking_status === 'completed')
                                    <span class="badge bg-success">Completed</span>
                                @elseif($b->booking_status === 'cancelled')
                                    <span class="badge bg-secondary">Cancelled</span>
                                @elseif($b->booking_status === 'processing')
                                    <span class="badge bg-info">Processing</span>
                                @else
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @endif
                            </td>
                            <td data-label="Date">{{ $b->created_at->format('M j, H:i') }}</td>
                            <td data-label="">
                                <a href="{{ route('admin.bookings.show', $b) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

@php
    $recentSpecialRequests = \App\Models\SpecialRequest::latest()->take(8)->get();
@endphp
@if($recentSpecialRequests->isNotEmpty())
<div class="card">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Recent special requests</h5>
        <a href="{{ route('admin.special-requests.index') }}" class="btn btn-sm btn-outline-primary">View all</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 admin-table-cards">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Occasion</th>
                        <th>Event date</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentSpecialRequests as $sr)
                        <tr>
                            <td data-label="Client">
                                {{ $sr->name }}<br>
                                <small class="text-muted">{{ $sr->email }}</small>
                            </td>
                            <td data-label="Occasion">{{ $sr->occasion ?: 'Not specified' }}</td>
                            <td data-label="Event date">{{ optional($sr->event_date)->format('M j, Y') ?: 'Not specified' }}</td>
                            <td data-label="Status">
                                @php
                                    $srBadge = match($sr->status) {
                                        'new' => 'bg-primary',
                                        'in_review' => 'bg-info',
                                        'quoted' => 'bg-warning text-dark',
                                        'accepted' => 'bg-success',
                                        'declined', 'cancelled' => 'bg-secondary',
                                        default => 'bg-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $srBadge }}">{{ ucfirst(str_replace('_', ' ', $sr->status)) }}</span>
                            </td>
                            <td data-label="Submitted">{{ $sr->created_at->format('M j, H:i') }}</td>
                            <td data-label="">
                                <a href="{{ route('admin.special-requests.show', $sr) }}" class="btn btn-sm btn-outline-primary" title="View"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('admin.special-requests.quote', $sr) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Quote"><i class="bi bi-file-earmark-text"></i></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

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
