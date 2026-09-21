@extends('admin.layout')

@section('title', 'Expenses')
@section('sidebar_active', 'expenses')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <h1 class="mb-0">Expenses</h1>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('admin.expenses.export', request()->query()) }}" class="btn btn-outline-success">
            <i class="bi bi-download"></i> Export CSV
        </a>
        <a href="{{ route('admin.expenses.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Log expense</a>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-6 col-md-3">
        <a href="{{ route('admin.expenses.index', ['period' => 'week']) }}" class="text-decoration-none">
            <div class="card h-100 border-0 {{ $period === 'week' ? 'border border-danger' : '' }}" style="background: #fdeeee;">
                <div class="card-body">
                    <div class="text-muted small">This week</div>
                    <div class="h5 mb-0 text-danger">N$ {{ number_format($totalWeek, 2) }}</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('admin.expenses.index', ['period' => 'month']) }}" class="text-decoration-none">
            <div class="card h-100 border-0 {{ $period === 'month' ? 'border border-danger' : '' }}" style="background: #fdeeee;">
                <div class="card-body">
                    <div class="text-muted small">This month</div>
                    <div class="h5 mb-0 text-danger">N$ {{ number_format($totalMonth, 2) }}</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('admin.expenses.index', ['period' => 'year']) }}" class="text-decoration-none">
            <div class="card h-100 border-0 {{ $period === 'year' ? 'border border-danger' : '' }}" style="background: #fdeeee;">
                <div class="card-body">
                    <div class="text-muted small">This year</div>
                    <div class="h5 mb-0 text-danger">N$ {{ number_format($totalYear, 2) }}</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('admin.expenses.index') }}" class="text-decoration-none">
            <div class="card h-100 border-0 {{ $period === '' ? 'border border-danger' : '' }}" style="background: #fdeeee;">
                <div class="card-body">
                    <div class="text-muted small">All-time</div>
                    <div class="h5 mb-0 text-danger">N$ {{ number_format($totalAllTime, 2) }}</div>
                </div>
            </div>
        </a>
    </div>
</div>

<form method="GET" class="row g-2 mb-3 admin-filters">
    @if($period)
        <input type="hidden" name="period" value="{{ $period }}">
    @endif
    <div class="col-12 col-sm-4 col-md-3">
        <select name="category" class="form-select">
            <option value="">All categories</option>
            @foreach($categories as $c)
                <option value="{{ $c }}" {{ request('category') === $c ? 'selected' : '' }}>{{ $c }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-6 col-sm-4 col-md-3">
        <input type="date" name="from" class="form-control" value="{{ request('from') }}" placeholder="From" {{ $period ? 'disabled' : '' }}>
    </div>
    <div class="col-6 col-sm-4 col-md-3">
        <input type="date" name="to" class="form-control" value="{{ request('to') }}" placeholder="To" {{ $period ? 'disabled' : '' }}>
    </div>
    <div class="col-12 col-md-3">
        <button type="submit" class="btn btn-outline-primary w-100"><i class="bi bi-funnel"></i> Filter</button>
    </div>
    @if($period)
        <div class="col-12">
            <span class="badge bg-secondary">Showing: {{ ucfirst($period) === 'Week' ? 'This week' : (ucfirst($period) === 'Month' ? 'This month' : 'This year') }}</span>
            <a href="{{ route('admin.expenses.index') }}" class="small ms-2">Clear period, use custom dates</a>
        </div>
    @endif
</form>

<div class="card mb-3" style="max-width: 320px;">
    <div class="card-body py-3">
        <div class="text-muted small">Total (filtered)</div>
        <div class="h4 mb-0 text-danger">N$ {{ number_format($totalFiltered, 2) }}</div>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 admin-table-cards">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Logged by</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $e)
                        <tr>
                            <td data-label="Date">{{ $e->spent_at->format('d M Y') }}</td>
                            <td data-label="Category"><span class="badge bg-secondary">{{ $e->category }}</span></td>
                            <td data-label="Description">{{ $e->description ?: 'N/A' }}</td>
                            <td data-label="Amount">N$ {{ number_format($e->amount, 2) }}</td>
                            <td data-label="Logged by">{{ $e->user->name ?? 'N/A' }}</td>
                            <td data-label="">
                                <form action="{{ route('admin.expenses.destroy', $e) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4" data-label="">No expenses logged yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $expenses->links() }}</div>
    </div>
</div>
@endsection
