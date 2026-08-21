@extends('layouts.app')

@php
    $seoPage = 'gallery';
    $structuredData = [
        \App\Services\SeoService::generateStructuredData('website'),
    ];
    $breadcrumbItems = [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => route('home')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Gallery', 'item' => route('gallery')],
    ];
    $structuredData[] = \App\Services\SeoService::generateStructuredData('breadcrumb', ['items' => $breadcrumbItems]);
@endphp

@section('title', 'Gallery – Flowers, Experiences & More | /Namsa Florals')

@section('content')
@php
    $breadcrumbs = [
        ['name' => 'Home', 'url' => route('home')],
        ['name' => 'Gallery', 'url' => route('gallery')],
    ];
@endphp
@include('components.breadcrumbs', ['breadcrumbs' => $breadcrumbs])

<div class="products-container">
    <div class="text-center mb-4">
        <h1 class="mb-2" style="color: #111;">Gallery</h1>
        <p class="lead text-muted" style="color: #555;">Flowers, client moments & experiences</p>
    </div>
    <div class="gallery-grid products-grid">
        @forelse($items as $item)
            <div class="gallery-card product-card">
                @if($item->image)
                    <img src="{{ asset('storage/' . $item->image) }}" class="gallery-image product-image" alt="{{ $item->title ?: 'Gallery image' }}" loading="lazy">
                @else
                    <div class="gallery-image product-image gallery-placeholder">
                        <i class="bi bi-image"></i>
                    </div>
                @endif
                <div class="product-info gallery-info">
                    @if($item->title)
                        <div class="product-name gallery-title">{{ $item->title }}</div>
                    @endif
                    @if($item->description)
                        <p class="gallery-description mb-0">{!! nl2br(e($item->description)) !!}</p>
                    @endif
                </div>
            </div>
        @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px;">
                <p style="font-size: 18px; color: #666;">No gallery items yet. Check back soon.</p>
            </div>
        @endforelse
    </div>
    @if($items->hasPages())
        <div style="margin-top: 40px; text-align: center;">
            {{ $items->links() }}
        </div>
    @endif
</div>

@push('styles')
<style>
    .gallery-card { cursor: default; }
    .gallery-card:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
    .gallery-image { display: block; }
    .gallery-placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f0f0f0;
        color: #999;
        font-size: 2.5rem;
    }
    .gallery-title { margin-bottom: 6px; }
    .gallery-description {
        font-size: 14px;
        line-height: 1.5;
        color: #555;
        text-align: left;
    }
</style>
@endpush
@endsection
