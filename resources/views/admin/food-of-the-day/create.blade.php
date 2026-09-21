@extends('admin.layout')

@section('title', 'Schedule Food of the Day')
@section('sidebar_active', 'food-of-the-day')

@section('content')
<h1 class="mb-4">Schedule Food of the Day</h1>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.food-of-the-day.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="serve_date" class="form-label">Date *</label>
                <input type="date" class="form-control @error('serve_date') is-invalid @enderror" id="serve_date" name="serve_date" value="{{ old('serve_date', now()->toDateString()) }}" required>
                @error('serve_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="menu_item_id" class="form-label">Link to an existing menu item <span class="text-muted fw-normal">Optional</span></label>
                <select class="form-select @error('menu_item_id') is-invalid @enderror" id="menu_item_id" name="menu_item_id">
                    <option value="">One-off special (fill in details below)</option>
                    @foreach($menuItems as $item)
                        <option value="{{ $item->id }}" {{ old('menu_item_id') == $item->id ? 'selected' : '' }}>{{ $item->name }} (N$ {{ number_format($item->price, 2) }})</option>
                    @endforeach
                </select>
                @error('menu_item_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="form-text">Pick a menu item to spotlight it, or leave blank and describe a one-off dish below.</div>
            </div>
            <div class="mb-3">
                <label for="title" class="form-label">Title <span class="text-muted fw-normal">Required if not linking a menu item</span></label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}">
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="price" class="form-label">Price override (N$) <span class="text-muted fw-normal">Optional</span></label>
                    <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price') }}">
                    @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="image" class="form-label">Image override <span class="text-muted fw-normal">Optional</span></label>
                    <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                    @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">Active (visible on the storefront)</label>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Schedule</button>
                <a href="{{ route('admin.food-of-the-day.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
