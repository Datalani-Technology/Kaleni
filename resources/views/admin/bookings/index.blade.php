@extends('admin.layout')

@section('title', 'Bookings')
@section('sidebar_active', 'bookings')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <h1 class="mb-0">Bookings</h1>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('admin.bookings.export', request()->query()) }}" class="btn btn-outline-success">
            <i class="bi bi-download"></i> Export CSV
        </a>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Dashboard
        </a>
    </div>
</div>

<form method="GET" class="row g-2 mb-4 admin-filters">
    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
        <input type="text" name="q" class="form-control" placeholder="Booking #, name, email, phone..." value="{{ request('q') }}">
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
                        <th>Booking</th>
                        <th>Customer</th>
                        <th>Cart</th>
                        <th>Event date</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                        <tr>
                            <td data-label="Booking">
                                <strong>{{ $booking->booking_number }}</strong>
                            </td>
                            <td data-label="Customer">
                                {{ $booking->customer_name }}<br>
                                <small class="text-muted">{{ $booking->customer_email }}</small>
                            </td>
                            <td data-label="Cart">
                                <span class="badge bg-secondary">{{ $booking->items->count() }} {{ Str::plural('item', $booking->items->count()) }}</span>
                                <small class="d-block text-muted">{{ Str::limit($booking->items->map(fn ($i) => optional($i->menuItem)->name ?? 'Deleted item')->implode(', '), 40) }}</small>
                            </td>
                            <td data-label="Event date">{{ optional($booking->event_date)->format('M j, Y') ?: 'N/A' }}</td>
                            <td data-label="Total">N$ {{ number_format($booking->total_amount, 2) }}</td>
                            <td data-label="Payment">
                                @if($booking->payment_status === 'completed')
                                    <span class="badge bg-success">Paid</span>
                                @elseif($booking->payment_status === 'failed')
                                    <span class="badge bg-danger">Failed</span>
                                @else
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @endif
                                <small class="d-block text-muted">{{ $booking->payment_method_label }}</small>
                            </td>
                            <td data-label="Status">
                                @if($booking->booking_status === 'completed')
                                    <span class="badge bg-success">Completed</span>
                                @elseif($booking->booking_status === 'cancelled')
                                    <span class="badge bg-secondary">Cancelled</span>
                                @elseif($booking->booking_status === 'processing')
                                    <span class="badge bg-info">Processing</span>
                                @else
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @endif
                            </td>
                            <td data-label="Actions">
                                <a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-sm btn-primary" title="View">
                                    <i class="bi bi-eye"></i> View
                                </a>
                                <form action="{{ route('admin.bookings.destroy', $booking) }}" method="POST" class="d-inline" data-confirm-message="This permanently deletes booking {{ $booking->booking_number }} and restores {{ $booking->items->sum('quantity') }} unit(s) of stock for its items. This cannot be undone." data-confirm-label="Delete booking">
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
                            <td colspan="8" class="text-center py-4" data-label="">No bookings found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $bookings->links() }}</div>
    </div>
</div>
@endsection
