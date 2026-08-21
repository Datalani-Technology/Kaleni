@extends('admin.layout')

@section('title', 'Add Gallery Item')
@section('sidebar_active', 'gallery')

@section('content')
<h1 class="mb-4">Add Gallery Item</h1>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.gallery.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="image" class="form-label">Image *</label>
                <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*" required>
                <div class="form-text">JPG, PNG, GIF or WebP. Max 6MB. Same proportions as product photos work best.</div>
                @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" placeholder="e.g. Client order, Event bouquet">
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" maxlength="2000" placeholder="e.g. Experience, vlog-style note, client moment...">{{ old('description') }}</textarea>
                <div class="form-text">Brief caption. Max 2000 characters.</div>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="d-flex flex-wrap gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Add Item</button>
                <a href="{{ route('admin.gallery.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
