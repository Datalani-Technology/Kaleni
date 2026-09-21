@extends('layouts.app')

@php
    $seoPage = 'gallery';
    $structuredData = [\App\Services\SeoService::generateStructuredData('breadcrumb', ['items' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => route('home')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Gallery', 'item' => route('gallery')],
    ]])];
@endphp

@section('title', 'Gallery - Kaleni Catering Services Namibia')

@push('styles')
<style>
    /* ---------- Dark showcase section ---------- */
    .gallery-dark { background: #17110d; padding: 72px 24px 80px; }
    .gallery-dark-inner { max-width: 1180px; margin: 0 auto; }
    .gallery-dark-head { max-width: 620px; margin: 0 auto 46px; text-align: center; }
    .gallery-dark-head .section-kicker { justify-content: center; color: var(--secondary-color); }
    .gallery-dark-head h1 { margin: 10px 0 14px; color: #fff; font-size: clamp(1.8rem, 4vw, 2.5rem); font-weight: 800; line-height: 1.15; }
    .gallery-dark-head p { margin: 0; color: rgba(255,255,255,.68); line-height: 1.75; }

    /* ---------- Occasion filter chips ---------- */
    .gallery-filters { display: flex; flex-wrap: wrap; justify-content: center; gap: 10px; margin: -14px 0 40px; }
    .gallery-filter-chip {
        display: inline-flex; align-items: center; padding: 9px 20px; border-radius: 999px;
        border: 1px solid rgba(255,255,255,.18); color: rgba(255,255,255,.72);
        font-size: .82rem; font-weight: 700; text-decoration: none; transition: all .2s ease;
    }
    .gallery-filter-chip:hover { border-color: var(--secondary-color); color: #fff; }
    .gallery-filter-chip.is-active { background: var(--secondary-color); border-color: var(--secondary-color); color: #4A0814; }

    /* ---------- Masonry grid (mixed aspect ratios, modern Pinterest-style) ---------- */
    .gallery-masonry { column-count: 3; column-gap: 20px; margin-bottom: 20px; }
    .gallery-masonry-item {
        position: relative; display: block; width: 100%; margin-bottom: 20px; padding: 0;
        break-inside: avoid; overflow: hidden; background: #201712; border: 0; border-radius: 16px;
        cursor: zoom-in; appearance: none; text-align: left;
    }
    .gallery-masonry-item img { display: block; width: 100%; height: auto; transition: transform .6s cubic-bezier(.2,.7,.2,1); }
    .gallery-masonry-item:hover img { transform: scale(1.045); }
    .gallery-masonry-item.has-copy::after { content: ''; position: absolute; inset: 55% 0 0; background: linear-gradient(180deg,transparent,rgba(15,10,8,.88)); }
    .masonry-copy { position: absolute; left: 0; right: 0; bottom: 0; z-index: 1; padding: 18px; color: #fff; }
    .masonry-copy h3 { margin: 0 0 4px; color: #fff; font-size: 1.05rem; font-weight: 800; }
    .masonry-copy p { margin: 0; color: rgba(255,255,255,.72); font-size: .82rem; line-height: 1.5; }

    .gallery-cta { margin-top: 56px; text-align: center; }
    .gallery-cta h3 { margin: 0 0 8px; color: #fff; font-size: clamp(1.3rem, 3vw, 1.7rem); font-weight: 800; }
    .gallery-cta p { margin: 0 0 22px; color: rgba(255,255,255,.65); }
    .gallery-cta-actions { display: flex; flex-wrap: wrap; justify-content: center; gap: 12px; }
    .gallery-cta .buy-now-btn { width: auto; min-width: 190px; padding: 0 26px; text-decoration: none; }
    .gallery-cta-secondary {
        display: inline-flex; align-items: center; justify-content: center; min-height: 52px; padding: 0 26px;
        color: #fff; border: 1px solid rgba(255,255,255,.28); border-radius: 999px;
        font-size: .88rem; font-weight: 750; text-decoration: none;
    }
    .gallery-cta-secondary:hover { border-color: var(--secondary-color); color: var(--secondary-color); }

    /* ---------- Reflection section (real voice, no fabricated reviews) ---------- */
    .gallery-quote { padding: 70px 24px; text-align: center; background: var(--bg); }
    .gallery-quote-mark { color: var(--secondary-color); font-size: 2.6rem; line-height: 1; font-family: Georgia, serif; }
    .gallery-quote blockquote { max-width: 720px; margin: 14px auto 20px; color: var(--ink); font-size: clamp(1.1rem, 2.4vw, 1.4rem); font-weight: 650; line-height: 1.6; }
    .gallery-quote-attribution { color: var(--muted); font-size: .85rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; }

    .gallery-empty { max-width: 1180px; margin: 0 auto; padding: 90px 24px; text-align: center; background: #201712; border-radius: 24px; color: #fff; }
    .gallery-empty .section-kicker { justify-content: center; color: var(--secondary-color); }
    .gallery-empty h1 { color: #fff; }
    .gallery-empty p { color: rgba(255,255,255,.65); }

    .gallery-dialog { width: min(1120px,calc(100vw - 28px)); max-width: none; padding: 0; overflow: hidden; background: #17110d; border: 0; border-radius: 22px; box-shadow: 0 32px 100px rgba(0,0,0,.5); }
    .gallery-dialog::backdrop { background: rgba(20,14,10,.82); backdrop-filter: blur(8px); }
    .gallery-dialog img { display: block; width: 100%; max-height: 78vh; object-fit: contain; background: #17110d; }
    .gallery-dialog-copy { display: flex; align-items: center; justify-content: space-between; gap: 20px; padding: 18px 22px; color: #fff; }
    .gallery-dialog-copy h2 { margin: 0 0 3px; color: #fff; font-size: 1rem; }
    .gallery-dialog-copy p { margin: 0; color: rgba(255,255,255,.68); font-size: .8rem; }
    .gallery-close { display: grid; flex: 0 0 38px; width: 38px; height: 38px; place-items: center; color: #fff; background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.16); border-radius: 50%; }
    .gallery-dialog-image-wrap { position: relative; }
    .gallery-nav {
        position: absolute; top: 50%; transform: translateY(-50%); z-index: 2;
        display: grid; place-items: center; width: 44px; height: 44px; border-radius: 50%;
        color: #fff; background: rgba(20,14,10,.55); border: 1px solid rgba(255,255,255,.2);
        font-size: 1.1rem; transition: background .2s ease;
    }
    .gallery-nav:hover { background: rgba(20,14,10,.85); }
    .gallery-nav-prev { left: 14px; }
    .gallery-nav-next { right: 14px; }

    @media (max-width: 900px) {
        .gallery-masonry { column-count: 2; }
    }
    @media (max-width: 760px) {
        .gallery-dark { padding: 48px 16px 60px; }
    }
    @media (max-width: 520px) {
        .gallery-masonry { column-count: 1; }
    }
</style>
@endpush

@section('content')
<div class="gallery-dark">
    <div class="gallery-dark-inner">
        @if($items->count())
            <div class="gallery-dark-head">
                <span class="section-kicker">From our kitchen, for every occasion</span>
                <h1>Home-style food, made to be shared</h1>
                <p>A living edit of platters, packed meals, and event spreads, cooked fresh in Windhoek one pot at a time, catered for weddings, corporate days, braais, and birthdays alike.</p>
            </div>

            @if($availableOccasions->isNotEmpty())
                <nav class="gallery-filters" aria-label="Filter gallery by occasion">
                    <a href="{{ route('gallery') }}" class="gallery-filter-chip {{ !$activeOccasion ? 'is-active' : '' }}">All</a>
                    @foreach($availableOccasions as $occasion)
                        <a href="{{ route('gallery', ['occasion' => $occasion]) }}" class="gallery-filter-chip {{ $activeOccasion === $occasion ? 'is-active' : '' }}">{{ $occasion }}</a>
                    @endforeach
                </nav>
            @endif

            <div class="gallery-masonry">
                @foreach($items as $index => $cell)
                    <button type="button" class="gallery-masonry-item {{ ($cell->title || $cell->description) ? 'has-copy' : '' }}" data-gallery-open data-image="{{ $cell->image_url }}" data-title="{{ $cell->title ?: ($cell->occasion ?: 'Kitchen story') }}" data-description="{{ $cell->description }}" aria-label="Open {{ $cell->title ?: 'gallery image' }}">
                        <img src="{{ $cell->image_url }}" alt="{{ $cell->title ?: 'From the Kaleni Catering Services kitchen' }}" loading="{{ $index === 0 ? 'eager' : 'lazy' }}">
                        @if($cell->title || $cell->description)
                            <span class="masonry-copy">
                                @if($cell->title)<h3>{{ $cell->title }}</h3>@endif
                                @if($cell->description)<p>{{ $cell->description }}</p>@endif
                            </span>
                        @endif
                    </button>
                @endforeach
            </div>

            @if($items->hasPages())
                <div class="mt-5 text-center">{{ $items->links() }}</div>
            @endif

            <div class="gallery-cta">
                <h3>Inspired by what you see?</h3>
                <p>Let Chef K bring this same home-style cooking to your next event.</p>
                <div class="gallery-cta-actions">
                    <a href="{{ route('special-requests.create') }}" class="buy-now-btn">Get a quote</a>
                    <a href="{{ route('contact') }}" class="gallery-cta-secondary">Contact us</a>
                </div>
            </div>
        @else
            <div class="gallery-empty">
                <span class="section-kicker">Coming to the table</span>
                <h1 class="mt-2">New kitchen stories are being prepared.</h1>
                <p class="mb-4">In the meantime, explore the current menu.</p>
                <a href="{{ route('menu.index') }}" class="buy-now-btn d-inline-flex text-decoration-none" style="width:auto; padding:0 26px;">Explore the menu</a>
            </div>
        @endif
    </div>
</div>

<section class="gallery-quote">
    <span class="gallery-quote-mark" aria-hidden="true">&ldquo;</span>
    <blockquote>Every dish tells a story. We can't wait to help you tell yours.</blockquote>
    <p class="gallery-quote-attribution">Chef K &middot; Kaleni Catering Services</p>
</section>

<dialog class="gallery-dialog" id="galleryDialog" aria-labelledby="galleryDialogTitle">
    <div class="gallery-dialog-image-wrap">
        <button type="button" class="gallery-nav gallery-nav-prev" id="galleryDialogPrev" aria-label="Previous photo"><i class="bi bi-chevron-left"></i></button>
        <img src="" alt="" id="galleryDialogImage">
        <button type="button" class="gallery-nav gallery-nav-next" id="galleryDialogNext" aria-label="Next photo"><i class="bi bi-chevron-right"></i></button>
    </div>
    <div class="gallery-dialog-copy">
        <div><h2 id="galleryDialogTitle"></h2><p id="galleryDialogDescription"></p></div>
        <button type="button" class="gallery-close" id="galleryDialogClose" aria-label="Close image"><i class="bi bi-x-lg"></i></button>
    </div>
</dialog>

<script>
(function () {
    var dialog = document.getElementById('galleryDialog');
    if (!dialog || typeof dialog.showModal !== 'function') return;
    var image = document.getElementById('galleryDialogImage');
    var title = document.getElementById('galleryDialogTitle');
    var description = document.getElementById('galleryDialogDescription');
    var buttons = Array.prototype.slice.call(document.querySelectorAll('[data-gallery-open]'));
    var currentIndex = 0;

    function openAt(index) {
        currentIndex = (index + buttons.length) % buttons.length;
        var button = buttons[currentIndex];
        image.src = button.dataset.image;
        image.alt = button.dataset.title;
        title.textContent = button.dataset.title;
        description.textContent = button.dataset.description || '';
    }

    buttons.forEach(function (button, index) {
        button.addEventListener('click', function () {
            openAt(index);
            dialog.showModal();
        });
    });

    var hasMultiple = buttons.length > 1;
    document.getElementById('galleryDialogPrev').style.display = hasMultiple ? '' : 'none';
    document.getElementById('galleryDialogNext').style.display = hasMultiple ? '' : 'none';
    document.getElementById('galleryDialogPrev').addEventListener('click', function () { openAt(currentIndex - 1); });
    document.getElementById('galleryDialogNext').addEventListener('click', function () { openAt(currentIndex + 1); });
    dialog.addEventListener('keydown', function (event) {
        if (event.key === 'ArrowLeft') openAt(currentIndex - 1);
        if (event.key === 'ArrowRight') openAt(currentIndex + 1);
    });

    document.getElementById('galleryDialogClose').addEventListener('click', function () { dialog.close(); });
    dialog.addEventListener('click', function (event) { if (event.target === dialog) dialog.close(); });
})();
</script>
@endsection
