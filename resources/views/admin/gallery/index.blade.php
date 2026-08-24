@extends('admin.layout')

@section('title', 'Gallery')
@section('sidebar_active', 'gallery')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <h1 class="mb-0">Gallery</h1>
    <a href="{{ route('admin.gallery.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add Item
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 admin-table-cards">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td data-label="Image">
                                @if($item->image)
                                    <img src="{{ $item->image_url }}" alt="" style="width: 60px; height: 60px; object-fit: cover; border-radius: 6px;">
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td data-label="Title">{{ $item->title ?: '—' }}</td>
                            <td data-label="Description">{{ Str::limit($item->description, 60) ?: '—' }}</td>
                            <td data-label="Actions">
                                <div class="d-flex flex-wrap gap-1">
                                    <a href="{{ route('admin.gallery.edit', $item) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.gallery.destroy', $item) }}" method="POST" class="d-inline">
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
                            <td colspan="4" class="text-center py-4" data-label="">No gallery items yet. <a href="{{ route('admin.gallery.create') }}">Add one</a></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($items->hasPages())
            <div class="card-footer">{{ $items->links() }}</div>
        @endif
    </div>
</div>
@endsection
