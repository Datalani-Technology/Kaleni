<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    @php
        $seo = \App\Services\SeoService::getMetaTags(
            $seoPage ?? 'home',
            $seoData ?? []
        );
    @endphp
    
    <!-- Primary Meta Tags -->
    <title>{{ $seo['title'] }}</title>
    <meta name="title" content="{{ $seo['title'] }}">
    <meta name="description" content="{{ $seo['description'] }}">
    <meta name="keywords" content="{{ $seo['keywords'] }}">
    @if(!empty($seo['abstract']))
    <meta name="abstract" content="{{ $seo['abstract'] }}">
    @endif
    <meta name="author" content="Kaleni Catering Services">
    <meta name="robots" content="index, follow">
    <meta name="language" content="English">
    <meta name="revisit-after" content="7 days">
    <meta name="geo.region" content="NA">
    <meta name="geo.placename" content="Windhoek, Namibia">
    <meta name="geo.position" content="-22.5609;17.0658">
    <meta name="ICBM" content="-22.5609, 17.0658">
    
    <!-- Canonical URL -->
    <link rel="canonical" href="{{ $seo['url'] }}">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="{{ $seo['type'] }}">
    <meta property="og:url" content="{{ $seo['url'] }}">
    <meta property="og:title" content="{{ $seo['title'] }}">
    <meta property="og:description" content="{{ $seo['description'] }}">
    <meta property="og:image" content="{{ $seo['image'] }}">
    <meta property="og:site_name" content="Kaleni Catering Services">
    <meta property="og:locale" content="en_NA">
    
    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ $seo['url'] }}">
    <meta name="twitter:title" content="{{ $seo['title'] }}">
    <meta name="twitter:description" content="{{ $seo['description'] }}">
    <meta name="twitter:image" content="{{ $seo['image'] }}">
    
    @php
        $faviconLogo = \App\Models\Setting::get('logo_path');
        $faviconExt = $faviconLogo ? strtolower(pathinfo($faviconLogo, PATHINFO_EXTENSION)) : '';
        $faviconType = match($faviconExt) {
            'png' => 'image/png',
            'svg' => 'image/svg+xml',
            'jpg', 'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            default => 'image/png',
        };
    @endphp
    <!-- Favicon (logo as icon for tabs & Google search) -->
    @if($faviconLogo)
        <link rel="icon" type="{{ $faviconType }}" href="{{ asset('storage/' . $faviconLogo) }}">
        <link rel="apple-touch-icon" href="{{ asset('storage/' . $faviconLogo) }}">
    @else
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="48x48" href="{{ asset('favicon-48x48.png') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    @endif
    
    <!-- Stylesheets -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Great+Vibes&display=swap" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.css') }}">
    <!-- Kaleni brand color tokens — single source of truth for :root, shared with admin.css/admin-auth.css -->
    <link rel="stylesheet" href="{{ asset('css/kaleni-theme.css') }}">

    <!-- Structured Data -->
    @if(isset($structuredData))
        <script type="application/ld+json">
            {!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
        </script>
    @endif
    <style>
        :root {
            --radius: 14px;
            --radius-sm: 8px;
            --shadow-sm: 0 2px 10px rgba(41, 33, 31, 0.06);
            --shadow-md: 0 10px 30px rgba(41, 33, 31, 0.10);
            --font-display: 'Manrope', 'Segoe UI', Arial, sans-serif;
            --font-body: 'Manrope', 'Segoe UI', Arial, sans-serif;
        }
        html { scroll-behavior: smooth; }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: var(--font-body);
            color: var(--ink);
            background-color: var(--bg);
            overflow-x: hidden;
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: var(--font-display);
            letter-spacing: -0.01em;
        }
        /* Simple Header */
        .header {
            background: rgba(255,255,255,0.92);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            padding: 16px 0;
            box-shadow: var(--shadow-sm);
            position: sticky;
            top: 0;
            z-index: 1030;
        }
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        .logo {
            font-family: var(--font-display);
            font-size: 26px;
            font-weight: 600;
            color: var(--ink);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .logo i {
            color: var(--primary-color);
        }
        .logo-img {
            height: 46px;
            width: auto;
            object-fit: contain;
            vertical-align: middle;
        }
        .logo--image-only .logo-img {
            height: 56px;
        }
        .nav-links {
            display: flex;
            align-items: center;
            gap: 30px;
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .nav-links li {
            display: flex;
            align-items: center;
        }
        .nav-links a {
            color: #666;
            text-decoration: none;
            font-size: 15px;
            line-height: 22px;
            transition: color 0.3s;
            display: inline-flex;
            align-items: center;
            white-space: nowrap;
        }
        .nav-links a:hover,
        .nav-links a.active {
            color: #333;
            font-weight: 500;
        }
        .search-cart {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .search-form {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .search-input {
            padding: 5px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            width: 200px;
            height: 32px;
            line-height: 20px;
            box-sizing: border-box;
        }
        .search-btn {
            background: none;
            border: none;
            color: #666;
            cursor: pointer;
            font-size: 15px;
            line-height: 22px;
            padding: 0;
        }
        .cart-link {
            color: #666;
            text-decoration: none;
            font-size: 15px;
            line-height: 22px;
            position: relative;
            display: inline-flex;
            align-items: center;
        }
        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #333;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        /* Product Grid */
        .products-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        .products-grid {
            display: grid;
            /* auto-fit (not auto-fill/a fixed column count): fewer cards than
               a full row centre and size evenly instead of packing left with
               an empty, lopsided gap on the right. */
            grid-template-columns: repeat(auto-fit, minmax(240px, 280px));
            justify-content: center;
            gap: 30px;
        }
        .product-card {
            background: var(--surface);
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            border: 1px solid rgba(34, 28, 34, 0.05);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }
        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
        }
        .product-image {
            width: 100%;
            height: 280px;
            object-fit: cover;
            display: block;
        }
        .product-image-placeholder {
            background: linear-gradient(135deg, #241b17 0%, #14100e 100%);
            color: var(--secondary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
        }
        .product-detail-image-placeholder {
            width: 100%;
            aspect-ratio: 6 / 5;
            border-radius: var(--radius);
            font-size: 5rem;
        }
        .product-info {
            padding: 20px;
            text-align: center;
        }
        .product-name {
            font-size: 16px;
            font-weight: 500;
            color: var(--ink);
            margin-bottom: 10px;
        }
        .product-price {
            font-family: var(--font-display);
            font-size: 19px;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 15px;
        }
        .buy-now-btn {
            width: 100%;
            padding: 12px;
            background: var(--ink);
            border: none;
            border-radius: var(--radius-sm);
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 0.01em;
            color: white;
            cursor: pointer;
            transition: background 0.25s ease, transform 0.15s ease, box-shadow 0.25s ease;
            text-decoration: none;
            display: block;
            text-align: center;
        }
        .buy-now-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(104, 11, 28, 0.3);
            color: white;
        }
        .buy-now-btn:disabled {
            background: #f0f0f0;
            color: #999;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        /* Footer styling lives in storefront.css (footer.site-footer and friends) */
        /* Floating buttons */
        .scroll-top-btn,
        .float-whatsapp {
            position: fixed;
            z-index: 999;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            color: white;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            transition: opacity 0.3s, transform 0.3s, box-shadow 0.3s;
        }
        .scroll-top-btn {
            bottom: 140px;
            right: 24px;
            background: #333;
            opacity: 0;
            pointer-events: none;
        }
        .scroll-top-btn.visible {
            opacity: 1;
            pointer-events: auto;
        }
        .scroll-top-btn:hover {
            transform: translateY(-4px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.25);
            color: white;
        }
        .float-whatsapp {
            bottom: 96px;
            right: 24px;
            background: #25D366;
            font-size: 1.5rem;
        }
        .float-whatsapp:hover {
            transform: translateY(-4px);
            box-shadow: 0 6px 16px rgba(37, 211, 102, 0.4);
            color: white;
        }
        @media (max-width: 400px) {
            .scroll-top-btn, .float-whatsapp { width: 44px; height: 44px; }
            .scroll-top-btn { bottom: 148px; right: 16px; }
            .float-whatsapp { bottom: 88px; right: 16px; font-size: 1.35rem; }
        }
        .layout-alert {
            margin: 15px auto;
            max-width: 1200px;
            border-radius: 5px;
        }
        @media (max-width: 768px) {
            .products-grid {
                gap: 20px;
            }
        }
        @media (max-width: 480px) {
            .products-grid {
                grid-template-columns: minmax(0, 340px);
            }
        }
        /* Small phones (e.g. 320px–400px) */
        @media (max-width: 400px) {
            .header { padding: 12px 0; }
            .header-content { padding: 0 12px; gap: 12px; }
            .logo { font-size: 20px; }
            .logo-img { height: 36px; }
            .logo--image-only .logo-img { height: 44px; }
            .container { padding-left: 12px; padding-right: 12px; }
            .layout-alert { margin-left: 12px; margin-right: 12px; }
            .products-container { padding: 0 12px; margin: 24px auto; }
            .product-info { padding: 14px; }
            .product-name { font-size: 14px; }
            .product-price { font-size: 16px; }
            .product-image { height: 220px; }
            .buy-now-btn { padding: 10px; font-size: 13px; }
            /* Footer responsive rules live in storefront.css */
        }

        /* Mobile nav toggle (small screens) */
        .nav-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #333;
            cursor: pointer;
            padding: 4px 8px;
            align-items: center;
            justify-content: center;
        }
        .mobile-search-toggle {
            display: none;
            background: none;
            border: none;
            color: #333;
            cursor: pointer;
            padding: 6px 10px;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }
        .mobile-search-toggle:hover { color: #111; }
        .mobile-cart-link {
            display: none;
            align-items: center;
            justify-content: center;
            color: #333;
            text-decoration: none;
            padding: 6px 10px;
            font-size: 1.25rem;
            position: relative;
        }
        .mobile-cart-link:hover { color: #111; }
        .mobile-cart-link .cart-badge {
            top: 2px;
            right: 2px;
        }
        .mobile-search-bar {
            display: none;
            width: 100%;
            padding: 10px 12px;
            background: #f8f9fa;
            border-top: 1px solid #eee;
        }
        .mobile-search-bar.is-open { display: flex; }
        .mobile-search-bar .search-form { width: 100%; gap: 8px; display: flex; align-items: center; }
        .mobile-search-bar .search-input { flex: 1; min-width: 0; height: 40px; font-size: 16px; }
        .mobile-search-bar .search-btn { padding: 8px 14px; background: #333; color: #fff; border-radius: 6px; }
        @media (max-width: 991px) {
            .header-content {
                flex-wrap: wrap;
                justify-content: space-between;
            }
            .mobile-search-toggle { display: flex; order: 2; }
            .mobile-cart-link { display: flex; order: 3; }
            .nav-toggle { display: flex; order: 4; }
            .logo { order: 1; }
            .nav-group {
                order: 4;
                width: 100%;
                display: none;
                flex-direction: column;
                gap: 12px;
                padding-top: 8px;
                border-top: 1px solid #eee;
                margin-top: 4px;
            }
            .nav-group.is-open { display: flex; }
            .nav-links {
                flex-direction: column;
                gap: 4px;
                width: 100%;
                align-items: stretch;
            }
            .nav-links a { padding: 10px 0; font-size: 15px; }
            .search-cart {
                flex-direction: column;
                width: 100%;
                gap: 10px;
            }
            .search-form { width: 100%; }
            .search-input { width: 100%; max-width: none; }
            .search-btn { flex-shrink: 0; }
            .cart-link { display: block; padding: 10px 0; text-align: center; }
            .mobile-search-bar { order: 5; width: 100%; }
            .nav-group { order: 6; }
        }
        @media (min-width: 992px) {
            .nav-group {
                display: flex !important;
                flex-direction: row;
                align-items: center;
                gap: 30px;
                flex-wrap: nowrap;
            }
            .mobile-search-toggle,
            .mobile-search-bar,
            .mobile-cart-link { display: none !important; }
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/storefront.css') }}">
    @stack('styles')
</head>
<body>
    <div class="announcement-bar" role="region" aria-label="Store information">
        <div class="announcement-inner">
            <span class="announcement-item">
                <i class="bi bi-truck" aria-hidden="true"></i>
                Free delivery around Windhoek
            </span>
            <a href="{{ config('social.whatsapp') }}" target="_blank" rel="noopener noreferrer" class="announcement-item announcement-link">
                <i class="bi bi-whatsapp" aria-hidden="true"></i>
                Need help? Chat with Chef K
            </a>
        </div>
    </div>
    <header class="header" role="banner">
        <div class="header-content">
            @php
                $logoPath = \App\Models\Setting::get('logo_path');
                $logoText = \App\Models\Setting::get('logo_text', 'Kaleni Catering Services');
            @endphp
            <a href="{{ route('home') }}" class="logo logo--image-only">
                @if($logoPath)
                    <img src="{{ asset('storage/' . $logoPath) }}" alt="{{ $logoText }}" class="logo-img">
                @else
                    <img src="{{ asset('images/kaleni/brand/kaleni-logo.jpg') }}" alt="{{ $logoText }}" class="logo-img">
                @endif
            </a>
            <button type="button" class="mobile-search-toggle" id="mobileSearchToggle" aria-label="Search" title="Search the menu">
                <i class="bi bi-search"></i>
            </button>
            @php
                $headerCartCount = \App\Models\CartItem::where('session_id', session()->getId())->sum('quantity');
            @endphp
            <a href="{{ route('cart.index') }}" class="mobile-cart-link" id="mobileCartLink" aria-label="My order" title="View my order">
                <i class="bi bi-cart3"></i>
                <span class="cart-badge {{ $headerCartCount > 0 ? '' : 'd-none' }}" id="cartBadgeMobile">{{ $headerCartCount }}</span>
            </a>
            <button type="button" class="nav-toggle" id="navToggle" aria-label="Toggle menu" aria-expanded="false">
                <i class="bi bi-list"></i>
            </button>
            <div class="mobile-search-bar" id="mobileSearchBar" role="search">
                <form action="{{ route('menu.index') }}" method="GET" class="search-form">
                    <input type="text" name="search" placeholder="Search the menu..." value="{{ request('search') }}" class="search-input" aria-label="Search the menu">
                    <button type="submit" class="search-btn">
                        <i class="bi bi-search" aria-hidden="true"></i>
                        <span class="search-btn-label">Search</span>
                    </button>
                </form>
            </div>
            <div class="nav-group" id="navGroup">
                <ul class="nav-links">
                    <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}" @if(request()->routeIs('home')) aria-current="page" @endif>Home</a></li>
                    <li><a href="{{ route('menu.index') }}" class="{{ request()->routeIs('menu.*') ? 'active' : '' }}" @if(request()->routeIs('menu.*')) aria-current="page" @endif>Menu</a></li>
                    <li><a href="{{ route('food-of-the-day.index') }}" class="{{ request()->routeIs('food-of-the-day.*') ? 'active' : '' }}" @if(request()->routeIs('food-of-the-day.*')) aria-current="page" @endif>Food of the Day</a></li>
                    <li><a href="{{ route('special-requests.create') }}" class="{{ request()->routeIs('special-requests.*') ? 'active' : '' }}" @if(request()->routeIs('special-requests.*')) aria-current="page" @endif>Special Request</a></li>
                    <li><a href="{{ route('gallery') }}" class="{{ request()->routeIs('gallery') ? 'active' : '' }}" @if(request()->routeIs('gallery')) aria-current="page" @endif>Gallery</a></li>
                    <li><a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'active' : '' }}" @if(request()->routeIs('contact')) aria-current="page" @endif>Contact</a></li>
                </ul>
                <div class="search-cart">
                    <form action="{{ route('menu.index') }}" method="GET" class="search-form">
                        <input type="text" name="search" placeholder="Search the menu..." value="{{ request('search') }}" class="search-input">
                        <button type="submit" class="search-btn" aria-label="Search the menu">
                            <i class="bi bi-search" aria-hidden="true"></i>
                            <span class="search-btn-label">Search</span>
                        </button>
                    </form>
                    <a href="{{ route('cart.index') }}" class="cart-link" id="cartLink">
                        <i class="bi bi-bag" aria-hidden="true"></i>
                        <span>My Order</span>
                        <span class="cart-badge {{ $headerCartCount > 0 ? '' : 'd-none' }}" id="cartBadgeNav">{{ $headerCartCount }}</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main>
        @if(session('success') || session('error'))
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    @if(session('success'))
                        showConfirmPopup(@json(session('success')), false);
                    @endif
                    @if(session('error'))
                        showConfirmPopup(@json(session('error')), true);
                    @endif
                });
            </script>
        @endif

        @yield('content')
    </main>

    <footer role="contentinfo" class="site-footer" id="siteFooter" data-bg-carousel data-interval="6000">
        @foreach($brandCarouselImages as $i => $image)
            <div class="site-footer-bg {{ $loop->first ? 'is-active' : '' }}" data-bg-slide aria-hidden="true" style="background-image: url('{{ asset($image) }}');"></div>
        @endforeach
        <div class="site-footer-overlay" aria-hidden="true"></div>
        <svg class="site-footer-wave" viewBox="0 0 1200 60" preserveAspectRatio="none" aria-hidden="true">
            <path d="M0,22 C240,60 420,0 700,18 C900,30 1040,6 1200,26 L1200,0 L0,0 Z" fill="var(--bg)"></path>
        </svg>
        <div class="footer-content">
            <div class="row g-4 gy-5">
                <div class="col-lg-4 footer-section footer-brand footer-animate">
                    <a href="{{ route('home') }}" class="footer-logo">
                        @if($logoPath)
                            <img src="{{ asset('storage/' . $logoPath) }}" alt="{{ $logoText }}">
                        @else
                            <img src="{{ asset('images/kaleni/brand/kaleni-logo.jpg') }}" alt="{{ $logoText }}">
                        @endif
                    </a>
                    <p class="footer-description">
                        Home-style catering in Windhoek, Namibia. Lunch and dinner packs, a new Food of the Day, and full event catering by Chef K.
                    </p>
                    <div class="footer-social">
                        <span class="footer-social-label">Follow us</span>
                        <div class="social-links">
                            @if(config('social.facebook'))
                            <a href="{{ config('social.facebook') }}" target="_blank" rel="noopener noreferrer" class="social-link" title="Facebook" aria-label="Facebook">
                                <i class="bi bi-facebook"></i>
                            </a>
                            @endif
                            @if(config('social.instagram'))
                            <a href="{{ config('social.instagram') }}" target="_blank" rel="noopener noreferrer" class="social-link" title="Instagram" aria-label="Instagram">
                                <i class="bi bi-instagram"></i>
                            </a>
                            @endif
                            @if(config('social.tiktok'))
                            <a href="{{ config('social.tiktok') }}" target="_blank" rel="noopener noreferrer" class="social-link" title="TikTok" aria-label="TikTok">
                                <svg viewBox="0 0 448 512" width="1em" height="1em" fill="currentColor" aria-hidden="true"><path d="M448,209.91a210.06,210.06,0,0,1-122.77-39.25V349.38A162.55,162.55,0,1,1,185,188.31V278.2a74.62,74.62,0,1,0,52.23,71.18V0l88,0a121.18,121.18,0,0,0,1.86,22.17h0A122.18,122.18,0,0,0,381,102.39a121.43,121.43,0,0,0,67,20.14Z"/></svg>
                            </a>
                            @endif
                            @if(config('social.whatsapp'))
                            <a href="{{ config('social.whatsapp') }}" target="_blank" rel="noopener noreferrer" class="social-link" title="WhatsApp" aria-label="WhatsApp">
                                <i class="bi bi-whatsapp"></i>
                            </a>
                            @endif
                            @if(config('social.whatsapp_channel'))
                                <a href="{{ config('social.whatsapp_channel') }}" target="_blank" rel="noopener noreferrer" class="social-link" title="WhatsApp Channel" aria-label="Follow our WhatsApp Channel">
                                    <i class="bi bi-broadcast"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-2 footer-section footer-animate" style="--footer-delay: 0.08s">
                    <h5>Quick Links</h5>
                    <ul class="footer-links footer-links-single">
                        <li><a href="{{ route('home') }}">Home</a></li>
                        <li><a href="{{ route('menu.index') }}">Menu</a></li>
                        <li><a href="{{ route('gallery') }}">Gallery</a></li>
                        <li><a href="{{ route('contact') }}">Contact</a></li>
                        <li><a href="{{ route('cart.index') }}">My Order</a></li>
                    </ul>
                </div>
                <div class="col-sm-6 col-lg-3 footer-section footer-animate" style="--footer-delay: 0.16s">
                    <h5>Our Services</h5>
                    <ul class="footer-links footer-links-single">
                        <li><a href="{{ route('food-of-the-day.index') }}">Food of the Day</a></li>
                        <li><a href="{{ route('home') }}#get-a-quote">Get a Quote</a></li>
                        <li><a href="{{ route('special-requests.create') }}">Special Requests</a></li>
                        <li><a href="{{ route('delivery') }}">Delivery &amp; Setup</a></li>
                    </ul>
                </div>
                <div class="col-sm-6 col-lg-3 footer-section footer-animate" style="--footer-delay: 0.24s">
                    <h5>Contact Information</h5>
                    <ul class="footer-contact-list">
                        <li>
                            <a href="tel:{{ preg_replace('/[^+0-9]/', '', config('contact.phone')) }}">
                                <i class="bi bi-telephone" aria-hidden="true"></i>
                                <span>{{ config('contact.phone') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="mailto:{{ config('contact.email_info') }}">
                                <i class="bi bi-envelope" aria-hidden="true"></i>
                                <span>{{ config('contact.email_info') }}</span>
                            </a>
                        </li>
                        <li>
                            <span class="footer-contact-static">
                                <i class="bi bi-geo-alt" aria-hidden="true"></i>
                                <span>{{ config('contact.address.city') }}, {{ config('contact.address.country') }}@if(config('contact.address.po_box')), {{ config('contact.address.po_box') }}@endif</span>
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="footer-divider" aria-hidden="true"></div>
            <div class="footer-bottom">
                <p class="footer-copyright">&copy; {{ date('Y') }} Kaleni Catering Services. All rights reserved.</p>
                <ul class="footer-legal">
                    <li><a href="{{ route('terms') }}">Terms</a></li>
                    <li><a href="{{ route('privacy') }}">Privacy Policy</a></li>
                    <li><a href="{{ route('cancellations') }}">Cancellations &amp; Refunds</a></li>
                </ul>
                <p class="footer-credit-line">Website by <a href="https://datalani.com" target="_blank" rel="noopener noreferrer" class="footer-credit">Datalani Technology</a></p>
            </div>
        </div>
    </footer>
    <noscript><style>.footer-animate { opacity: 1 !important; transform: none !important; }</style></noscript>

    <a href="#" class="scroll-top-btn" id="scrollTopBtn" aria-label="Scroll to top" title="Back to top">
        <i class="bi bi-arrow-up"></i>
    </a>
    <a href="{{ config('social.whatsapp') }}" target="_blank" rel="noopener noreferrer" class="float-whatsapp" aria-label="Chat on WhatsApp" title="Chat on WhatsApp">
        <i class="bi bi-whatsapp"></i>
    </a>

    <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Centered pop-up (icon, title, message, button) for confirmations
        // that end a flow — form submissions, bookings, orders. Add to Cart
        // deliberately keeps the lighter showToast() below instead, since
        // people add several items in a row while browsing and a blocking
        // centered dialog on every click would slow that down.
        function showConfirmPopup(msg, isError, duration) {
            if (typeof Swal === 'undefined') return;
            Swal.fire({
                icon: isError ? 'error' : 'success',
                title: isError ? 'Oops!' : 'Success!',
                text: msg,
                confirmButtonText: 'OK',
                confirmButtonColor: isError ? '#dc3545' : '#198754',
                timer: duration || 5000,
                timerProgressBar: true,
            });
        }
        function showToast(msg, isError, duration) {
            var t = document.createElement('div');
            t.className = 'add-to-cart-toast' + (isError ? ' add-to-cart-toast-error' : '');
            t.setAttribute('role', 'alert');

            var icon = document.createElement('i');
            icon.className = 'bi ' + (isError ? 'bi-exclamation-circle-fill' : 'bi-check-circle-fill') + ' add-to-cart-toast-icon';

            var text = document.createElement('span');
            text.className = 'add-to-cart-toast-text';
            text.textContent = msg;

            var close = document.createElement('button');
            close.type = 'button';
            close.className = 'add-to-cart-toast-close';
            close.setAttribute('aria-label', 'Dismiss');
            close.innerHTML = '&times;';

            t.appendChild(icon);
            t.appendChild(text);
            t.appendChild(close);
            document.body.appendChild(t);

            var hideTimer;
            function dismiss() {
                clearTimeout(hideTimer);
                t.classList.remove('add-to-cart-toast-show');
                setTimeout(function() { t.remove(); }, 300);
            }
            close.addEventListener('click', dismiss);

            requestAnimationFrame(function() { t.classList.add('add-to-cart-toast-show'); });
            hideTimer = setTimeout(dismiss, duration || 2500);
        }
        (function() {
            var toggle = document.getElementById('navToggle');
            var group = document.getElementById('navGroup');
            if (toggle && group) {
                toggle.addEventListener('click', function() {
                    group.classList.toggle('is-open');
                    var open = group.classList.contains('is-open');
                    toggle.setAttribute('aria-expanded', open);
                    toggle.querySelector('i').className = open ? 'bi bi-x-lg' : 'bi bi-list';
                });
            }
        })();
        (function() {
            var searchToggle = document.getElementById('mobileSearchToggle');
            var searchBar = document.getElementById('mobileSearchBar');
            if (searchToggle && searchBar) {
                searchToggle.addEventListener('click', function() {
                    searchBar.classList.toggle('is-open');
                    if (searchBar.classList.contains('is-open')) {
                        var inp = searchBar.querySelector('.search-input');
                        if (inp) { inp.focus(); }
                    }
                });
            }
        })();
        (function() {
            var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            var containers = Array.prototype.slice.call(document.querySelectorAll('[data-bg-carousel]'));
            containers.forEach(function(container) {
                var slides = Array.prototype.slice.call(container.querySelectorAll('[data-bg-slide]'));
                var interval = Number(container.getAttribute('data-interval')) || 6000;
                var current = 0;
                var timer = null;
                if (slides.length < 2 || reduceMotion) return;
                function showSlide(index) {
                    current = (index + slides.length) % slides.length;
                    slides.forEach(function(slide, i) { slide.classList.toggle('is-active', i === current); });
                }
                function schedule() {
                    window.clearTimeout(timer);
                    timer = window.setTimeout(function() { showSlide(current + 1); schedule(); }, interval);
                }
                document.addEventListener('visibilitychange', function() {
                    if (document.hidden) window.clearTimeout(timer); else schedule();
                });
                schedule();
            });
        })();
        (function() {
            var items = document.querySelectorAll('.footer-animate');
            if (!items.length) return;
            if (typeof IntersectionObserver !== 'function') {
                items.forEach(function(item) { item.classList.add('is-visible'); });
                return;
            }
            var observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.15 });
            items.forEach(function(item) { observer.observe(item); });
        })();
        (function() {
            var btn = document.getElementById('scrollTopBtn');
            if (!btn) return;
            function toggleVisible() {
                btn.classList.toggle('visible', window.scrollY > 300);
            }
            window.addEventListener('scroll', toggleVisible, { passive: true });
            toggleVisible();
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        })();
        (function() {
            var cartAddForms = document.querySelectorAll('form[data-add-to-cart]');
            var cartBadges = document.querySelectorAll('.cart-badge');
            var cartAddUrl = '{{ route("cart.add") }}';
            cartAddForms.forEach(function(form) {
                form.addEventListener('submit', function(e) {
                    if (form.dataset.addToCart === 'no') return;
                    e.preventDefault();
                    var btn = form.querySelector('[type="submit"]');
                    var origText = btn ? btn.innerHTML : '';
                    if (btn) { btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Adding...'; }
                    var fd = new FormData(form);
                    fetch(cartAddUrl, {
                        method: 'POST',
                        body: fd,
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                    }).then(function(r) { return r.json().then(function(d) { return { ok: r.ok, data: d }; }); })
                    .then(function(_a) {
                        var ok = _a.ok, data = _a.data;
                        if (ok && data.success) {
                            // On the cart page itself, the order table/totals
                            // above this "recommended items" section were
                            // rendered server-side at page load — a reload is
                            // the simplest way to reflect the newly added
                            // item there instead of just the nav badge.
                            if (document.querySelector('.cart-page')) {
                                location.reload();
                                return;
                            }
                            var n = data.cart_count || 0;
                            cartBadges.forEach(function(b) {
                                b.textContent = n;
                                b.classList.toggle('d-none', !(n > 0));
                            });
                            showToast(data.message || 'Added to your order.');
                        } else {
                            showToast(data.message || 'Could not add to your order.', true);
                        }
                    }).catch(function() {
                        showToast('Something went wrong. Please try again.', true);
                    }).finally(function() {
                        if (btn) { btn.disabled = false; btn.innerHTML = origText; }
                    });
                });
            });
        })();
    </script>
    <style>
        .add-to-cart-toast {
            position: fixed; top: 20px; right: 20px; z-index: 9999;
            display: flex; align-items: flex-start; gap: 10px;
            min-width: 280px; max-width: 360px;
            background: #fff; color: var(--ink, #29211F);
            padding: 14px 34px 14px 16px; border-radius: 12px; font-size: 0.92rem; line-height: 1.4;
            border-left: 4px solid #198754;
            box-shadow: 0 14px 38px rgba(32, 25, 30, .2);
            opacity: 0; transform: translateX(24px);
            transition: opacity 0.3s, transform 0.3s;
        }
        .add-to-cart-toast.add-to-cart-toast-show { opacity: 1; transform: translateX(0); }
        .add-to-cart-toast-error { border-left-color: #dc3545; }
        .add-to-cart-toast-icon { color: #198754; font-size: 1.1rem; margin-top: 1px; flex-shrink: 0; }
        .add-to-cart-toast-error .add-to-cart-toast-icon { color: #dc3545; }
        .add-to-cart-toast-text { flex: 1; }
        .add-to-cart-toast-close {
            position: absolute; top: 8px; right: 8px; width: 24px; height: 24px;
            border: none; background: none; color: #999; font-size: 1.15rem; line-height: 1;
            cursor: pointer; border-radius: 6px;
        }
        .add-to-cart-toast-close:hover { background: rgba(0,0,0,.05); color: #333; }
        @media (max-width: 480px) {
            .add-to-cart-toast { left: 16px; right: 16px; top: 12px; max-width: none; min-width: 0; }
        }
    </style>
    @stack('scripts')
</body>
</html>
