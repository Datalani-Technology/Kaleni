@extends('admin.layout')

@section('title', 'Stock Count')
@section('sidebar_active', 'stock')

@push('styles')
<style>
    .stock-out { background: #f8d7da !important; }
    .stock-low { background: #fff3cd !important; }
    .adjust-form { display: flex; flex-wrap: wrap; gap: 6px; align-items: center; }
    .adjust-form input[type="number"] { width: 70px; }
    .adjust-form .btn { white-space: nowrap; }
    @media (max-width: 575.98px) {
        .adjust-form { flex-direction: column; align-items: stretch; }
        .adjust-form input[type="number"], .adjust-form .form-select { width: 100% !important; }
        .stock-header-form { flex-direction: column !important; width: 100%; }
        .stock-header-form .form-control,
        .stock-header-form .form-select { width: 100% !important; min-width: 0 !important; }
    }
</style>
@endpush

@section('content')
<h1 class="mb-4"><i class="bi bi-box-seam"></i> Stock Count & Inventory</h1>

<div class="row g-3 mb-4 admin-stat-cards">
    <div class="col-6 col-md-3">
        <div class="card border-0 bg-primary text-white">
            <div class="card-body"><h6 class="card-title text-white-50">Total units</h6><h3 class="mb-0">{{ number_format($totalUnits) }}</h3></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 admin-stat-white">
            <div class="card-body"><h6 class="card-title text-muted">Low stock (≤{{ $threshold }})</h6><h3 class="mb-0 {{ $lowStockCount > 0 ? 'text-danger' : 'text-muted' }}">{{ $lowStockCount }}</h3></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 bg-primary text-white">
            <div class="card-body"><h6 class="card-title text-white-50">Out of stock</h6><h3 class="mb-0">{{ $outOfStockCount }}</h3></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 admin-stat-white">
            <div class="card-body"><h6 class="card-title text-muted">Menu items</h6><h3 class="mb-0 text-primary">{{ $menuItemsCount }}</h3></div>
        </div>
    </div>
</div>
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card border-primary">
            <div class="card-body">
                <h6 class="card-title text-muted">Potential from stock</h6>
                <p class="mb-0 small text-muted">Stock × price for each menu item</p>
                <h3 class="text-primary mt-1 mb-0">N$ {{ number_format($potentialValue, 2) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-success">
            <div class="card-body">
                <h6 class="card-title text-muted">Total sales (bookings)</h6>
                <p class="mb-0 small text-muted">Sum of booking totals · counts as customers order</p>
                <h3 class="text-success mt-1 mb-0">N$ {{ number_format($totalSales, 2) }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header d-flex flex-wrap align-items-center gap-2">
        <span class="fw-bold">All menu items</span>
        <span class="text-muted small d-none d-md-inline">Set to = new total · Add/Subtract = amount</span>
        <form action="{{ route('admin.stock.index') }}" method="GET" class="d-flex flex-wrap gap-2 ms-md-auto stock-header-form">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search..." value="{{ request('search') }}" style="min-width: 120px;">
            <select name="filter" class="form-select form-select-sm" style="min-width: 120px;">
                <option value="">All stock</option>
                <option value="low" {{ request('filter') === 'low' ? 'selected' : '' }}>Low</option>
                <option value="out" {{ request('filter') === 'out' ? 'selected' : '' }}>Out</option>
            </select>
            <button type="submit" class="btn btn-sm btn-outline-primary"><i class="bi bi-search"></i></button>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 admin-table-cards">
                <thead>
                    <tr>
                        <th>Menu item</th>
                        <th>Category</th>
                        <th>Stock</th>
                        <th>Value</th>
                        <th>Adjust stock</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($menuItems as $item)
                        @php $rowClass = $item->stock <= 0 ? 'stock-out' : ($item->stock <= $threshold ? 'stock-low' : 'stock-ok'); @endphp
                        <tr class="{{ $rowClass }}">
                            <td data-label="">
                                <div class="d-flex align-items-center gap-2">
                                    @include('admin.partials.menu-item-image', ['menuItem' => $item, 'size' => 40])
                                    <span>{{ $item->name }}</span>
                                </div>
                            </td>
                            <td data-label="Category">{{ $item->category ?? 'N/A' }}</td>
                            <td data-label="Stock">
                                <strong>{{ $item->stock }}</strong>
                                @if($item->stock <= 0)<span class="badge bg-danger ms-1">Out</span>
                                @elseif($item->stock <= $threshold)<span class="badge bg-warning text-dark ms-1">Low</span>@endif
                            </td>
                            <td data-label="Value">N$ {{ number_format($item->stock * (float) $item->price, 2) }}</td>
                            <td data-label="Adjust">
                                <form action="{{ route('admin.stock.adjust') }}" method="POST" class="adjust-form" data-item-name="{{ $item->name }}" data-current-stock="{{ $item->stock }}" data-confirm-mode="stock-adjust">
                                    @csrf
                                    <input type="hidden" name="menu_item_id" value="{{ $item->id }}">
                                    <select name="action" class="form-select form-select-sm" style="width: 95px;">
                                        <option value="set">Set to</option>
                                        <option value="add">Add</option>
                                        <option value="subtract">Subtract</option>
                                    </select>
                                    <input type="number" name="quantity" class="form-control form-control-sm" min="0" value="0" required title="Set to: new total. Add/Subtract: amount.">
                                    <input type="text" name="reason" class="form-control form-control-sm" placeholder="Reason (opt)" style="width: 120px;">
                                    <button type="submit" class="btn btn-sm btn-primary">Update</button>
                                </form>
                            </td>
                            <td data-label="">
                                <a href="{{ route('admin.menu-items.edit', $item->id) }}" class="btn btn-sm btn-outline-secondary" title="Edit"><i class="bi bi-pencil"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4" data-label="">No menu items match.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($menuItems->hasPages())
        <div class="card-footer">{{ $menuItems->links() }}</div>
    @endif
</div>

@if($recentMovements->isNotEmpty())
    <div class="card">
        <div class="card-header fw-bold"><i class="bi bi-clock-history"></i> Recent stock changes</div>
        <div class="card-body p-0">
            <ul class="list-group list-group-flush">
                @foreach($recentMovements as $m)
                    <li class="list-group-item d-flex flex-wrap justify-content-between align-items-center gap-1">
                        <span>
                            <strong>{{ $m->menuItem->name ?? 'Deleted item' }}</strong>
                            @if($m->type === 'set') set to <strong>{{ $m->quantity_after }}</strong>
                            @elseif($m->type === 'add') +{{ $m->delta }} → <strong>{{ $m->quantity_after }}</strong>
                            @else {{ $m->delta }} → <strong>{{ $m->quantity_after }}</strong> @endif
                            @if($m->reason) <span class="text-muted">({{ Str::limit($m->reason, 40) }})</span> @endif
                        </span>
                        <span class="text-muted small">{{ $m->created_at->diffForHumans() }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

@endsection
