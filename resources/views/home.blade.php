@extends('layouts.app')

@php
    $seoPage = 'home';
    $structuredData = [
        \App\Services\SeoService::generateStructuredData('website'),
        \App\Services\SeoService::generateStructuredData('organization'),
        \App\Services\SeoService::generateStructuredData('webpage_home'),
    ];
    $occasions = ['Weddings', 'Birthdays', 'Baby Showers', 'Corporate', 'Funerals', 'Parties'];
    $heroImages = $brandCarouselImages;
    $logoPath = \App\Models\Setting::get('logo_path');
    $logoText = \App\Models\Setting::get('logo_text', 'Kaleni Catering Services');
@endphp

@push('styles')
<style>
    /* ---------- Hero: full-bleed photo carousel, dark overlay, single CTA ---------- */
    .hero-full {
        position: relative;
        min-height: max(680px, min(94vh, 820px));
        display: flex;
        align-items: flex-end;
        overflow: hidden;
        background: #171310;
    }
    .hero-full-slide {
        position: absolute;
        inset: 0;
        background-size: cover;
        background-position: center;
        opacity: 0;
        transition: opacity 1.4s ease;
    }
    .hero-full-slide.is-active { opacity: 1; }
    /* Parallax: background stays fixed to the viewport while the page scrolls
       past it, same technique used for the footer background (desktop only —
       background-attachment: fixed is unreliable and can jank on mobile). */
    @media (min-width: 992px) {
        .hero-full-slide {
            background-attachment: fixed;
        }
    }
    .hero-full-overlay {
        position: absolute;
        inset: 0;
        background:
            linear-gradient(180deg, rgba(8,5,4,.5) 0%, rgba(8,5,4,.62) 40%, rgba(6,4,3,.96) 100%),
            linear-gradient(90deg, rgba(6,4,3,.5), transparent 60%);
    }
    .hero-full-content {
        position: relative;
        z-index: 2;
        width: min(720px, calc(100% - 48px));
        margin: 0 auto;
        padding-bottom: clamp(48px, 8vw, 88px);
        text-align: center;
    }
    .hero-brand-name {
        margin: 0 0 2px;
        font-family: 'Great Vibes', cursive;
        font-size: clamp(3rem, 9vw, 5.6rem);
        font-weight: 400;
        line-height: 1;
        color: var(--secondary-color);
        background: linear-gradient(100deg, #d6900f 0%, #fff3c4 20%, #F8AD27 42%, #fffbe8 58%, #d6900f 100%);
        background-size: 240% auto;
        background-position: 0 center;
        background-clip: text;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        filter: drop-shadow(0 2px 24px rgba(248, 173, 39, .55));
        animation: heroBrandShine 5s linear infinite;
    }
    @keyframes heroBrandShine {
        to { background-position: -240% center; }
    }
    @media (prefers-reduced-motion: reduce) {
        .hero-brand-name { animation: none; }
    }
    .hero-logo {
        display: block;
        width: clamp(64px, 9vw, 92px);
        height: clamp(64px, 9vw, 92px);
        margin: 6px auto 18px;
        object-fit: cover;
        border-radius: 50%;
        border: 3px solid rgba(248,173,39,.65);
        box-shadow: 0 10px 30px rgba(0,0,0,.45);
        background: #fff;
    }
    .hero-full-title {
        margin: 0 0 14px;
        color: #fff;
        font-size: clamp(1.5rem, 3vw, 2rem);
        font-weight: 700;
        line-height: 1.15;
        letter-spacing: -.01em;
    }
    .hero-full-lede {
        max-width: 46ch;
        margin: 0 auto 30px;
        color: rgba(255,255,255,.8);
        font-size: 1rem;
        line-height: 1.7;
    }
    .hero-full-actions { display: flex; flex-wrap: wrap; justify-content: center; align-items: center; gap: 18px; }
    .hero-full-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 54px;
        padding: 0 32px;
        color: #4A0814;
        background: var(--secondary-color);
        border-radius: 999px;
        font-size: .92rem;
        font-weight: 800;
        text-decoration: none;
        box-shadow: 0 18px 38px rgba(0,0,0,.4);
    }
    .hero-full-btn:hover { background: #ffbd4a; color: #4A0814; }
    .hero-full-link { color: #fff; font-size: .88rem; font-weight: 700; text-decoration: underline; text-underline-offset: 3px; }
    .hero-full-link:hover { color: var(--secondary-color); }
    .hero-full-dots { margin-top: 26px; display: flex; justify-content: center; gap: 8px; }
    .hero-full-dot { width: 8px; height: 8px; padding: 0; border: 0; border-radius: 999px; background: rgba(255,255,255,.4); transition: width .25s ease, background .25s ease; }
    .hero-full-dot.is-active { width: 24px; background: var(--secondary-color); }
    @media (max-width: 575.98px) {
        .hero-full { min-height: 78vh; }
    }

    /* ---------- Trust bar ---------- */
    .home-trust {
        width: min(1120px, calc(100% - 48px));
        margin: 40px auto 64px;
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 18px;
        box-shadow: 0 14px 40px rgba(41,33,31,.08);
    }
    .home-trust-item { min-width: 0; padding: 20px 24px; display: flex; align-items: center; gap: 13px; }
    .home-trust-item + .home-trust-item { border-left: 1px solid var(--border); }
    .home-trust-icon { width: 42px; height: 42px; flex: 0 0 42px; display: grid; place-items: center; color: var(--primary-dark); background: var(--primary-soft); border-radius: 12px; font-size: 1.08rem; }
    .home-trust-item strong { display: block; color: var(--ink); font-size: .82rem; font-weight: 800; }
    .home-trust-item > span:last-child > span { display: block; margin-top: 2px; color: var(--muted); font-size: .72rem; }
    @media (max-width: 767.98px) {
        .home-trust { grid-template-columns: 1fr; }
        .home-trust-item + .home-trust-item { border-top: 1px solid var(--border); border-left: 0; }
    }

    /* ---------- Food of the Day ---------- */
    .home-fotd {
        width: min(1240px, calc(100% - 48px));
        margin: 0 auto 76px;
        padding: clamp(26px, 4vw, 40px);
        display: grid;
        grid-template-columns: minmax(0, 220px) 1fr auto;
        align-items: center;
        gap: clamp(20px, 4vw, 40px);
        background: linear-gradient(120deg, var(--primary-color), var(--primary-dark));
        border-radius: 26px;
        box-shadow: 0 26px 60px rgba(104,11,28,.22);
        color: #fff;
        overflow: hidden;
    }
    .home-fotd-image { width: 100%; aspect-ratio: 1 / 1; object-fit: cover; border-radius: 18px; box-shadow: 0 12px 28px rgba(0,0,0,.25); }
    .home-fotd-badge { display: inline-flex; align-items: center; gap: 7px; margin-bottom: 10px; padding: 5px 12px; color: #4A0814; background: var(--secondary-color); border-radius: 999px; font-size: .68rem; font-weight: 800; letter-spacing: .1em; text-transform: uppercase; }
    .home-fotd-title { margin: 0 0 8px; color: #fff; font-size: clamp(1.4rem, 3vw, 2rem); }
    .home-fotd-desc { margin: 0; color: rgba(255,255,255,.85); font-size: .92rem; line-height: 1.65; max-width: 46ch; }
    .home-fotd-cta { display: flex; flex-direction: column; align-items: flex-start; gap: 12px; }
    .home-fotd-price { font-size: 1.5rem; font-weight: 800; color: #fff; }
    .home-fotd-btn { padding: 12px 22px; color: var(--primary-dark) !important; background: #fff; border-radius: 999px; font-weight: 800; text-decoration: none; white-space: nowrap; }
    .home-fotd-btn:hover { background: var(--secondary-color); color: #4A0814 !important; }
    .home-fotd-empty { text-align: center; padding: 44px 24px; }
    .home-fotd-empty p { margin: 0 0 14px; color: rgba(255,255,255,.85); }
    @media (max-width: 767.98px) {
        .home-fotd { grid-template-columns: 120px 1fr; grid-template-areas: "img title" "img cta"; }
        .home-fotd-image { grid-area: img; aspect-ratio: 1/1.3; }
        .home-fotd-title, .home-fotd-desc, .home-fotd-badge { grid-area: title; }
        .home-fotd-cta { grid-area: cta; }
    }
    @media (max-width: 575.98px) {
        .home-fotd { grid-template-columns: 1fr; text-align: center; }
        .home-fotd-image { max-width: 220px; margin: 0 auto; }
        .home-fotd-cta { align-items: center; }
        .home-fotd-desc { margin-inline: auto; }
    }

    .home-products {
        width: min(1240px, calc(100% - 48px));
        margin: 0 auto;
        padding: clamp(28px, 4vw, 48px);
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 28px;
        box-shadow: 0 20px 55px rgba(41,33,31,.06);
    }
    .home-products .products-grid { margin-top: 0; }
    @media (min-width: 860px) {
        .home-products .products-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    }
    /* Light card variant for the homepage only — every other page (menu,
       cart, item recommendations) keeps the dark card treatment. */
    .home-products .product-card {
        background: #fff;
        border: 1px solid var(--border);
        box-shadow: 0 10px 26px rgba(41,33,31,.06);
    }
    .home-products .product-card:hover {
        border-color: var(--border);
        box-shadow: 0 16px 34px rgba(41,33,31,.1);
    }
    .home-products .product-media-link { background: #f4ece7; }
    .home-products .product-name-link { color: var(--ink); }
    .home-products .product-name-link:hover { color: var(--primary-color); }
    .home-products .price-dots { border-bottom-color: rgba(41,33,31,.2); }
    .home-products .product-price { color: var(--primary-dark); }

    /* ---------- Quote / enquiry form (Fresh 'n Wild-style centerpiece) ---------- */
    .home-quote {
        width: min(1240px, calc(100% - 48px));
        margin: 84px auto 0;
        display: grid;
        grid-template-columns: minmax(0, .85fr) minmax(0, 1.15fr);
        overflow: hidden;
        background: #221a16;
        border-radius: 26px;
        box-shadow: 0 26px 65px rgba(34,26,22,.22);
    }
    .home-quote-copy { padding: clamp(30px,5vw,52px); color: #fff; }
    .home-quote-copy .section-kicker { color: var(--secondary-color); }
    .home-quote-copy h2 { margin: 10px 0 14px; color: #fff; font-size: clamp(1.7rem, 3vw, 2.3rem); line-height: 1.12; }
    .home-quote-copy > p { margin: 0 0 22px; color: rgba(255,255,255,.7); line-height: 1.7; }
    .home-quote-occasions { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 26px; }
    .home-quote-occasions span { padding: 6px 12px; color: #fff; background: rgba(255,255,255,.07); border: 1px solid rgba(255,255,255,.14); border-radius: 999px; font-size: .74rem; font-weight: 700; }
    .home-quote-contact { display: grid; gap: 10px; padding-top: 22px; border-top: 1px solid rgba(255,255,255,.12); }
    .home-quote-contact a { display: flex; align-items: center; gap: 10px; color: #fff; text-decoration: none; font-size: .88rem; font-weight: 700; }
    .home-quote-contact i { color: var(--secondary-color); }
    .home-quote-form-wrap { padding: clamp(28px,5vw,48px); background: #fff; }
    .home-quote-form-wrap .form-label { font-weight: 750; font-size: .78rem; margin-bottom: 6px; }
    .home-quote-form-wrap .form-control, .home-quote-form-wrap .form-select { min-height: 46px; border-color: var(--border); border-radius: 11px; }
    .home-quote-submit { min-height: 50px; width: 100%; margin-top: 6px; }
    @media (max-width: 860px) {
        .home-quote { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
<section class="hero-full" data-hero-carousel data-interval="5000" aria-label="Kaleni Catering Services">
    @foreach($heroImages as $i => $image)
        <div class="hero-full-slide {{ $loop->first ? 'is-active' : '' }}" data-hero-slide style="background-image: url('{{ asset($image) }}')"></div>
    @endforeach
    <div class="hero-full-overlay"></div>
    <div class="hero-full-content">
        <p class="hero-brand-name" aria-hidden="true">Kaleni Catering Services</p>
        @if($logoPath)
            <img src="{{ asset('storage/' . $logoPath) }}" alt="{{ $logoText }}" class="hero-logo">
        @else
            <img src="{{ asset('images/kaleni/brand/kaleni-logo.jpg') }}" alt="{{ $logoText }}" class="hero-logo">
        @endif
        <h1 class="hero-full-title">Planning an event?</h1>
        <p class="hero-full-lede">Home-style Namibian catering for weddings, birthdays, corporate functions and everything in between. Cooked fresh by Chef K, delivered free around Windhoek.</p>
        <div class="hero-full-actions">
            <a href="{{ route('menu.index') }}" class="hero-full-btn">View the menu</a>
            <a href="#get-a-quote" class="hero-full-link">or get a quote for your event</a>
        </div>
        @if(count($heroImages) > 1)
            <div class="hero-full-dots" role="tablist" aria-label="Featured dishes">
                @foreach($heroImages as $i => $image)
                    <button type="button" class="hero-full-dot {{ $loop->first ? 'is-active' : '' }}" data-hero-dot="{{ $i }}" role="tab" aria-selected="{{ $loop->first ? 'true' : 'false' }}" aria-label="Slide {{ $i + 1 }}"></button>
                @endforeach
            </div>
        @endif
    </div>
</section>

<section class="home-trust" aria-label="Why book Kaleni Catering Services">
    <div class="home-trust-item">
        <span class="home-trust-icon"><i class="bi bi-truck" aria-hidden="true"></i></span>
        <span><strong>Free delivery</strong><span>Across Windhoek</span></span>
    </div>
    <div class="home-trust-item">
        <span class="home-trust-icon"><i class="bi bi-egg-fried" aria-hidden="true"></i></span>
        <span><strong>Home-style cooking</strong><span>Every dish made fresh</span></span>
    </div>
    <div class="home-trust-item">
        <span class="home-trust-icon"><i class="bi bi-chat-dots" aria-hidden="true"></i></span>
        <span><strong>Personal service</strong><span>Friendly help on WhatsApp</span></span>
    </div>
</section>

@if($foodOfTheDay)
<section class="home-fotd" aria-labelledby="fotd-title">
    <img src="{{ $foodOfTheDay->effective_image_url ?? asset('images/kaleni/brand/kaleni-logo.jpg') }}" alt="{{ $foodOfTheDay->effective_title }}" class="home-fotd-image">
    <div>
        <span class="home-fotd-badge"><i class="bi bi-stars" aria-hidden="true"></i> Food of the Day &middot; {{ \Illuminate\Support\Carbon::today()->format('l, j M') }}</span>
        <h2 class="home-fotd-title" id="fotd-title">{{ $foodOfTheDay->effective_title }}</h2>
        @if($foodOfTheDay->effective_description)
            <p class="home-fotd-desc">{{ $foodOfTheDay->effective_description }}</p>
        @endif
    </div>
    <div class="home-fotd-cta">
        @if($foodOfTheDay->effective_price !== null)
            <span class="home-fotd-price">N$ {{ number_format((float) $foodOfTheDay->effective_price, 2) }}</span>
        @endif
        @if($foodOfTheDay->menu_item_id)
            <a href="{{ route('menu.show', $foodOfTheDay->menu_item_id) }}" class="home-fotd-btn">Order today's special</a>
        @else
            <a href="{{ route('special-requests.create') }}" class="home-fotd-btn">Ask about today's special</a>
        @endif
    </div>
</section>
@endif

<section class="home-products" aria-labelledby="featured-menu-title">
    <div class="section-heading">
        <div class="section-heading-copy">
            <span class="section-kicker">Curated for you</span>
            <h2 class="section-title" id="featured-menu-title">Featured menu</h2>
            <p class="section-subtitle">Hearty, home-style dishes for lunch, dinner, and every event in between.</p>
        </div>
        <a href="{{ route('menu.index') }}" class="text-link">View full menu <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
    </div>

    @if($featuredMenuItems->count() > 0)
        <div class="products-grid">
            @foreach($featuredMenuItems as $item)
                @include('menu._card', ['item' => $item])
            @endforeach
        </div>
    @else
        <div class="text-center p-5" style="background: var(--bg); border-radius: 18px;">
            <p class="text-muted mb-3">New menu items are being prepared. Please check back soon.</p>
            <a href="{{ route('contact') }}" class="text-link justify-content-center">Contact Kaleni Catering <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
        </div>
    @endif
</section>

<section class="home-quote" id="get-a-quote" aria-labelledby="quote-title">
    <div class="home-quote-copy">
        <span class="section-kicker">Get a quote</span>
        <h2 id="quote-title">Tell us about your event.</h2>
        <p>Weddings, birthdays, corporate functions, baby showers, funerals, or just a big get-together. Describe what you need and Chef K will follow up with a quote.</p>
        <div class="home-quote-occasions" aria-label="Occasions">
            @foreach($occasions as $occasion)
                <span>{{ $occasion }}</span>
            @endforeach
        </div>
        <div class="home-quote-contact">
            <a href="tel:{{ preg_replace('/[^+0-9]/', '', config('contact.phone')) }}"><i class="bi bi-telephone"></i> {{ config('contact.phone') }}</a>
            <a href="mailto:{{ config('contact.email_info') }}"><i class="bi bi-envelope"></i> {{ config('contact.email_info') }}</a>
            <a href="{{ config('social.whatsapp') }}" target="_blank" rel="noopener"><i class="bi bi-whatsapp"></i> Chat on WhatsApp</a>
        </div>
    </div>
    <div class="home-quote-form-wrap">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <form action="{{ route('special-requests.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="quote_name" class="form-label">Full name *</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="quote_name" name="name" value="{{ old('name') }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="quote_phone" class="form-label">Phone number *</label>
                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="quote_phone" name="phone" value="{{ old('phone') }}" required>
                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label for="quote_email" class="form-label">Email address *</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="quote_email" name="email" value="{{ old('email') }}" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="quote_event_date" class="form-label">Event date *</label>
                    <input type="date" class="form-control @error('event_date') is-invalid @enderror" id="quote_event_date" name="event_date" min="{{ now()->toDateString() }}" value="{{ old('event_date') }}" required>
                    @error('event_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="quote_guest_count" class="form-label">Guests *</label>
                    <input type="number" min="1" max="5000" class="form-control @error('guest_count') is-invalid @enderror" id="quote_guest_count" name="guest_count" value="{{ old('guest_count') }}" required>
                    @error('guest_count')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="quote_occasion" class="form-label">Occasion *</label>
                    <select class="form-select @error('occasion') is-invalid @enderror" id="quote_occasion" name="occasion" required>
                        <option value="">Choose one</option>
                        @foreach($occasions as $occasion)
                            <option value="{{ $occasion }}" {{ old('occasion') === $occasion ? 'selected' : '' }}>{{ $occasion }}</option>
                        @endforeach
                        <option value="Other" {{ old('occasion') === 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('occasion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="quote_budget_range" class="form-label">Budget range *</label>
                    <input type="text" class="form-control @error('budget_range') is-invalid @enderror" id="quote_budget_range" name="budget_range" value="{{ old('budget_range') }}" placeholder="e.g. N$ 2,000 - N$ 4,000" required>
                    @error('budget_range')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label for="quote_details" class="form-label">Tell us what you need *</label>
                    <textarea class="form-control @error('details') is-invalid @enderror" id="quote_details" name="details" rows="4" placeholder="Number of guests, menu preferences, venue, budget…" required>{{ old('details') }}</textarea>
                    @error('details')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <button type="submit" class="buy-now-btn home-quote-submit"><i class="bi bi-envelope me-1"></i> Request a quote</button>
        </form>
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
        var interval = Number(carousel.getAttribute('data-interval')) || 5000;
        var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        var current = 0;
        var timer = null;

        if (slides.length < 2 || reduceMotion) return;

        function showSlide(index) {
            current = (index + slides.length) % slides.length;
            slides.forEach(function (slide, i) { slide.classList.toggle('is-active', i === current); });
            dots.forEach(function (dot, i) {
                dot.classList.toggle('is-active', i === current);
                dot.setAttribute('aria-selected', i === current ? 'true' : 'false');
            });
        }

        function schedule() {
            window.clearTimeout(timer);
            timer = window.setTimeout(function () { showSlide(current + 1); schedule(); }, interval);
        }

        dots.forEach(function (dot) {
            dot.addEventListener('click', function () {
                showSlide(Number(dot.getAttribute('data-hero-dot')));
                schedule();
            });
        });

        document.addEventListener('visibilitychange', function () {
            if (document.hidden) window.clearTimeout(timer); else schedule();
        });

        schedule();
    })();
</script>
@endpush
