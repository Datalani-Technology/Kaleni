@extends('layouts.app')

@php
    $seoPage = 'promotion';
@endphp

@section('content')
<div class="container my-5 promotion-page">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <h1 class="mb-4"><i class="bi bi-tag-fill" style="color: var(--primary-color);"></i> Promotions &amp; Specials</h1>

            <div class="card">
                <div class="card-body p-5">
                    @if($pdfPath)
                        <p class="lead text-muted mb-4">Browse our current specials and promotions. View or download the catalog below.</p>
                        <div class="d-flex flex-wrap gap-3 mb-4">
                            <a href="{{ asset('storage/' . $pdfPath) }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary">
                                <i class="bi bi-file-earmark-pdf"></i> View / Download PDF Catalog
                            </a>
                        </div>
                        <div class="ratio ratio-16x9 bg-light rounded overflow-hidden" style="min-height: 480px;">
                            <iframe src="{{ asset('storage/' . $pdfPath) }}#toolbar=1" title="Promotion catalog" class="border-0"></iframe>
                        </div>
                    @else
                        <p class="lead text-muted mb-0">No promotion catalog is available at the moment. Check back soon for our latest specials!</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .promotion-page .card { border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
    @media (max-width: 768px) {
        .promotion-page .card-body { padding: 1.5rem !important; }
        .promotion-page .ratio { min-height: 320px !important; }
    }
    @media (max-width: 400px) {
        .promotion-page h1 { font-size: 1.5rem; }
        .promotion-page .card-body { padding: 1.25rem !important; }
    }
</style>
@endpush
@endsection
