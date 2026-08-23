@extends('admin.layout')

@section('title', 'Reports')
@section('sidebar_active', 'reports')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <h1 class="mb-0">Sales &amp; Expense Report</h1>
    <button type="button" onclick="openAdminDocument('{{ route('admin.reports.document', ['from' => $from->toDateString(), 'to' => $to->toDateString()]) }}')" class="btn btn-outline-dark">
        <i class="bi bi-file-earmark-text"></i> Open Printable Report
    </button>
</div>

<form method="GET" class="row g-2 mb-4">
    <div class="col-6 col-md-3">
        <label class="form-label small text-muted mb-1">From</label>
        <input type="date" name="from" class="form-control" value="{{ $from->toDateString() }}">
    </div>
    <div class="col-6 col-md-3">
        <label class="form-label small text-muted mb-1">To</label>
        <input type="date" name="to" class="form-control" value="{{ $to->toDateString() }}">
    </div>
    <div class="col-12 col-md-2 d-flex align-items-end">
        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-arrow-repeat"></i> Update</button>
    </div>
</form>

<div class="mb-3">
    <h2 class="h5">/Namsa Florals — {{ $from->format('d M Y') }} to {{ $to->format('d M Y') }}</h2>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-success h-100">
            <div class="card-body">
                <div class="text-muted small">Revenue ({{ $orderCount }} orders)</div>
                <div class="h3 text-success mb-0">N$ {{ number_format($revenue, 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-danger h-100">
            <div class="card-body">
                <div class="text-muted small">Expenses</div>
                <div class="h3 text-danger mb-0">N$ {{ number_format($totalExpenses, 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-primary h-100">
            <div class="card-body">
                <div class="text-muted small">Net Profit</div>
                <div class="h3 mb-0 {{ $netProfit >= 0 ? 'text-primary' : 'text-danger' }}">N$ {{ number_format($netProfit, 2) }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-light"><strong>Expenses by category</strong></div>
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead><tr><th>Category</th><th class="text-end">Amount</th></tr></thead>
            <tbody>
                @forelse($expensesByCategory as $category => $amount)
                    <tr>
                        <td>{{ $category }}</td>
                        <td class="text-end">N$ {{ number_format($amount, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="2" class="text-center text-muted py-3">No expenses in this period.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
