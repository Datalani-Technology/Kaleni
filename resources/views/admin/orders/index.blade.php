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
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td data-label="Order">
                                <strong>{{ $order->order_number }}</strong>
                            </td>
                            <td data-label="Customer">
                                {{ $order->customer_name }}<br>
                                <small class="text-muted">{{ $order->customer_email }}</small>
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
                                <small class="d-block text-muted">{{ ucfirst($order->payment_method) }}</small>
                            </td>
                            <td data-label="Status">
                                @if($order->order_status === 'completed')
                                    <span class="badge bg-success">Completed</span>
                                @elseif($order->order_status === 'cancelled')
                                    <span class="badge bg-secondary">Cancelled</span>
                                @elseif($order->order_status === 'processing')
                                    <span class="badge bg-info">Processing</span>
                                @else
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @endif
                            </td>
                            <td data-label="Date">{{ $order->created_at->format('M j, Y H:i') }}</td>
                            <td data-label="Actions">
                                <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-primary" title="View">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4" data-label="">No orders found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $orders->links() }}</div>
    </div>
</div>
@endsection
