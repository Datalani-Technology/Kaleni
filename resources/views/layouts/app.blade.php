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
    <meta name="author" content="/Namsa Florals">
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
    <meta property="og:site_name" content="/Namsa Florals">
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
    @endif
    
    <!-- Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- Structured Data -->
    @if(isset($structuredData))
        <script type="application/ld+json">
            {!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
        </script>
    @endif
    <style>
        :root {
            --primary-color: #d63384;
            --secondary-color: #ffc107;
            --success-color: #28a745;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            overflow-x: hidden;
        }
        /* Simple Header */
        .header {
            background: white;
            padding: 20px 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
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
            font-size: 26px;
            font-weight: bold;
            color: #333;
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
            grid-template-columns: repeat(4, 1fr);
            gap: 30px;
        }
        .product-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .product-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .product-image {
            width: 100%;
            height: 280px;
            object-fit: cover;
            display: block;
        }
        .product-info {
            padding: 20px;
            text-align: center;
        }
        .product-name {
            font-size: 16px;
            font-weight: 500;
            color: #333;
            margin-bottom: 10px;
        }
        .product-price {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
        }
        .buy-now-btn {
            width: 100%;
            padding: 12px;
            background: #333;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 500;
            color: white;
            cursor: pointer;
            transition: background 0.3s;
            text-decoration: none;
            display: block;
            text-align: center;
        }
        .buy-now-btn:hover {
            background: #222;
        }
        .buy-now-btn:disabled {
            background: #f0f0f0;
            color: #999;
            cursor: not-allowed;
        }
        footer {
            background: #333;
            color: white;
            padding: 40px 0 20px;
            margin-top: 60px;
        }
        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        .footer-section {
            margin-bottom: 30px;
        }
        .footer-section h5 {
            color: #fff;
            margin-bottom: 15px;
            font-size: 1.1rem;
        }
        .social-links {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .social-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.15);
            color: white;
            border-radius: 50%;
            text-decoration: none;
            transition: all 0.3s;
            font-size: 1.2rem;
        }
        .social-link:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: translateY(-3px);
            color: white;
        }
        .footer-bottom {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: 30px;
        }
        .footer-credit {
            color: #888;
            text-decoration: none;
            border-bottom: 1px dotted #888;
            transition: color 0.3s;
        }
        .footer-credit:hover {
            color: #fff;
            border-bottom-color: #fff;
        }
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
        @media (max-width: 1024px) {
            .products-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        @media (max-width: 768px) {
            .products-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
            }
        }
        @media (max-width: 480px) {
            .products-grid {
                grid-template-columns: 1fr;
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
            footer { padding: 24px 0 16px; margin-top: 40px; }
            .footer-content { padding: 0 12px; }
            .footer-section { margin-bottom: 20px; }
            .footer-section h5 { font-size: 1rem; }
            .footer-bottom { padding-top: 16px; margin-top: 20px; }
            .footer-bottom p { font-size: 0.85rem; line-height: 1.5; }
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
        @media (max-width: 767px) {
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
        @media (min-width: 768px) {
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
    @stack('styles')
</head>
<body>
    <header class="header">
        <div class="header-content">
            @php
                $logoPath = \App\Models\Setting::get('logo_path');
                $logoText = \App\Models\Setting::get('logo_text', '/Namsa Florals');
            @endphp
            <a href="{{ route('home') }}" class="logo {{ $logoPath ? 'logo--image-only' : '' }}">
                @if($logoPath)
                    <img src="{{ asset('storage/' . $logoPath) }}" alt="{{ $logoText }}" class="logo-img">
                @else
                    <i class="bi bi-flower1"></i>
                    <span>{{ $logoText }}</span>
                @endif
            </a>
            <button type="button" class="mobile-search-toggle" id="mobileSearchToggle" aria-label="Search" title="Search products">
                <i class="bi bi-search"></i>
            </button>
            @php
                $headerCartCount = \App\Models\CartItem::where('session_id', session()->getId())->sum('quantity');
            @endphp
            <a href="{{ route('cart.index') }}" class="mobile-cart-link" id="mobileCartLink" aria-label="Cart" title="View cart">
                <i class="bi bi-cart3"></i>
                <span class="cart-badge {{ $headerCartCount > 0 ? '' : 'd-none' }}" id="cartBadgeMobile">{{ $headerCartCount }}</span>
            </a>
            <button type="button" class="nav-toggle" id="navToggle" aria-label="Toggle menu" aria-expanded="false">
                <i class="bi bi-list"></i>
            </button>
            <div class="mobile-search-bar" id="mobileSearchBar" role="search">
                <form action="{{ route('products.index') }}" method="GET" class="search-form">
                    <input type="text" name="search" placeholder="Search products..." value="{{ request('search') }}" class="search-input" aria-label="Search products">
                    <button type="submit" class="search-btn">Search</button>
                </form>
            </div>
            <div class="nav-group" id="navGroup">
                <ul class="nav-links">
                    <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a></li>
                    <li><a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}">Products</a></li>
                    <li><a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'active' : '' }}">Contact Us</a></li>
                    <li><a href="{{ route('promotion') }}" class="{{ request()->routeIs('promotion') ? 'active' : '' }}">Promotion</a></li>
                    <li><a href="{{ route('gallery') }}" class="{{ request()->routeIs('gallery') ? 'active' : '' }}">Gallery</a></li>
                    <li><a href="{{ route('terms') }}" class="{{ request()->routeIs('terms') ? 'active' : '' }}">Terms and Conditions</a></li>
                </ul>
                <div class="search-cart">
                    <form action="{{ route('products.index') }}" method="GET" class="search-form">
                        <input type="text" name="search" placeholder="Search products..." value="{{ request('search') }}" class="search-input">
                        <button type="submit" class="search-btn">Search</button>
                    </form>
                    <a href="{{ route('cart.index') }}" class="cart-link" id="cartLink">
                        Cart
                        <span class="cart-badge {{ $headerCartCount > 0 ? '' : 'd-none' }}" id="cartBadgeNav">{{ $headerCartCount }}</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main>
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show layout-alert" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show layout-alert" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <footer>
        <div class="footer-content">
            <div class="row">
                <div class="col-md-4 footer-section">
                    <h5>/Namsa Florals</h5>
                    <p style="color: #ccc; line-height: 1.6;">
                        Your premier destination for personalized flowers in Namibia. Fresh, hand-picked flowers for every occasion.
                    </p>
                </div>
                <div class="col-md-4 footer-section">
                    <h5>Quick Links</h5>
                    <ul style="list-style: none; padding: 0; color: #ccc;">
                        <li style="margin-bottom: 8px;">
                            <a href="{{ route('home') }}" style="color: #ccc; text-decoration: none;">Home</a>
                        </li>
                        <li style="margin-bottom: 8px;">
                            <a href="{{ route('products.index') }}" style="color: #ccc; text-decoration: none;">Products</a>
                        </li>
                        <li style="margin-bottom: 8px;">
                            <a href="{{ route('contact') }}" style="color: #ccc; text-decoration: none;">Contact Us</a>
                        </li>
                        <li style="margin-bottom: 8px;">
                            <a href="{{ route('promotion') }}" style="color: #ccc; text-decoration: none;">Promotion</a>
                        </li>
                        <li style="margin-bottom: 8px;">
                            <a href="{{ route('gallery') }}" style="color: #ccc; text-decoration: none;">Gallery</a>
                        </li>
                        <li style="margin-bottom: 8px;">
                            <a href="{{ route('terms') }}" style="color: #ccc; text-decoration: none;">Terms & Conditions</a>
                        </li>
                    </ul>
                </div>
                <div class="col-md-4 footer-section">
                    <h5>Follow Us</h5>
                    <p style="color: #ccc; margin-bottom: 15px;">Connect with us on social media</p>
                    <div class="social-links">
                        <a href="{{ config('social.facebook') }}" target="_blank" class="social-link" title="Facebook">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <a href="https://www.instagram.com/namsa.florals" target="_blank" class="social-link" title="Instagram">
                            <i class="bi bi-instagram"></i>
                        </a>
                        <a href="{{ config('social.tiktok') }}" target="_blank" class="social-link" title="TikTok">
                            <i class="bi bi-tiktok"></i>
                        </a>
                        <a href="{{ config('social.whatsapp') }}" target="_blank" class="social-link" title="WhatsApp">
                            <i class="bi bi-whatsapp"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p style="margin: 0; color: #ccc; text-align: center;">
                    &copy; {{ date('Y') }} /Namsa Florals. All rights reserved. | Beautiful flowers for every occasion | Website by <a href="https://datalani.com" target="_blank" class="footer-credit" style="color: #ccc; text-decoration: underline;">Datalani Technology</a>
                </p>
            </div>
        </div>
    </footer>

    <a href="#" class="scroll-top-btn" id="scrollTopBtn" aria-label="Scroll to top" title="Back to top">
        <i class="bi bi-arrow-up"></i>
    </a>
    <a href="{{ config('social.whatsapp') }}" target="_blank" rel="noopener noreferrer" class="float-whatsapp" aria-label="Chat on WhatsApp" title="Chat on WhatsApp">
        <i class="bi bi-whatsapp"></i>
    </a>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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
            function showToast(msg, isError) {
                var t = document.createElement('div');
                t.className = 'add-to-cart-toast' + (isError ? ' add-to-cart-toast-error' : '');
                t.textContent = msg;
                t.setAttribute('role', 'alert');
                document.body.appendChild(t);
                requestAnimationFrame(function() { t.classList.add('add-to-cart-toast-show'); });
                setTimeout(function() {
                    t.classList.remove('add-to-cart-toast-show');
                    setTimeout(function() { t.remove(); }, 300);
                }, 2500);
            }
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
                            var n = data.cart_count || 0;
                            cartBadges.forEach(function(b) {
                                b.textContent = n;
                                b.classList.toggle('d-none', !(n > 0));
                            });
                            showToast(data.message || 'Added to cart.');
                        } else {
                            showToast(data.message || 'Could not add to cart.', true);
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
            position: fixed; bottom: 120px; left: 50%; transform: translateX(-50%) translateY(20px);
            background: #333; color: #fff; padding: 12px 24px; border-radius: 8px; font-size: 0.95rem;
            z-index: 9999; opacity: 0; transition: opacity 0.3s, transform 0.3s; box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        .add-to-cart-toast.add-to-cart-toast-show { opacity: 1; transform: translateX(-50%) translateY(0); }
        .add-to-cart-toast-error { background: #dc3545; }
    </style>
    @stack('scripts')
</body>
</html>
