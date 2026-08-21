@extends('admin.layout')

@section('title', 'Promotion Catalog')
@section('sidebar_active', 'promotion')

@section('content')
<h1 class="mb-4">Promotion Catalog</h1>

<div class="card">
    <div class="card-body">
        <p class="text-muted">Upload a PDF catalog for your promotions and specials. It will be shown on the public <a href="{{ route('promotion') }}" target="_blank">Promotions</a> page.</p>

        <form action="{{ route('admin.promotion.update') }}" method="POST" enctype="multipart/form-data" onsubmit="var r=document.getElementById('remove_catalog'); return !r || !r.checked || confirm('Remove the promotion catalog?');">
            @csrf
            <div class="mb-4">
                <label class="form-label">Current catalog</label>
                <div>
                    @if($pdfPath)
                        <a href="{{ asset('storage/' . $pdfPath) }}" target="_blank" class="d-inline-flex align-items-center gap-2">
                            <i class="bi bi-file-earmark-pdf text-danger"></i> View current PDF
                        </a>
                    @else
                        <span class="text-muted">No catalog uploaded yet.</span>
                    @endif
                </div>
            </div>
            <div class="mb-3">
                <label for="catalog" class="form-label">Upload PDF catalog</label>
                <input type="file" class="form-control @error('catalog') is-invalid @enderror" id="catalog" name="catalog" accept=".pdf,application/pdf">
                <div class="form-text">PDF only. Max 20MB.</div>
                @error('catalog')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            @if($pdfPath)
                <div class="mb-4 form-check">
                    <input type="checkbox" class="form-check-input" id="remove_catalog" name="remove_catalog" value="1">
                    <label class="form-check-label" for="remove_catalog">Remove current catalog</label>
                </div>
            @endif
            <div class="d-flex flex-wrap gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save</button>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
                <a href="{{ route('promotion') }}" target="_blank" class="btn btn-outline-secondary"><i class="bi bi-box-arrow-up-right"></i> View Promotion Page</a>
            </div>
        </form>
    </div>
</div>
@endsection
