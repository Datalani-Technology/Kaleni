@extends('admin.layout')

@section('title', 'Food of the Day')
@section('sidebar_active', 'food-of-the-day')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <h1 class="mb-0">Food of the Day</h1>
    <a href="{{ route('admin.food-of-the-day.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Schedule a day
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 admin-table-cards">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Dish</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schedule as $entry)
                        <tr>
                            <td data-label="Date">{{ $entry->serve_date->format('D, j M Y') }}</td>
                            <td data-label="Dish">
                                {{ $entry->effective_title }}
                                @if($entry->menu_item_id)<small class="d-block text-muted">Linked to menu item</small>@endif
                            </td>
                            <td data-label="Price">{{ $entry->effective_price !== null ? 'N$ '.number_format((float) $entry->effective_price, 2) : 'N/A' }}</td>
                            <td data-label="Status">
                                @if($entry->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Hidden</span>
                                @endif
                            </td>
                            <td data-label="Actions">
                                <div class="d-flex flex-wrap gap-1">
                                    <a href="{{ route('admin.food-of-the-day.edit', $entry) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.food-of-the-day.destroy', $entry) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Remove">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4" data-label="">Nothing scheduled yet. <a href="{{ route('admin.food-of-the-day.create') }}">Schedule the first day</a></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $schedule->links() }}</div>
    </div>
</div>
@endsection
