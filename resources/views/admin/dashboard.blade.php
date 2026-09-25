@extends('admin.layout')

@section('title', 'Dashboard')
@section('sidebar_active', 'dashboard')

@section('content')
<h1 class="mb-4">Admin Dashboard</h1>

<h2 class="h5 text-muted mb-3"><i class="bi bi-inboxes"></i> Client Requests</h2>
<div class="row mb-4 g-3 admin-stat-cards">
    <div class="col-md-3">
        @php $pendingBookingCount = \App\Models\Booking::where('order_type', \App\Models\Booking::ORDER_TYPE_CATERING_BOOKING)->where('booking_status', 'pending')->count(); @endphp
        <a href="{{ route('admin.bookings.index') }}" class="text-decoration-none">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title text-white-50">Bookings</h5>
                    <h2 class="mb-0">{{ \App\Models\Booking::where('order_type', \App\Models\Booking::ORDER_TYPE_CATERING_BOOKING)->count() }}</h2>
                    <small class="opacity-75">{{ $pendingBookingCount }} pending →</small>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        @php $pendingOrderCount = \App\Models\Booking::where('order_type', \App\Models\Booking::ORDER_TYPE_QUICK_ORDER)->where('booking_status', 'pending')->count(); @endphp
        <a href="{{ route('admin.orders.index') }}" class="text-decoration-none">
            <div class="card admin-stat-white">
                <div class="card-body">
                    <h5 class="card-title text-muted">Orders</h5>
                    <h2 class="mb-0 text-primary">{{ \App\Models\Booking::where('order_type', \App\Models\Booking::ORDER_TYPE_QUICK_ORDER)->count() }}</h2>
                    <small class="text-muted">{{ $pendingOrderCount }} pending →</small>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        @php $newSpecialRequestCount = \App\Models\SpecialRequest::where('source', \App\Models\SpecialRequest::SOURCE_SPECIAL_REQUEST_PAGE)->where('status', 'new')->count(); @endphp
        <a href="{{ route('admin.special-requests.index') }}" class="text-decoration-none">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title text-white-50">Special Requests</h5>
                    <h2 class="mb-0">{{ \App\Models\SpecialRequest::where('source', \App\Models\SpecialRequest::SOURCE_SPECIAL_REQUEST_PAGE)->count() }}</h2>
                    <small class="opacity-75">{{ $newSpecialRequestCount }} new →</small>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        @php $newQuoteRequestCount = \App\Models\SpecialRequest::where('source', \App\Models\SpecialRequest::SOURCE_HOME_QUOTE_FORM)->where('status', 'new')->count(); @endphp
        <a href="{{ route('admin.quotes.index') }}" class="text-decoration-none">
            <div class="card admin-stat-white">
                <div class="card-body">
                    <h5 class="card-title text-muted">Quotes</h5>
                    <h2 class="mb-0 text-primary">{{ \App\Models\SpecialRequest::where('source', \App\Models\SpecialRequest::SOURCE_HOME_QUOTE_FORM)->count() }}</h2>
                    <small class="text-muted">{{ $newQuoteRequestCount }} new →</small>
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

<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title">Quick Actions</h5>
        <div class="d-flex flex-wrap gap-2 admin-quick-actions">
            <a href="{{ route('admin.bookings.index') }}" class="btn btn-primary">
                <i class="bi bi-cart-check"></i> View All Bookings
            </a>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-basket"></i> Orders
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
            <a href="{{ route('admin.quotes.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-file-earmark-text"></i> Quotes
            </a>
            <a href="{{ route('admin.stock.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-box-seam"></i> Stock Count & Inventory
            </a>
            <a href="{{ route('admin.finance.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-cash-coin"></i> Finance
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
    // Completed bookings/orders need no further action, so they don't
    // belong on a board meant to surface what's still active.
    $recentBookings = \App\Models\Booking::with('items.menuItem')->where('order_type', \App\Models\Booking::ORDER_TYPE_CATERING_BOOKING)->where('booking_status', '!=', 'completed')->latest()->take(8)->get();
    $recentOrders = \App\Models\Booking::with('items.menuItem')->where('order_type', \App\Models\Booking::ORDER_TYPE_QUICK_ORDER)->where('booking_status', '!=', 'completed')->latest()->take(8)->get();
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

@if($recentOrders->isNotEmpty())
<div class="card mb-4">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Recent orders</h5>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-primary">View all</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 admin-table-cards">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Customer</th>
                        <th>Cart</th>
                        <th>Fulfillment</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentOrders as $o)
                        <tr>
                            <td data-label="Order"><strong>{{ $o->booking_number }}</strong></td>
                            <td data-label="Customer">{{ $o->customer_name }}</td>
                            <td data-label="Cart">
                                <span class="badge bg-secondary">{{ $o->items->count() }} {{ Str::plural('item', $o->items->count()) }}</span>
                                <small class="d-block text-muted">{{ Str::limit($o->items->map(fn ($i) => optional($i->menuItem)->name ?? 'Deleted item')->implode(', '), 40) }}</small>
                            </td>
                            <td data-label="Fulfillment">
                                @if($o->fulfillment_method === 'delivery')
                                    <span class="badge bg-info"><i class="bi bi-truck"></i> Delivery</span>
                                @else
                                    <span class="badge bg-secondary"><i class="bi bi-bag-check"></i> Pickup</span>
                                @endif
                            </td>
                            <td data-label="Total">N$ {{ number_format((float) $o->total_amount, 2) }}</td>
                            <td data-label="Status">
                                @if($o->booking_status === 'completed')
                                    <span class="badge bg-success">Completed</span>
                                @elseif($o->booking_status === 'cancelled')
                                    <span class="badge bg-secondary">Cancelled</span>
                                @elseif($o->booking_status === 'processing')
                                    <span class="badge bg-info">Processing</span>
                                @else
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @endif
                            </td>
                            <td data-label="Date">{{ $o->created_at->format('M j, H:i') }}</td>
                            <td data-label="">
                                <a href="{{ route('admin.bookings.show', $o) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
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
    // Accepted is the special request/quote equivalent of "completed" — no
    // further action needed, so it doesn't belong on this board either.
    $recentSpecialRequestsOnly = \App\Models\SpecialRequest::where('source', \App\Models\SpecialRequest::SOURCE_SPECIAL_REQUEST_PAGE)->where('status', '!=', 'accepted')->latest()->take(8)->get();
    $recentQuotesOnly = \App\Models\SpecialRequest::where('source', \App\Models\SpecialRequest::SOURCE_HOME_QUOTE_FORM)->where('status', '!=', 'accepted')->latest()->take(8)->get();
@endphp

@if($recentSpecialRequestsOnly->isNotEmpty())
<div class="card mb-4">
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
                    @foreach($recentSpecialRequestsOnly as $sr)
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

@if($recentQuotesOnly->isNotEmpty())
<div class="card">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Recent quotes</h5>
        <a href="{{ route('admin.quotes.index') }}" class="btn btn-sm btn-outline-primary">View all</a>
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
                    @foreach($recentQuotesOnly as $sr)
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
@endsection
