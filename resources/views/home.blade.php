@extends('layouts.app')

@php
    $seoPage = 'home';
    $structuredData = [
        \App\Services\SeoService::generateStructuredData('website'),
        \App\Services\SeoService::generateStructuredData('organization'),
        \App\Services\SeoService::generateStructuredData('webpage_home'),
    ];
    $heroProducts = $featuredProducts->filter(fn ($product) => filled($product->image))->take(6)->values();
    $heroProduct = $heroProducts->first() ?? $featuredProducts->first();
@endphp

@push('styles')
<style>
    .home-hero-section {
        position: relative;
        padding: clamp(48px, 7vw, 92px) 0 76px;
        overflow: hidden;
        background:
            radial-gradient(circle at 12% 18%, rgba(180,35,99,.10), transparent 27rem),
            radial-gradient(circle at 92% 4%, rgba(215,165,65,.11), transparent 25rem),
            linear-gradient(180deg, #fff 0%, #fcf6f8 100%);
    }
    .home-hero-section::before {
        content: '';
        position: absolute;
        top: -190px;
        right: -175px;
        width: 510px;
        height: 510px;
        border: 82px solid rgba(180,35,99,.045);
        border-radius: 50%;
        pointer-events: none;
    }
    .home-hero {
        position: relative;
        z-index: 1;
        width: min(1240px, calc(100% - 48px));
        margin: 0 auto;
        display: grid;
        grid-template-columns: minmax(0, 1.05fr) minmax(360px, .82fr);
        align-items: center;
        gap: clamp(46px, 7vw, 100px);
    }
    .home-hero-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 9px;
        margin-bottom: 20px;
        color: var(--primary-dark);
        font-size: .73rem;
        font-weight: 800;
        letter-spacing: .11em;
        text-transform: uppercase;
    }
    .home-hero-eyebrow::before {
        content: '';
        width: 8px;
        height: 8px;
        background: var(--primary-color);
        border-radius: 50%;
        box-shadow: 0 0 0 5px rgba(180,35,99,.10);
    }
    .home-hero-title {
        max-width: 680px;
        margin: 0 0 22px;
        color: var(--ink);
        font-size: clamp(2.55rem, 5.3vw, 5rem);
        font-weight: 800;
        line-height: 1.02;
        letter-spacing: -.06em;
    }
    .home-hero-title span { color: var(--primary-color); }
    .home-hero-subtitle {
        max-width: 590px;
        margin: 0 0 30px;
        color: var(--muted);
        font-size: clamp(1rem, 1.5vw, 1.13rem);
        line-height: 1.75;
    }
    .home-hero-actions {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 11px;
    }
    .home-hero-btn {
        width: auto;
        min-height: 51px;
        padding: 14px 22px;
        text-decoration: none;
    }
    .home-hero-btn-secondary {
        color: var(--ink) !important;
        background: #fff !important;
        border-color: var(--border) !important;
        box-shadow: var(--shadow-sm);
    }
    .home-hero-btn-secondary:hover {
        color: var(--primary-dark) !important;
        background: var(--primary-soft) !important;
        border-color: #ecc6d6 !important;
        box-shadow: var(--shadow-sm);
    }
    .home-hero-note {
        margin-top: 23px;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 9px 18px;
        color: #776d73;
        font-size: .78rem;
        font-weight: 650;
    }
    .home-hero-note span {
        display: inline-flex;
        align-items: center;
        gap: 7px;
    }
    .home-hero-note i { color: var(--success-color); }
    .home-hero-visual {
        position: relative;
        width: min(100%, 520px);
        justify-self: end;
    }
    .home-art-stage {
        position: relative;
        isolation: isolate;
        width: 100%;
        aspect-ratio: 1 / 1.02;
        overflow: hidden;
        background:
            radial-gradient(circle at 50% 45%, rgba(241,120,172,.23) 0, rgba(180,35,99,.09) 34%, transparent 60%),
            linear-gradient(145deg, #2b1823 0%, #160f14 70%, #2c1722 100%);
        border: 1px solid rgba(255,255,255,.1);
        border-radius: 46% 54% 48% 52% / 52% 43% 57% 48%;
        box-shadow:
            0 38px 80px rgba(40,19,30,.24),
            inset 0 0 80px rgba(232,74,142,.08);
    }
    .home-art-stage::before,
    .home-art-stage::after {
        content: '';
        position: absolute;
        border-radius: 50%;
        pointer-events: none;
    }
    .home-art-stage::before {
        z-index: -1;
        inset: 9%;
        border: 1px solid rgba(255,255,255,.12);
        box-shadow:
            0 0 0 28px rgba(255,255,255,.025),
            0 0 0 58px rgba(255,255,255,.018),
            0 0 85px rgba(232,74,142,.22);
        animation: homeArtPulse 4.8s ease-in-out infinite;
    }
    .home-art-stage::after {
        z-index: 5;
        inset: 0;
        opacity: .2;
        background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 180 180' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.9' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='.17'/%3E%3C/svg%3E");
        mix-blend-mode: soft-light;
    }
    .home-art-word {
        position: absolute;
        z-index: 0;
        top: 50%;
        left: 50%;
        color: rgba(255,255,255,.045);
        font-size: clamp(4.7rem, 8vw, 7.6rem);
        font-weight: 800;
        letter-spacing: -.075em;
        line-height: 1;
        transform: translate(-50%, -50%) rotate(-8deg);
        user-select: none;
    }
    .home-art-index {
        position: absolute;
        z-index: 7;
        top: 9%;
        left: 9%;
        color: rgba(255,255,255,.7);
        font-size: .62rem;
        font-weight: 800;
        letter-spacing: .16em;
        text-transform: uppercase;
    }
    .home-hero-slide {
        position: absolute;
        z-index: 2;
        inset: 5.5%;
        display: block;
        opacity: 0;
        pointer-events: none;
        text-decoration: none;
        transition: opacity .85s ease;
    }
    .home-hero-slide.is-active {
        z-index: 3;
        opacity: 1;
        pointer-events: auto;
    }
    .home-hero-bloom {
        position: absolute;
        inset: 0;
        display: grid;
        place-items: center;
        transform-origin: 50% 54%;
    }
    .home-hero-slide.is-active .home-hero-bloom {
        animation: homeFlowerBloom 5.6s cubic-bezier(.17,.84,.25,1) both;
    }
    .home-hero-bloom::before {
        content: '';
        position: absolute;
        z-index: -1;
        inset: 15%;
        background: radial-gradient(circle, rgba(255,112,178,.52), rgba(180,35,99,.12) 45%, transparent 70%);
        border-radius: 50%;
        filter: blur(20px);
        opacity: .8;
        animation: homeFlowerGlow 2.8s ease-in-out infinite alternate;
    }
    .home-hero-image {
        width: 100%;
        height: 100%;
        display: block;
        object-fit: cover;
        object-position: center;
        border-radius: 48% 52% 50% 50% / 44% 46% 54% 56%;
        -webkit-mask-image: radial-gradient(ellipse 72% 76% at 50% 50%, #000 61%, rgba(0,0,0,.94) 68%, transparent 74%);
        mask-image: radial-gradient(ellipse 72% 76% at 50% 50%, #000 61%, rgba(0,0,0,.94) 68%, transparent 74%);
        filter: saturate(1.18) contrast(1.04) drop-shadow(0 24px 30px rgba(0,0,0,.32));
    }
    .home-hero-caption {
        position: absolute;
        z-index: 8;
        right: 7%;
        bottom: 7%;
        left: 7%;
        padding: 12px 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        color: #fff;
        background: rgba(22,15,20,.72);
        border: 1px solid rgba(255,255,255,.18);
        border-radius: 13px;
        box-shadow: 0 12px 32px rgba(0,0,0,.25);
        backdrop-filter: blur(14px);
        opacity: 0;
        transform: translateY(12px);
    }
    .home-hero-slide.is-active .home-hero-caption {
        animation: homeCaptionIn .6s 1.05s ease forwards;
    }
    .home-hero-caption small {
        display: block;
        color: #f4a7c7;
        font-size: .63rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
    }
    .home-hero-caption strong {
        display: block;
        overflow: hidden;
        font-size: .88rem;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .home-hero-caption-price {
        flex: 0 0 auto;
        color: #fff;
        font-size: .86rem;
        font-weight: 800;
    }
    .home-hero-carousel-controls {
        position: absolute;
        z-index: 10;
        right: 7%;
        bottom: -24px;
        left: 7%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
    }
    .home-hero-control {
        width: 38px;
        height: 38px;
        padding: 0;
        display: grid;
        place-items: center;
        color: var(--ink);
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 50%;
        box-shadow: 0 9px 22px rgba(46,24,35,.14);
        transition: color .2s ease, background .2s ease, transform .2s ease;
    }
    .home-hero-control:hover {
        color: #fff;
        background: var(--primary-dark);
        transform: translateY(-2px);
    }
    .home-hero-dots {
        min-height: 38px;
        padding: 0 12px;
        display: flex;
        align-items: center;
        gap: 7px;
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 999px;
        box-shadow: 0 9px 22px rgba(46,24,35,.14);
    }
    .home-hero-dot {
        position: relative;
        width: 7px;
        height: 7px;
        padding: 0;
        overflow: hidden;
        background: #d8cbd1;
        border: 0;
        border-radius: 999px;
        transition: width .3s ease, background .3s ease;
    }
    .home-hero-dot.is-active {
        width: 25px;
        background: #efb0cb;
    }
    .home-hero-dot.is-active::after {
        content: '';
        position: absolute;
        inset: 0;
        background: var(--primary-color);
        border-radius: inherit;
        transform-origin: left;
        animation: homeSlideProgress 5.6s linear forwards;
    }
    .home-hero-visual.is-paused .home-hero-dot.is-active::after,
    .home-hero-visual.is-paused .home-art-stage::before,
    .home-hero-visual.is-paused .home-hero-bloom::before,
    .home-hero-visual.is-paused .home-hero-slide.is-active .home-hero-bloom {
        animation-play-state: paused;
    }
    .home-hero-placeholder {
        position: absolute;
        inset: 10%;
        display: grid;
        place-items: center;
        color: #f4a7c7;
        font-size: 6rem;
    }
    @keyframes homeFlowerBloom {
        0% { opacity: 0; transform: scale(.12) rotate(-14deg); filter: blur(14px) brightness(1.75); }
        13% { opacity: .92; }
        38% { opacity: 1; transform: scale(1.035) rotate(1.2deg); filter: blur(0) brightness(1.08); }
        70% { transform: scale(.99) rotate(0); filter: blur(0) brightness(1); }
        100% { opacity: 1; transform: scale(1.025); filter: blur(0) brightness(1); }
    }
    @keyframes homeFlowerGlow {
        from { opacity: .45; transform: scale(.78); }
        to { opacity: .95; transform: scale(1.12); }
    }
    @keyframes homeArtPulse {
        0%, 100% { transform: scale(.96); opacity: .7; }
        50% { transform: scale(1.02); opacity: 1; }
    }
    @keyframes homeCaptionIn {
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes homeSlideProgress {
        from { transform: scaleX(0); }
        to { transform: scaleX(1); }
    }

    .home-trust {
        position: relative;
        z-index: 3;
        width: min(1120px, calc(100% - 48px));
        margin: -34px auto 76px;
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 18px;
        box-shadow: 0 18px 50px rgba(52,27,40,.10);
    }
    .home-trust-item {
        min-width: 0;
        padding: 20px 24px;
        display: flex;
        align-items: center;
        gap: 13px;
    }
    .home-trust-item + .home-trust-item { border-left: 1px solid var(--border); }
    .home-trust-icon {
        width: 42px;
        height: 42px;
        flex: 0 0 42px;
        display: grid;
        place-items: center;
        color: var(--primary-dark);
        background: var(--primary-soft);
        border-radius: 12px;
        font-size: 1.08rem;
    }
    .home-trust-item > span:last-child { min-width: 0; }
    .home-trust-item strong {
        display: block;
        color: var(--ink);
        font-size: .82rem;
        font-weight: 800;
    }
    .home-trust-item > span:last-child > span {
        display: block;
        margin-top: 2px;
        color: var(--muted);
        font-size: .72rem;
    }

    .home-products {
        width: min(1240px, calc(100% - 48px));
        margin: 0 auto;
    }
    .home-products .products-grid { margin-top: 0; }
    .home-products-footer {
        margin-top: 30px;
        display: flex;
        justify-content: center;
    }
    .home-products-footer .buy-now-btn { width: auto; min-width: 210px; text-decoration: none; }

    .home-about {
        width: min(1240px, calc(100% - 48px));
        margin: 84px auto 0;
        padding: clamp(30px, 5vw, 60px);
        display: grid;
        grid-template-columns: .85fr 1.15fr;
        gap: clamp(34px, 7vw, 90px);
        background: #241b21;
        border-radius: 26px;
        box-shadow: 0 26px 65px rgba(45,27,36,.14);
    }
    .home-about .section-kicker { color: #ef9fc1; }
    .home-about-title {
        margin: 0;
        color: #fff;
        font-size: clamp(1.9rem, 3.8vw, 3.15rem);
        line-height: 1.1;
    }
    .home-about-copy {
        display: grid;
        align-content: center;
        gap: 20px;
    }
    .home-about-copy p {
        margin: 0;
        color: #d0c4ca;
        line-height: 1.8;
    }
    .home-about-points {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 10px;
    }
    .home-about-point {
        padding: 12px;
        color: #f1e9ed;
        background: rgba(255,255,255,.055);
        border: 1px solid rgba(255,255,255,.08);
        border-radius: 11px;
        font-size: .72rem;
        font-weight: 750;
    }
    .home-about-point i {
        margin-right: 5px;
        color: #ef9fc1;
    }

    @media (max-width: 991.98px) {
        .home-hero {
            grid-template-columns: minmax(0, 1fr) minmax(290px, .72fr);
            gap: 38px;
        }
        .home-hero-title { font-size: clamp(2.5rem, 6vw, 3.8rem); }
        .home-about { grid-template-columns: 1fr; }
    }
    @media (max-width: 767.98px) {
        .home-hero-section { padding: 42px 0 70px; }
        .home-hero {
            width: min(calc(100% - 32px), 600px);
            grid-template-columns: 1fr;
            gap: 38px;
        }
        .home-hero-copy { text-align: center; }
        .home-hero-title { margin-inline: auto; font-size: clamp(2.35rem, 12vw, 4rem); }
        .home-hero-subtitle { margin-inline: auto; }
        .home-hero-actions, .home-hero-note { justify-content: center; }
        .home-hero-visual { width: min(92%, 465px); justify-self: center; }
        .home-trust {
            width: min(calc(100% - 32px), 600px);
            grid-template-columns: 1fr;
            margin-bottom: 58px;
        }
        .home-trust-item { padding: 16px 18px; }
        .home-trust-item + .home-trust-item { border-top: 1px solid var(--border); border-left: 0; }
        .home-products, .home-about { width: min(calc(100% - 32px), 680px); }
        .home-about { margin-top: 58px; }
    }
    @media (max-width: 479.98px) {
        .home-hero-section { padding-top: 34px; }
        .home-hero-title { font-size: clamp(2.15rem, 13vw, 3.1rem); }
        .home-hero-actions { align-items: stretch; flex-direction: column; }
        .home-hero-btn { width: 100%; }
        .home-hero-note { align-items: flex-start; flex-direction: column; }
        .home-hero-visual { width: 96%; }
        .home-art-stage { border-radius: 42% 58% 45% 55% / 52% 44% 56% 48%; }
        .home-hero-caption { right: 5%; bottom: 6%; left: 5%; padding: 10px 11px; }
        .home-hero-caption strong { max-width: 160px; }
        .home-hero-carousel-controls { right: 4%; left: 4%; }
        .home-about { padding: 28px 20px; }
        .home-about-points { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
<section class="home-hero-section">
    <div class="home-hero">
        <div class="home-hero-copy">
            <span class="home-hero-eyebrow">Windhoek's online flower shop</span>
            <h1 class="home-hero-title">Say it beautifully, <span>with flowers.</span></h1>
            <p class="home-hero-subtitle">
                Fresh, hand-picked blooms and personal touches, thoughtfully arranged for every moment that matters.
            </p>
            <div class="home-hero-actions">
                <a href="{{ route('products.index') }}" class="buy-now-btn home-hero-btn">
                    Explore flowers <i class="bi bi-arrow-right" aria-hidden="true"></i>
                </a>
                <a href="{{ config('social.whatsapp') }}" target="_blank" rel="noopener noreferrer" class="buy-now-btn home-hero-btn home-hero-btn-secondary">
                    <i class="bi bi-whatsapp" aria-hidden="true"></i> Order on WhatsApp
                </a>
            </div>
            <div class="home-hero-note" aria-label="Service highlights">
                <span><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Same-day Windhoek delivery</span>
                <span><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Made fresh to order</span>
            </div>
        </div>

        <div class="home-hero-visual" data-hero-carousel data-interval="5600">
            <div class="home-art-stage" aria-label="Featured flower slideshow" aria-live="polite">
                <span class="home-art-word" aria-hidden="true">BLOOM</span>
                @if($heroProducts->count() > 0)
                    <span class="home-art-index"><span data-hero-current>01</span> / {{ str_pad((string) $heroProducts->count(), 2, '0', STR_PAD_LEFT) }} &middot; living arrangements</span>
                    @foreach($heroProducts as $product)
                        <a href="{{ route('products.show', $product->id) }}"
                           class="home-hero-slide {{ $loop->first ? 'is-active' : '' }}"
                           data-hero-slide
                           data-slide-index="{{ $loop->index }}"
                           aria-hidden="{{ $loop->first ? 'false' : 'true' }}"
                           tabindex="{{ $loop->first ? '0' : '-1' }}"
                           aria-label="View {{ $product->name }}">
                            <span class="home-hero-bloom">
                                <img src="{{ $product->image_url }}"
                                     class="home-hero-image"
                                     alt="{{ $product->name }} by /Namsa Florals"
                                     loading="{{ $loop->index < 2 ? 'eager' : 'lazy' }}"
                                     fetchpriority="{{ $loop->first ? 'high' : 'low' }}"
                                     decoding="async">
                            </span>
                            <span class="home-hero-caption">
                                <span>
                                    <small>Featured arrangement</small>
                                    <strong>{{ $product->name }}</strong>
                                </span>
                                <span class="home-hero-caption-price">N$ {{ number_format($product->price, 2) }}</span>
                            </span>
                        </a>
                    @endforeach
                @elseif($heroProduct)
                    <div class="home-hero-placeholder" aria-label="{{ $heroProduct->name }}">
                        <i class="bi bi-flower1" aria-hidden="true"></i>
                    </div>
                @else
                    <div class="home-hero-placeholder" aria-label="Fresh flowers coming soon">
                        <i class="bi bi-flower1" aria-hidden="true"></i>
                    </div>
                @endif
            </div>

            @if($heroProducts->count() > 1)
                <div class="home-hero-carousel-controls" aria-label="Flower slideshow controls">
                    <button type="button" class="home-hero-control" data-hero-prev aria-label="Previous flower">
                        <i class="bi bi-chevron-left" aria-hidden="true"></i>
                    </button>
                    <div class="home-hero-dots" role="tablist" aria-label="Choose a featured flower">
                        @foreach($heroProducts as $product)
                            <button type="button"
                                    class="home-hero-dot {{ $loop->first ? 'is-active' : '' }}"
                                    data-hero-dot="{{ $loop->index }}"
                                    role="tab"
                                    aria-selected="{{ $loop->first ? 'true' : 'false' }}"
                                    aria-label="Show {{ $product->name }}"></button>
                        @endforeach
                    </div>
                    <button type="button" class="home-hero-control" data-hero-pause aria-label="Pause flower slideshow" aria-pressed="false">
                        <i class="bi bi-pause-fill" aria-hidden="true"></i>
                    </button>
                    <button type="button" class="home-hero-control" data-hero-next aria-label="Next flower">
                        <i class="bi bi-chevron-right" aria-hidden="true"></i>
                    </button>
                </div>
            @endif
        </div>
    </div>
</section>

<section class="home-trust" aria-label="Why shop with /Namsa Florals">
    <div class="home-trust-item">
        <span class="home-trust-icon"><i class="bi bi-lightning-charge" aria-hidden="true"></i></span>
        <span><strong>Same-day delivery</strong><span>Across Windhoek</span></span>
    </div>
    <div class="home-trust-item">
        <span class="home-trust-icon"><i class="bi bi-flower2" aria-hidden="true"></i></span>
        <span><strong>Fresh &amp; hand-picked</strong><span>Every bouquet made to order</span></span>
    </div>
    <div class="home-trust-item">
        <span class="home-trust-icon"><i class="bi bi-chat-dots" aria-hidden="true"></i></span>
        <span><strong>Personal service</strong><span>Friendly help on WhatsApp</span></span>
    </div>
</section>

<section class="home-products" aria-labelledby="featured-products-title">
    <div class="section-heading">
        <div class="section-heading-copy">
            <span class="section-kicker">Curated for you</span>
            <h2 class="section-title" id="featured-products-title">Featured flowers</h2>
            <p class="section-subtitle">Thoughtful arrangements for birthdays, milestones, love, and everything in between.</p>
        </div>
        <a href="{{ route('products.index') }}" class="text-link">View all flowers <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
    </div>

    @if($featuredProducts->count() > 0)
        <div class="products-grid">
            @foreach($featuredProducts as $product)
                <article class="product-card">
                    <a href="{{ route('products.show', $product->id) }}" class="product-media-link">
                        @if($product->category)
                            <span class="product-card-badge">{{ $product->category }}</span>
                        @endif
                        <x-product-image :product="$product" alt="{{ $product->name }} - Featured flower from /Namsa Florals Namibia" />
                    </a>
                    <div class="product-info">
                        <h3 class="product-name"><a href="{{ route('products.show', $product->id) }}" class="product-name-link">{{ $product->name }}</a></h3>
                        <div class="product-price">N$ {{ number_format($product->price, 2) }}</div>
                        @if($product->stock > 0)
                            <form action="{{ route('cart.add') }}" method="POST" data-add-to-cart>
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="buy-now-btn"><i class="bi bi-bag-plus" aria-hidden="true"></i> Add to cart</button>
                            </form>
                        @else
                            <button class="buy-now-btn" disabled>Out of stock</button>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
        <div class="home-products-footer">
            <a href="{{ route('products.index') }}" class="buy-now-btn">Browse all flowers <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
        </div>
    @else
        <div class="card text-center p-5">
            <p class="text-muted mb-3">New arrangements are being prepared. Please check back soon.</p>
            <a href="{{ route('contact') }}" class="text-link justify-content-center">Contact our florist <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
        </div>
    @endif
</section>

<section class="home-about" aria-labelledby="home-about-title">
    <div>
        <span class="section-kicker">More than a bouquet</span>
        <h2 class="home-about-title" id="home-about-title">We help you send the feeling.</h2>
    </div>
    <div class="home-about-copy">
        <p>
            At <strong>/Namsa Florals</strong>, every order is created with intention. From hand-picked blooms to personal poster messages, we make meaningful gestures feel beautifully yours.
        </p>
        <div class="home-about-points">
            <span class="home-about-point"><i class="bi bi-check2" aria-hidden="true"></i> Fresh blooms</span>
            <span class="home-about-point"><i class="bi bi-check2" aria-hidden="true"></i> Personal touches</span>
            <span class="home-about-point"><i class="bi bi-check2" aria-hidden="true"></i> Local delivery</span>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    (function () {
        var carousel = document.querySelector('[data-hero-carousel]');
        if (!carousel) return;

        var slides = Array.prototype.slice.call(carousel.querySelectorAll('[data-hero-slide]'));
        var dots = Array.prototype.slice.call(carousel.querySelectorAll('[data-hero-dot]'));
        var previousButton = carousel.querySelector('[data-hero-prev]');
        var nextButton = carousel.querySelector('[data-hero-next]');
        var pauseButton = carousel.querySelector('[data-hero-pause]');
        var currentLabel = carousel.querySelector('[data-hero-current]');
        var interval = Number(carousel.getAttribute('data-interval')) || 5600;
        var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        var current = Math.max(0, slides.findIndex(function (slide) { return slide.classList.contains('is-active'); }));
        var timer = null;
        var startedAt = 0;
        var remaining = interval;
        var paused = false;

        if (slides.length < 2) return;

        function haltClock() {
            if (!timer) return;
            remaining = Math.max(250, remaining - (Date.now() - startedAt));
            window.clearTimeout(timer);
            timer = null;
        }

        function scheduleClock() {
            if (paused || document.hidden || reduceMotion) return;
            window.clearTimeout(timer);
            startedAt = Date.now();
            timer = window.setTimeout(function () {
                showSlide(current + 1);
            }, remaining);
        }

        function updatePauseState() {
            carousel.classList.toggle('is-paused', paused || document.hidden);
            if (!pauseButton) return;
            pauseButton.setAttribute('aria-pressed', paused ? 'true' : 'false');
            pauseButton.setAttribute('aria-label', paused ? 'Play flower slideshow' : 'Pause flower slideshow');
            var icon = pauseButton.querySelector('i');
            if (icon) icon.className = paused ? 'bi bi-play-fill' : 'bi bi-pause-fill';
        }

        function showSlide(nextIndex) {
            var normalized = (nextIndex + slides.length) % slides.length;
            if (normalized === current) return;

            slides.forEach(function (slide, index) {
                var active = index === normalized;
                slide.classList.toggle('is-active', active);
                slide.setAttribute('aria-hidden', active ? 'false' : 'true');
                slide.setAttribute('tabindex', active ? '0' : '-1');
            });

            dots.forEach(function (dot, index) {
                var active = index === normalized;
                dot.classList.toggle('is-active', active);
                dot.setAttribute('aria-selected', active ? 'true' : 'false');
            });

            current = normalized;
            remaining = interval;
            if (currentLabel) currentLabel.textContent = String(current + 1).padStart(2, '0');
            scheduleClock();
        }

        if (previousButton) {
            previousButton.addEventListener('click', function () { showSlide(current - 1); });
        }
        if (nextButton) {
            nextButton.addEventListener('click', function () { showSlide(current + 1); });
        }
        dots.forEach(function (dot) {
            dot.addEventListener('click', function () {
                showSlide(Number(dot.getAttribute('data-hero-dot')));
            });
        });
        if (pauseButton) {
            if (reduceMotion) {
                pauseButton.hidden = true;
            } else {
                pauseButton.addEventListener('click', function () {
                    paused = !paused;
                    if (paused) haltClock(); else scheduleClock();
                    updatePauseState();
                });
            }
        }
        document.addEventListener('visibilitychange', function () {
            if (document.hidden) haltClock(); else scheduleClock();
            updatePauseState();
        });

        updatePauseState();
        scheduleClock();
    })();
</script>
@endpush
