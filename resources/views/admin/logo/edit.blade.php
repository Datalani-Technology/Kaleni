@extends('admin.layout')

@section('title', 'Update Logo')
@section('sidebar_active', 'logo')

@push('styles')
<style>.logo-preview { max-height: 80px; width: auto; object-fit: contain; }</style>
@endpush

@section('content')
<h1 class="mb-4">Update Logo</h1>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.logo.update') }}" method="POST" enctype="multipart/form-data" data-confirm-mode="remove-logo">
            @csrf
            <div class="mb-4">
                <label class="form-label">Current logo</label>
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    @if($logoPath)
                        <img src="{{ asset('storage/' . $logoPath) }}" alt="Logo" class="logo-preview border rounded p-1 bg-light">
                        <span class="text-muted small">Custom image</span>
                    @else
                        <span class="text-muted d-inline-flex align-items-center gap-2">
                            <img src="{{ asset('images/kaleni/brand/kaleni-logo.jpg') }}" alt="Default logo" style="height: 28px; width: auto; border-radius: 4px;">
                            {{ $logoText }} (default logo)
                        </span>
                    @endif
                </div>
            </div>
            <div class="mb-3">
                <label for="logo" class="form-label">Upload new logo</label>
                <input type="file" class="form-control @error('logo') is-invalid @enderror" id="logo" name="logo" accept="image/*">
                <div class="form-text">PNG, JPG, GIF, SVG or WebP. Max 6MB. Recommended height ~40px.</div>
                @error('logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="logo_text" class="form-label">Logo text</label>
                <input type="text" class="form-control @error('logo_text') is-invalid @enderror" id="logo_text" name="logo_text" value="{{ old('logo_text', $logoText) }}" placeholder="Kaleni Catering Services" maxlength="100">
                <div class="form-text">Shown as the alt text/label alongside the logo image.</div>
                @error('logo_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            @if($logoPath)
                <div class="mb-4 form-check">
                    <input type="checkbox" class="form-check-input" id="remove_logo" name="remove_logo" value="1">
                    <label class="form-check-label" for="remove_logo">Remove custom logo (revert to icon + text)</label>
                </div>
            @endif
            <div class="d-flex flex-wrap gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save</button>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
            </div>
        </form>
    </div>
</div>
@endsection
