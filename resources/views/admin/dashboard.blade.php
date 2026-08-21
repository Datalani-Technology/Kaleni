@extends('admin.layout')

@section('title', 'Dashboard')
@section('sidebar_active', 'dashboard')

@section('content')
<h1 class="mb-4">Admin Dashboard</h1>

<div class="row mb-4 g-3 admin-dashboard-stats">
    <div class="col-6 col-md-3">
        <a href="{{ route('admin.products.index') }}" class="text-decoration-none">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title text-white-50">Total Products</h5>
                    <h2 class="mb-0">{{ \App\Models\Product::count() }}</h2>
                    <small class="opacity-75">View all →</small>
                </div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('admin.orders.index') }}" class="text-decoration-none">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title text-white-50">Total Orders</h5>
                    <h2 class="mb-0">{{ \App\Models\Order::count() }}</h2>
                    <small class="opacity-75">View all →</small>
                </div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="text-decoration-none">
            <div class="card text-white bg-warning text-dark">
                <div class="card-body">
                    <h5 class="card-title text-dark text-opacity-75">Pending Orders</h5>
                    <h2 class="mb-0">{{ \App\Models\Order::where('order_status', 'pending')->count() }}</h2>
                    <small class="opacity-75">View orders →</small>
                </div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        @php
            $lowThreshold = config('inventory.low_stock_threshold', 5);
            $lowCount = \App\Models\Product::where('stock', '<=', $lowThreshold)->count();
        @endphp
        <a href="{{ route('admin.stock.index') }}" class="text-decoration-none">
            <div class="card {{ $lowCount > 0 ? 'bg-danger text-white' : 'bg-secondary text-white' }}">
                <div class="card-body">
                    <h5 class="card-title text-white-50">Low Stock (≤{{ $lowThreshold }})</h5>
                    <h2 class="mb-0">{{ $lowCount }}</h2>
                    <small class="opacity-75">View stock count →</small>
                </div>
            </div>
        </a>
    </div>
</div>
@push('styles')
<style>
    @media (max-width: 575.98px) {
        .admin-dashboard-stats .card-body { padding: 0.75rem !important; }
        .admin-dashboard-stats .card-title { font-size: 0.8rem; margin-bottom: 0.25rem; }
        .admin-dashboard-stats h2 { font-size: 1.35rem; }
        .admin-dashboard-stats small { font-size: 0.7rem; }
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
                    <span><i class="bi bi-envelope-exclamation text-primary"></i> <strong>{{ $unreadEnquiries }}</strong> unread {{ $unreadEnquiries === 1 ? 'enquiry' : 'enquiries' }}</span>
                    <span class="text-primary">View →</span>
                </div>
            </div>
        </a>
    </div>
</div>
@endif
@php
    $potentialFromStock = \App\Models\Product::get()->sum(fn ($p) => $p->stock * (float) $p->price);
    $totalSales = (float) \App\Models\Order::where('order_status', '!=', 'cancelled')->sum('total_amount');
@endphp
<div class="row mb-4 g-3">
    <div class="col-md-6">
        <div class="card border-primary">
            <div class="card-body">
                <h5 class="card-title text-muted">Potential from current stock</h5>
                <p class="mb-0 small text-muted">If all current stock were sold at listed prices</p>
                <h2 class="text-primary mt-1">N$ {{ number_format($potentialFromStock, 2) }}</h2>
                <a href="{{ route('admin.stock.index') }}" class="btn btn-sm btn-outline-primary mt-2">View stock</a>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-success">
            <div class="card-body">
                <h5 class="card-title text-muted">Total sales (orders)</h5>
                <p class="mb-0 small text-muted">Sum of all order totals · updates as customers buy</p>
                <h2 class="text-success mt-1">N$ {{ number_format($totalSales, 2) }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title">Quick Actions</h5>
        <div class="d-flex flex-wrap gap-2 admin-quick-actions">
            <a href="{{ route('admin.orders.index') }}" class="btn btn-primary">
                <i class="bi bi-cart-check"></i> View All Orders
            </a>
            <a href="{{ route('admin.products.create') }}" class="btn btn-outline-primary">
                <i class="bi bi-plus-circle"></i> Add New Product
            </a>
            <a href="{{ route('admin.stock.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-boxes"></i> Stock Count & Inventory
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
    $recentOrders = \App\Models\Order::with('items')->latest()->take(8)->get();
@endphp
@if($recentOrders->isNotEmpty())
<div class="card">
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
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentOrders as $o)
                        <tr>
                            <td data-label="Order"><strong>{{ $o->order_number }}</strong></td>
                            <td data-label="Customer">{{ $o->customer_name }}</td>
                            <td data-label="Total">N$ {{ number_format((float) $o->total_amount, 2) }}</td>
                            <td data-label="Status">
                                @if($o->order_status === 'completed')
                                    <span class="badge bg-success">Completed</span>
                                @elseif($o->order_status === 'cancelled')
                                    <span class="badge bg-secondary">Cancelled</span>
                                @elseif($o->order_status === 'processing')
                                    <span class="badge bg-info">Processing</span>
                                @else
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @endif
                            </td>
                            <td data-label="Date">{{ $o->created_at->format('M j, H:i') }}</td>
                            <td data-label="">
                                <a href="{{ route('admin.orders.show', $o) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
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
