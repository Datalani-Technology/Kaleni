@extends('admin.layout')

@section('title', 'Edit Menu Item')
@section('sidebar_active', 'menu-items')

@section('content')
<h1 class="mb-4">Edit Menu Item</h1>

<div class="card">
    <div class="card-body">
        @if($menuItem->image)
            <div class="mb-3">
                <label class="form-label">Current Image</label><br>
                <img src="{{ $menuItem->image_url }}" alt="{{ $menuItem->name }}" style="max-width: 200px; border-radius: 5px;">
            </div>
        @endif
        <form action="{{ route('admin.menu-items.update', $menuItem->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="name" class="form-label">Item Name *</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $menuItem->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description', $menuItem->description) }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="price" class="form-label">Price (N$) *</label>
                    <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price', $menuItem->price) }}" required>
                    @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="unit_label" class="form-label">Unit</label>
                    <input type="text" class="form-control @error('unit_label') is-invalid @enderror" id="unit_label" name="unit_label" value="{{ old('unit_label', $menuItem->unit_label) }}" placeholder="e.g. per person, per pack">
                    @error('unit_label')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="serves_count" class="form-label">Serves (people)</label>
                    <input type="number" min="1" class="form-control @error('serves_count') is-invalid @enderror" id="serves_count" name="serves_count" value="{{ old('serves_count', $menuItem->serves_count) }}">
                    @error('serves_count')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="stock" class="form-label">Stock Quantity *</label>
                    <input type="number" class="form-control @error('stock') is-invalid @enderror" id="stock" name="stock" value="{{ old('stock', $menuItem->stock) }}" required>
                    @error('stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="category" class="form-label">Category</label>
                    <input type="text" class="form-control @error('category') is-invalid @enderror" id="category" name="category" value="{{ old('category', $menuItem->category) }}" placeholder="e.g., Individual Meals, Sharing Platters">
                    @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Item Image (Leave empty to keep current)</label>
                <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                <div class="form-text">JPG, PNG, GIF or WebP. Max 6MB. High‑quality images supported.</div>
                @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', $menuItem->is_active) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">Active (item will be visible to customers)</label>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured" value="1" {{ old('is_featured', $menuItem->is_featured) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_featured">Featured (item will appear on homepage)</label>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Update Menu Item</button>
                <a href="{{ route('admin.menu-items.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
