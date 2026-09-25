@extends('admin.layout')

@section('title', 'Orders')
@section('sidebar_active', 'orders')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <h1 class="mb-0">Orders</h1>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('admin.orders.export', request()->query()) }}" class="btn btn-outline-success">
            <i class="bi bi-download"></i> Export CSV
        </a>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Dashboard
        </a>
    </div>
</div>
<p class="text-muted mb-4">Quick food orders placed for pickup or delivery, without booking a full catered event. Event bookings live under <a href="{{ route('admin.bookings.index') }}">Bookings</a>.</p>

<form method="GET" class="row g-2 mb-4 admin-filters">
    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
        <input type="text" name="q" class="form-control" placeholder="Order #, name, email, phone..." value="{{ request('q') }}">
    </div>
    <div class="col-12 col-sm-6 col-md-4 col-lg-2">
        <select name="status" class="form-select">
            <option value="">All statuses</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>
    </div>
    <div class="col-12 col-sm-6 col-md-4 col-lg-2">
        <select name="payment" class="form-select">
            <option value="">All payments</option>
            <option value="pending" {{ request('payment') === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="completed" {{ request('payment') === 'completed' ? 'selected' : '' }}>Paid</option>
            <option value="failed" {{ request('payment') === 'failed' ? 'selected' : '' }}>Failed</option>
        </select>
    </div>
    <div class="col-12 col-sm-6 col-md-4 col-lg-2">
        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Filter</button>
    </div>
</form>

<div class="card">
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
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td data-label="Order">
                                <strong>{{ $order->booking_number }}</strong>
                            </td>
                            <td data-label="Customer">
                                {{ $order->customer_name }}<br>
                                <small class="text-muted">{{ $order->customer_email }}</small>
                            </td>
                            <td data-label="Cart">
                                <span class="badge bg-secondary">{{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }}</span>
                                <small class="d-block text-muted">{{ Str::limit($order->items->map(fn ($i) => optional($i->menuItem)->name ?? 'Deleted item')->implode(', '), 40) }}</small>
                            </td>
                            <td data-label="Fulfillment">
                                @if($order->fulfillment_method === 'delivery')
                                    <span class="badge bg-info"><i class="bi bi-truck"></i> Delivery</span>
                                @else
                                    <span class="badge bg-secondary"><i class="bi bi-bag-check"></i> Pickup</span>
                                @endif
                            </td>
                            <td data-label="Total">N$ {{ number_format($order->total_amount, 2) }}</td>
                            <td data-label="Payment">
                                @if($order->payment_status === 'completed')
                                    <span class="badge bg-success">Paid</span>
                                @elseif($order->payment_status === 'failed')
                                    <span class="badge bg-danger">Failed</span>
                                @else
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @endif
                                <small class="d-block text-muted">{{ $order->payment_method_label }}</small>
                            </td>
                            <td data-label="Status">
                                @if($order->booking_status === 'completed')
                                    <span class="badge bg-success">Completed</span>
                                @elseif($order->booking_status === 'cancelled')
                                    <span class="badge bg-secondary">Cancelled</span>
                                @elseif($order->booking_status === 'processing')
                                    <span class="badge bg-info">Processing</span>
                                @else
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @endif
                            </td>
                            <td data-label="Actions">
                                <a href="{{ route('admin.bookings.show', $order) }}" class="btn btn-sm btn-primary" title="View">
                                    <i class="bi bi-eye"></i> View
                                </a>
                                <form action="{{ route('admin.bookings.destroy', $order) }}" method="POST" class="d-inline" data-confirm-message="This permanently deletes order {{ $order->booking_number }} and restores {{ $order->items->sum('quantity') }} unit(s) of stock for its items. This cannot be undone." data-confirm-label="Delete order">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4" data-label="">No orders found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $orders->links() }}</div>
    </div>
</div>
@endsection
