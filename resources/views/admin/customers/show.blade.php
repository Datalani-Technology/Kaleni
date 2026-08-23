@extends('admin.layout')

@section('title', $customer->name)
@section('sidebar_active', 'customers')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary btn-sm mb-3"><i class="bi bi-arrow-left"></i> All Customers</a>
    <h1 class="mb-0">{{ $customer->name }}</h1>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header bg-light"><strong>Profile</strong></div>
            <div class="card-body">
                <form action="{{ route('admin.customers.update', $customer) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $customer->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" value="{{ $customer->email }}" disabled>
                        <div class="form-text">Email can't be changed (used to match orders).</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $customer->phone) }}">
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="2">{{ old('address', $customer->address) }}</textarea>
                        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes <span class="text-muted small">(internal, staff-only)</span></label>
                        <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="4" placeholder="e.g. Prefers pastel colours, allergic to lilies, VIP customer...">{{ old('notes', $customer->notes) }}</textarea>
                        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-save"></i> Save</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="row text-center g-3">
                    <div class="col-6">
                        <div class="text-muted small">Orders</div>
                        <div class="h4 mb-0">{{ $customer->orders->count() }}</div>
                    </div>
                    <div class="col-6">
                        <div class="text-muted small">Lifetime Spend</div>
                        <div class="h4 mb-0 text-success">N$ {{ number_format($lifetimeSpend, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-light"><strong>Order history</strong></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 admin-table-cards">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th class="text-end">Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customer->orders as $o)
                                <tr>
                                    <td data-label="Order"><strong>{{ $o->order_number }}</strong></td>
                                    <td data-label="Date">{{ $o->created_at->format('d M Y') }}</td>
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
                                    <td data-label="Total" class="text-end">N$ {{ number_format((float) $o->total_amount, 2) }}</td>
                                    <td data-label="">
                                        <a href="{{ route('admin.orders.show', $o) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-4" data-label="">No orders yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
