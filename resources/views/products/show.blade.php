@extends('layouts.app')

@php
    $seoPage = 'product';
    $seoData = [
        'name' => $product->name,
        'description' => $product->description ?? $product->name . ' - Fresh flowers from /Namsa Florals, Namibia\'s premier flower shop. Order online with same-day delivery in Windhoek.',
        'image' => $product->image,
        'price' => $product->price,
        'stock' => $product->stock,
        'url' => route('products.show', $product->id),
    ];
    
    $structuredData = [
        \App\Services\SeoService::generateStructuredData('product', [
            'name' => $product->name,
            'description' => $product->description ?? '',
            'image' => $product->image ? asset('storage/' . $product->image) : '',
            'price' => $product->price,
            'stock' => $product->stock,
            'url' => route('products.show', $product->id),
        ]),
    ];
    
    $breadcrumbItems = [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => route('home')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Products', 'item' => route('products.index')],
        ['@type' => 'ListItem', 'position' => 3, 'name' => $product->name, 'item' => route('products.show', $product->id)],
    ];
    $structuredData[] = \App\Services\SeoService::generateStructuredData('breadcrumb', ['items' => $breadcrumbItems]);
@endphp

@section('title', $product->name . ' - Buy Online | /Namsa Florals Namibia')

@push('styles')
<style>
    .product-detail-image-wrap {
        max-width: 100%;
        max-height: 70vh;
        display: flex;
        align-items: flex-start;
        justify-content: center;
        margin-bottom: 1rem;
    }
    .product-detail-page .product-detail-image {
        max-width: 100%;
        max-height: 70vh;
        width: auto;
        height: auto;
        object-fit: contain;
        display: block;
    }
    @media (max-width: 768px) {
        .product-detail-page .display-5 { font-size: 1.5rem; }
        .product-detail-page .h3 { font-size: 1.25rem; }
        .product-detail-image-wrap,
        .product-detail-page .product-detail-image { max-height: 55vh; }
    }
    @media (max-width: 400px) {
        .product-detail-page .display-5 { font-size: 1.35rem; }
        .product-detail-page .quantity-input-wrap input { max-width: 100% !important; }
        .product-detail-page .btn-back { max-width: 100%; text-align: center; }
        .product-detail-image-wrap,
        .product-detail-page .product-detail-image { max-height: 50vh; }
    }
</style>
@endpush

@section('content')
@php
    $breadcrumbs = [
        ['name' => 'Home', 'url' => route('home')],
        ['name' => 'Products', 'url' => route('products.index')],
        ['name' => $product->name, 'url' => route('products.show', $product->id)],
    ];
@endphp
@include('components.breadcrumbs', ['breadcrumbs' => $breadcrumbs])

<div class="container my-5 product-detail-page">
    <div class="row">
        <div class="col-md-6">
            <div class="product-detail-image-wrap">
                @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" class="product-detail-image img-fluid rounded" alt="{{ $product->name }} - Fresh Flowers from /Namsa Florals Namibia" title="{{ $product->name }}">
                @else
                    <img src="https://via.placeholder.com/600x500?text={{ urlencode($product->name) }}" class="product-detail-image img-fluid rounded" alt="{{ $product->name }} - Fresh Flowers from /Namsa Florals Namibia" title="{{ $product->name }}">
                @endif
            </div>
        </div>
        <div class="col-md-6">
            <h1 class="display-5">{{ $product->name }}</h1>
            <p class="h3 mb-4" style="color: #333;">N$ {{ number_format($product->price, 2) }}</p>
            
            <div class="mb-4">
                <p>{{ $product->description }}</p>
                <p><strong>Category:</strong> {{ $product->category ?? 'Uncategorized' }}</p>
                <p><strong>Stock:</strong> 
                    @if($product->stock > 0)
                        <span class="badge bg-success">{{ $product->stock }} available</span>
                    @else
                        <span class="badge bg-danger">Out of Stock</span>
                    @endif
                </p>
            </div>

            @if($product->stock > 0)
                <form action="{{ route('cart.add') }}" method="POST" class="mb-4" data-add-to-cart>
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <div class="mb-3 quantity-input-wrap">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" value="1" min="1" max="{{ $product->stock }}" style="max-width: 100px;">
                    </div>
                    <button type="submit" class="buy-now-btn" style="background: #333; color: white; padding: 15px 30px; font-size: 1.1rem;">
                        Add to Cart
                    </button>
                </form>
            @else
                <button class="btn btn-secondary btn-lg" disabled>Out of Stock</button>
            @endif

            <a href="{{ route('products.index') }}" class="buy-now-btn btn-back" style="background: #666; color: white; max-width: 200px; text-decoration: none; display: inline-block; margin-top: 15px;">
                Back to Products
            </a>
        </div>
    </div>

    @if($recommendations->count() > 0)
        <div class="row mt-5">
            <div class="col-12">
                <h3 class="mb-4">You May Also Like</h3>
                <div class="row">
                    @foreach($recommendations as $recommended)
                        <div class="col-md-3 mb-4">
                            <div class="card product-card h-100">
                                @if($recommended->image)
                                    <img src="{{ asset('storage/' . $recommended->image) }}" class="product-image" alt="{{ $recommended->name }} - Related Flowers Namibia" title="{{ $recommended->name }}">
                                @else
                                    <img src="https://via.placeholder.com/300x250?text={{ urlencode($recommended->name) }}" class="product-image" alt="{{ $recommended->name }} - Related Flowers Namibia" title="{{ $recommended->name }}">
                                @endif
                                <div class="card-body d-flex flex-column">
                                    <h6 class="card-title">{{ $recommended->name }}</h6>
                                    <p class="h5 mb-3" style="color: #333;">N$ {{ number_format($recommended->price, 2) }}</p>
                                    <a href="{{ route('products.show', $recommended->id) }}" class="buy-now-btn" style="background: #333; color: white; text-decoration: none; display: block; text-align: center; font-size: 0.9rem; padding: 8px;">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
