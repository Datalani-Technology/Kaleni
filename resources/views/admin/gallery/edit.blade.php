@extends('admin.layout')

@section('title', 'Edit Gallery Item')
@section('sidebar_active', 'gallery')

@section('content')
<h1 class="mb-4">Edit Gallery Item</h1>

<div class="card">
    <div class="card-body">
        @if($item->image)
            <div class="mb-3">
                <label class="form-label">Current Image</label><br>
                <img src="{{ $item->image_url }}" alt="" style="max-width: 200px; max-height: 200px; object-fit: cover; border-radius: 8px;">
            </div>
        @endif
        <form action="{{ route('admin.gallery.update', $item) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="image" class="form-label">Image (leave empty to keep current)</label>
                <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                <div class="form-text">JPG, PNG, GIF or WebP. Max 6MB.</div>
                @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $item->title) }}" placeholder="e.g. Braai platter, Event spread">
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" maxlength="2000">{{ old('description', $item->description) }}</textarea>
                <div class="form-text">Max 2000 characters.</div>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="occasion" class="form-label">Occasion</label>
                <select class="form-select @error('occasion') is-invalid @enderror" id="occasion" name="occasion">
                    <option value="">Not tied to an occasion (shows in "All" only)</option>
                    @foreach(\App\Models\GalleryItem::OCCASIONS as $occasion)
                        <option value="{{ $occasion }}" {{ old('occasion', $item->occasion) === $occasion ? 'selected' : '' }}>{{ $occasion }}</option>
                    @endforeach
                </select>
                <div class="form-text">Lets visitors filter the gallery by event type. Only pick an occasion this photo genuinely represents.</div>
                @error('occasion')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="d-flex flex-wrap gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Update</button>
                <a href="{{ route('admin.gallery.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
