@extends('layouts.app')

@php
    $seoPage = 'home';
    $structuredData = [
        \App\Services\SeoService::generateStructuredData('website'),
        \App\Services\SeoService::generateStructuredData('organization'),
        \App\Services\SeoService::generateStructuredData('webpage_home'),
    ];
@endphp

@section('content')
<!-- Hero: Client message -->
<section class="home-hero-section">
    <div class="container my-5 home-hero">
        <div class="row justify-content-center mb-5">
            <div class="col-md-10">
                <div class="card home-hero-card">
                <div class="card-body p-5 text-center">
                    <h1 class="display-4 mb-4" style="color: #111;">
                        Hey there :) We're so glad you're here!
                    </h1>
                    <div class="row mt-4">
                        <div class="col-md-8 mx-auto text-start">
                            <p style="font-size: 1.1rem; line-height: 1.8; color: #333;">
                                At <strong>/Namsa Florals</strong>, we don't just sell flowers… we create <strong>moments that matter</strong>. Whether you're celebrating love, lifting someone's spirit, saying "thank you," or marking milestones big and small — we're here to help you do it with intention, beauty, and soul. <strong>Fresh flowers</strong>, <strong>same-day delivery in Windhoek</strong> — Namibia's leading <strong>online flower shop</strong>.
                            </p>
                            <p style="font-size: 1.1rem; line-height: 1.8; color: #333; margin-top: 20px;">
                                We create more than bouquets. From perfectly <strong>hand-picked blooms</strong> to our signature custom poster messages, every order is made to feel intentional, meaningful, and truly yours. <strong>Order flowers online in Namibia</strong> with confidence — whether it's a quiet gesture or a bold celebration, say it with /Namsa: beautifully, personally, and from the heart.
                            </p>
                            <p class="text-center mt-4 mb-4" style="font-size: 1.15rem; font-weight: 600; color: #111;">
                                Explore. Personalize. Send the feeling. 💐
                            </p>
                            <div class="mt-4 btn-group-mobile d-flex flex-wrap justify-content-center gap-2">
                                <a href="{{ route('products.index') }}" class="buy-now-btn" style="background: #111; color: #fff; max-width: 250px; margin: 0 auto; display: inline-block; text-decoration: none;">
                                    Browse All Products
                                </a>
                                <a href="{{ route('contact') }}" class="buy-now-btn" style="background: #333; color: #fff; max-width: 200px; margin: 0 auto; display: inline-block; text-decoration: none;">
                                    Contact Us
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="container my-4">
    <!-- Featured Products Section -->
    @if($featuredProducts->count() > 0)
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h2 class="display-5 mb-2" style="color: #111;">Featured Products</h2>
                <p class="lead text-muted" style="color: #555;">Handpicked selections for you</p>
            </div>
        </div>

        <div class="products-grid">
            @foreach($featuredProducts as $product)
                <div class="product-card">
                    <a href="{{ route('products.show', $product->id) }}">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" class="product-image" alt="{{ $product->name }} - Featured Flower from /Namsa Florals Namibia" title="{{ $product->name }}">
                        @else
                            <img src="https://via.placeholder.com/300x280?text={{ urlencode($product->name) }}" class="product-image" alt="{{ $product->name }} - Featured Flower from /Namsa Florals Namibia" title="{{ $product->name }}">
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
                            <button type="submit" class="buy-now-btn" style="background: #111; color: #fff;">Buy Now</button>
                        </form>
                    @else
                        <button class="buy-now-btn" disabled style="background: #f0f0f0; color: #999;">Out of Stock</button>
                    @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row mt-5">
            <div class="col-12 text-center">
                <a href="{{ route('products.index') }}" class="buy-now-btn" style="background: #111; color: #fff; max-width: 300px; margin: 0 auto; display: block; text-decoration: none;">
                    View All Products
                </a>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-12 text-center py-5">
                <p class="lead text-muted" style="color: #555;">No featured products available at the moment.</p>
                <a href="{{ route('products.index') }}" class="buy-now-btn" style="background: #111; color: #fff; max-width: 250px; margin: 0 auto; display: block; text-decoration: none;">
                    Browse All Products
                </a>
            </div>
        </div>
    @endif
</div>

<style>
    .home-hero-section {
        background-image: url('https://images.unsplash.com/photo-1490750967868-88aa4486c946?w=1200');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        position: relative;
        padding: 1rem 0;
    }
    @media (max-width: 768px) {
        .home-hero-section { padding: 0.75rem 0; }
    }
    .home-hero-section::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(to bottom, rgba(255,255,255,0.88), rgba(255,255,255,0.92));
        pointer-events: none;
    }
    .home-hero-section .container { position: relative; z-index: 1; }
    .home-hero-card {
        background: #fff !important;
        border: 2px solid #ddd !important;
        box-shadow: 0 4px 24px rgba(0,0,0,0.12) !important;
    }
    .home-hero h1 { color: #111 !important; }
    .products-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 30px;
        margin-top: 30px;
    }

    @media (max-width: 1024px) {
        .products-grid { grid-template-columns: repeat(3, 1fr); }
    }
    @media (max-width: 768px) {
        .products-grid { grid-template-columns: repeat(2, 1fr); gap: 20px; }
    }
    @media (max-width: 480px) {
        .products-grid { grid-template-columns: 1fr; }
    }

    /* Mobile: hero & card */
    @media (max-width: 768px) {
        .home-hero .display-4 { font-size: 1.75rem; }
        .home-hero .lead { font-size: 1rem; }
        .home-hero .card-body { padding: 1.5rem !important; }
        .home-hero p { font-size: 0.95rem !important; }
        .home-hero .btn-group-mobile { flex-direction: column; gap: 10px; }
        .home-hero .btn-group-mobile .buy-now-btn { max-width: 100%; margin: 0 !important; }
    }
    @media (max-width: 400px) {
        .home-hero .display-4 { font-size: 1.5rem; }
        .home-hero .lead { font-size: 0.95rem; }
        .home-hero .card-body { padding: 1.25rem !important; }
        .home-hero p { font-size: 0.9rem !important; }
    }
    @media (max-width: 360px) {
        .home-hero .display-4 { font-size: 1.35rem; }
        .home-hero .lead { font-size: 0.9rem; }
    }
</style>
@endsection
