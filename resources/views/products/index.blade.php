@extends('layouts.app')

@php
    $seoPage = 'products';
    $structuredData = [
        \App\Services\SeoService::generateStructuredData('website'),
    ];
    
    $breadcrumbItems = [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => route('home')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Products', 'item' => route('products.index')],
    ];
    $structuredData[] = \App\Services\SeoService::generateStructuredData('breadcrumb', ['items' => $breadcrumbItems]);
@endphp

@section('title', 'Buy Fresh Flowers Online in Namibia | /Namsa Florals Flower Shop')

@push('styles')
<style>
    .search-results-bar { margin-bottom: 20px; padding: 15px; background: white; border-radius: 8px; }
    .search-clear-link { color: var(--primary-color); text-decoration: none; margin-left: 10px; }
    .search-clear-link:hover { text-decoration: underline; }
    @media (max-width: 400px) {
        .search-results-bar { padding: 12px; margin-bottom: 16px; font-size: 0.9rem; }
        .search-clear-link { display: inline-block; margin-left: 0; margin-top: 6px; }
    }
</style>
@endpush

@section('content')
@php
    $breadcrumbs = [
        ['name' => 'Home', 'url' => route('home')],
        ['name' => 'Products', 'url' => route('products.index')],
    ];
@endphp
@include('components.breadcrumbs', ['breadcrumbs' => $breadcrumbs])

<div class="products-container">
    @if(request('search'))
        <div class="search-results-bar">
            <p class="mb-0">Search results for: <strong>"{{ request('search') }}"</strong>
                <a href="{{ route('products.index') }}" class="search-clear-link">Clear search</a>
            </p>
        </div>
    @endif
    <div class="products-grid">
        @forelse($products as $product)
            <div class="product-card">
                <a href="{{ route('products.show', $product->id) }}">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" class="product-image" alt="{{ $product->name }} - Buy Fresh Flowers Online Namibia" title="{{ $product->name }}">
                    @else
                        <img src="https://via.placeholder.com/300x280?text={{ urlencode($product->name) }}" class="product-image" alt="{{ $product->name }} - Buy Fresh Flowers Online Namibia" title="{{ $product->name }}">
                    @endif
                </a>
                <div class="product-info">
                    <div class="product-name">{{ $product->name }}</div>
                    <div class="product-price">N$ {{ number_format($product->price, 2) }}</div>
                    @if($product->stock > 0)
                        <form action="{{ route('cart.add') }}" method="POST" data-add-to-cart>
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="buy-now-btn" style="background: #333; color: white;">Buy Now</button>
                        </form>
                    @else
                        <button class="buy-now-btn" disabled style="background: #f0f0f0; color: #999;">Out of Stock</button>
                    @endif
                </div>
            </div>
        @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px;">
                <p style="font-size: 18px; color: #666;">No products available at the moment.</p>
            </div>
        @endforelse
    </div>

    @if($products->hasPages())
        <div style="margin-top: 40px; text-align: center;">
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection
