@extends('admin.layout')

@section('title', 'Menu Items')
@section('sidebar_active', 'menu-items')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <h1 class="mb-0">Menu Items Management</h1>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('admin.menu-items.export') }}" class="btn btn-outline-success">
            <i class="bi bi-download"></i> Export CSV
        </a>
        <a href="{{ route('admin.menu-items.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add New Menu Item
        </a>
    </div>
</div>

@if($errors->has('ids'))
    <div class="alert alert-warning alert-dismissible fade show">
        {{ $errors->first('ids') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<form id="bulk-form" action="{{ route('admin.menu-items.bulk-destroy') }}" method="POST" class="mb-0">
    @csrf
    <div class="card mb-3" id="bulk-actions-bar" style="display: none;">
        <div class="card-body py-2 d-flex flex-wrap align-items-center gap-2">
            <span class="me-2" id="bulk-count">0 selected</span>
            <button type="submit" class="btn btn-danger btn-sm" id="bulk-delete-btn">
                <i class="bi bi-trash"></i> Delete selected
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm" id="bulk-clear-btn">Clear selection</button>
        </div>
    </div>
</form>
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 admin-table-cards">
                <thead>
                    <tr>
                        <th style="width: 2.5rem;">
                            <label class="form-check-label d-flex align-items-center justify-content-center mb-0" style="cursor: pointer;">
                                <input type="checkbox" class="form-check-input" id="select-all" form="bulk-form" title="Select all on this page">
                            </label>
                        </th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($menuItems as $menuItem)
                            <tr>
                                <td data-label="Select" style="vertical-align: middle;">
                                    <label class="form-check-label d-flex align-items-center justify-content-center mb-0" style="cursor: pointer;">
                                        <input type="checkbox" class="form-check-input product-checkbox" name="ids[]" value="{{ $menuItem->id }}" form="bulk-form">
                                    </label>
                                </td>
                                <td data-label="">
                                    @include('admin.partials.menu-item-image', ['menuItem' => $menuItem, 'size' => 50])
                                </td>
                                <td data-label="Name">{{ $menuItem->name }}</td>
                                <td data-label="Price">N$ {{ number_format($menuItem->price, 2) }}{{ $menuItem->unit_label ? ' / '.$menuItem->unit_label : '' }}</td>
                                <td data-label="Stock">{{ $menuItem->stock }}</td>
                                <td data-label="Category">{{ $menuItem->category ?? 'N/A' }}</td>
                                <td data-label="Status">
                                    @if($menuItem->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td data-label="Actions">
                                    <div class="d-flex flex-wrap gap-1">
                                        <a href="{{ route('admin.menu-items.edit', $menuItem->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.menu-items.destroy', $menuItem->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4" data-label="">No menu items found. <a href="{{ route('admin.menu-items.create') }}">Create one now</a></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        <div class="card-footer d-flex flex-wrap align-items-center gap-2">
            <div class="me-auto">{{ $menuItems->links() }}</div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    var form = document.getElementById('bulk-form');
    var bar = document.getElementById('bulk-actions-bar');
    var countEl = document.getElementById('bulk-count');
    var clearBtn = document.getElementById('bulk-clear-btn');
    var selectAll = document.getElementById('select-all');
    var checkboxes = document.querySelectorAll('.product-checkbox');

    function updateUI() {
        var n = 0;
        for (var i = 0; i < checkboxes.length; i++) { if (checkboxes[i].checked) n++; }
        if (countEl) countEl.textContent = n + ' selected';
        if (bar) bar.style.display = n ? 'block' : 'none';
        if (selectAll) selectAll.checked = n > 0 && n === checkboxes.length;
        if (selectAll) selectAll.indeterminate = n > 0 && n < checkboxes.length;
    }

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            for (var i = 0; i < checkboxes.length; i++) checkboxes[i].checked = selectAll.checked;
            updateUI();
        });
    }
    for (var i = 0; i < checkboxes.length; i++) {
        checkboxes[i].addEventListener('change', updateUI);
    }
    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            if (selectAll) selectAll.checked = false;
            for (var j = 0; j < checkboxes.length; j++) checkboxes[j].checked = false;
            updateUI();
        });
    }
    updateUI();
})();
</script>
@endpush
@endsection
