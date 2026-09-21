@extends('layouts.app')

@php
    $seoPage = 'product';
    $seoData = [
        'name' => $menuItem->name,
        'description' => $menuItem->description ?? $menuItem->name . ' - Home-style catering from Kaleni Catering Services, Windhoek. Order online or book us for your event.',
        'image_url' => $menuItem->image_url,
        'price' => $menuItem->price,
        'stock' => $menuItem->stock,
        'url' => route('menu.show', $menuItem->id),
    ];

    $structuredData = [
        \App\Services\SeoService::generateStructuredData('product', [
            'name' => $menuItem->name,
            'description' => $menuItem->description ?? '',
            'image_url' => $menuItem->image_url,
            'price' => $menuItem->price,
            'stock' => $menuItem->stock,
            'url' => route('menu.show', $menuItem->id),
        ]),
    ];

    $breadcrumbItems = [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => route('home')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Menu', 'item' => route('menu.index')],
        ['@type' => 'ListItem', 'position' => 3, 'name' => $menuItem->name, 'item' => route('menu.show', $menuItem->id)],
    ];
    $structuredData[] = \App\Services\SeoService::generateStructuredData('breadcrumb', ['items' => $breadcrumbItems]);
@endphp

@section('title', $menuItem->name . ' - Kaleni Catering Services')

