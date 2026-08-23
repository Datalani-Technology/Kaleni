@extends('admin.layout')

@section('title', 'Customers')
@section('sidebar_active', 'customers')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <h1 class="mb-0">Customers</h1>
</div>

<form method="GET" class="row g-2 mb-4 admin-filters">
    <div class="col-12 col-sm-6 col-md-4">
        <input type="text" name="q" class="form-control" placeholder="Search name, email, phone..." value="{{ request('q') }}">
    </div>
    <div class="col-12 col-sm-4 col-md-2">
        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Search</button>
    </div>
</form>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 admin-table-cards">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Contact</th>
                        <th class="text-end">Orders</th>
                        <th class="text-end">Lifetime Spend</th>
                        <th>Last Order</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $c)
                        <tr>
                            <td data-label="Customer"><strong>{{ $c->name }}</strong></td>
                            <td data-label="Contact">
                                {{ $c->email }}<br>
                                <small class="text-muted">{{ $c->phone }}</small>
                            </td>
                            <td data-label="Orders" class="text-end">{{ $c->orders_count }}</td>
                            <td data-label="Lifetime Spend" class="text-end">N$ {{ number_format($c->lifetime_spend ?? 0, 2) }}</td>
                            <td data-label="Last Order">{{ $c->orders_max_created_at ? \Illuminate\Support\Carbon::parse($c->orders_max_created_at)->format('d M Y') : '—' }}</td>
                            <td data-label="">
                                <a href="{{ route('admin.customers.show', $c) }}" class="btn btn-sm btn-primary"><i class="bi bi-eye"></i> View</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4" data-label="">No customers yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $customers->links() }}</div>
    </div>
</div>
@endsection