@push('styles')
<style>
    .product-detail-page { max-width: 1240px; padding-inline: 24px; }
    .product-detail-image-wrap {
        position: sticky;
        top: 125px;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        background: #f4ece7;
        border: 1px solid var(--border);
        border-radius: 24px;
        box-shadow: var(--shadow-sm);
    }
    .product-detail-page .product-detail-image {
        width: 100%;
        aspect-ratio: 1 / 1.04;
        height: auto;
        object-fit: cover;
        display: block;
    }
    .product-detail-shell {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(340px, .82fr);
        gap: clamp(38px, 7vw, 90px);
        align-items: start;
    }
    .product-detail-copy { padding-top: 16px; }
    .product-category-label {
        display: inline-flex;
        margin-bottom: 14px;
        padding: 7px 10px;
        color: var(--primary-dark);
        background: var(--primary-soft);
        border-radius: 999px;
        font-size: .69rem;
        font-weight: 800;
        letter-spacing: .06em;
        text-transform: uppercase;
    }
    .product-detail-title {
        margin-bottom: 12px;
        font-size: clamp(2rem, 4vw, 3.5rem);
        font-weight: 800;
        line-height: 1.08;
    }
    .product-detail-price {
        margin-bottom: 22px;
        color: var(--primary-dark);
        font-size: 1.4rem;
        font-weight: 800;
    }
    .product-stock {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 7px 10px;
        border-radius: 999px;
        font-size: .73rem;
        font-weight: 800;
    }
    .product-stock-in { color: #176741; background: #e8f6ef; }
    .product-stock-out { color: #8f241a; background: #f7ece0; }
    .product-stock-dot { width: 7px; height: 7px; background: currentColor; border-radius: 50%; }
    .product-description {
        margin: 22px 0;
        color: var(--muted);
        font-size: 1rem;
        line-height: 1.8;
        white-space: pre-line;
    }
    .product-order-panel {
        margin-top: 26px;
        padding: 20px;
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 16px;
        box-shadow: var(--shadow-sm);
    }
    .product-order-row { display: grid; grid-template-columns: 92px 1fr; align-items: end; gap: 12px; }
    .product-order-row .buy-now-btn { width: 100%; min-height: 48px; }
    .product-order-row .form-control { width: 100%; }
    .product-detail-meta {
        margin-top: 18px;
        padding-top: 18px;
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
        border-top: 1px solid var(--border);
    }
    .product-detail-meta span { display: flex; align-items: center; gap: 8px; color: var(--muted); font-size: .75rem; font-weight: 650; }
    .product-detail-meta i { color: var(--primary-color); }
    .product-back-link { margin-top: 20px; }
    .recommendations-section { margin-top: 84px; }
    @media (max-width: 768px) {
        .product-detail-page { padding-inline: 16px; }
        .product-detail-shell { grid-template-columns: 1fr; gap: 28px; }
        .product-detail-image-wrap { position: static; }
        .product-detail-copy { padding-top: 0; }
        .recommendations-section { margin-top: 58px; }
    }
    @media (max-width: 400px) {
        .product-order-row { grid-template-columns: 1fr; }
        .product-detail-meta { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
@php
    $breadcrumbs = [
        ['name' => 'Home', 'url' => route('home')],
        ['name' => 'Menu', 'url' => route('menu.index')],
        ['name' => $menuItem->name, 'url' => route('menu.show', $menuItem->id)],
    ];
@endphp
@include('components.breadcrumbs', ['breadcrumbs' => $breadcrumbs])

<div class="container my-5 product-detail-page">
    <div class="product-detail-shell">
        <div class="product-detail-gallery">
            <div class="product-detail-image-wrap">
                <x-menu-item-image :menu-item="$menuItem" img-class="product-detail-image" alt="{{ $menuItem->name }} - Kaleni Catering Services" />
            </div>
        </div>
        <div class="product-detail-copy">
            @if($menuItem->category)
                <span class="product-category-label">{{ $menuItem->category }}</span>
            @endif
            <h1 class="product-detail-title">{{ $menuItem->name }}</h1>
            <p class="product-detail-price">
                N$ {{ number_format($menuItem->price, 2) }}{{ $menuItem->unit_label ? ' / '.$menuItem->unit_label : '' }}
                @if($menuItem->serves_count) <span class="text-muted fs-6 fw-normal">&middot; serves {{ $menuItem->serves_count }}</span> @endif
            </p>

            @if($menuItem->stock > 0)
                <span class="product-stock product-stock-in"><span class="product-stock-dot"></span>{{ $menuItem->stock }} available</span>
            @else
                <span class="product-stock product-stock-out"><span class="product-stock-dot"></span>Out of stock</span>
            @endif

            <p class="product-description">{{ $menuItem->description ?: 'A home-style dish, made fresh by Kaleni Catering Services.' }}</p>

            @if($menuItem->stock > 0)
                <form action="{{ route('cart.add') }}" method="POST" class="product-order-panel" data-add-to-cart>
                    @csrf
                    <input type="hidden" name="menu_item_id" value="{{ $menuItem->id }}">
                    <div class="product-order-row">
                        <div class="quantity-input-wrap">
                            <label for="quantity" class="form-label">Quantity</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" value="1" min="1" max="{{ $menuItem->stock }}">
                        </div>
                        <button type="submit" class="buy-now-btn">
                            <i class="bi bi-bag-plus" aria-hidden="true"></i> Add to order
                        </button>
                    </div>
                    <div class="product-detail-meta">
                        <span><i class="bi bi-truck" aria-hidden="true"></i> Free Windhoek delivery</span>
                        <span><i class="bi bi-egg-fried" aria-hidden="true"></i> Cooked fresh to order</span>
                    </div>
                </form>
            @else
                <a href="{{ route('contact') }}" class="buy-now-btn mt-4 text-decoration-none"><i class="bi bi-chat-dots" aria-hidden="true"></i> Ask about availability</a>
            @endif

            <a href="{{ route('menu.index') }}" class="text-link product-back-link">
                <i class="bi bi-arrow-left" aria-hidden="true"></i> Back to full menu
            </a>
        </div>
    </div>

    @if($recommendations->count() > 0)
        <section class="recommendations-section" aria-labelledby="recommendations-title">
            <div class="section-heading">
                <div>
                    <span class="section-kicker">More to love</span>
                    <h2 class="section-title" id="recommendations-title">Often ordered together</h2>
                </div>
            </div>
            <div class="products-grid">
                @foreach($recommendations as $recommended)
                    @include('menu._card', ['item' => $recommended])
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection
