@extends('layouts.app')

@php
    $seoPage = 'gallery';
    $structuredData = [\App\Services\SeoService::generateStructuredData('breadcrumb', ['items' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => route('home')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Gallery', 'item' => route('gallery')],
    ]])];
@endphp

@section('title', 'Floral Journal - /Namsa Florals Namibia')

@push('styles')
<style>
    .gallery-page { max-width: 1240px; margin: 0 auto; padding: 58px 24px 20px; }
    .gallery-hero { display: grid; grid-template-columns: minmax(0,1fr) minmax(280px,.65fr); align-items: end; gap: 48px; margin-bottom: 42px; }
    .gallery-hero h1 { max-width: 720px; margin: 8px 0 0; font-size: clamp(2.5rem,6vw,5.2rem); font-weight: 800; line-height: .98; letter-spacing: -.055em; }
    .gallery-hero p { margin: 0; color: var(--muted); font-size: 1rem; line-height: 1.8; }
    .journal-grid { display: grid; grid-template-columns: repeat(2,minmax(0,1fr)); gap: 22px; }
    .journal-card { position: relative; min-height: 520px; padding: 0; overflow: hidden; background: #20171d; border: 0; border-radius: 24px; text-align: left; cursor: zoom-in; }
    .journal-card:first-child { grid-column: 1 / -1; min-height: 650px; }
    .journal-card img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; transition: transform .7s cubic-bezier(.2,.7,.2,1), filter .4s ease; }
    .journal-card::after { content: ''; position: absolute; inset: 35% 0 0; background: linear-gradient(180deg,transparent,rgba(24,14,20,.88)); }
    .journal-card:hover img { transform: scale(1.035); filter: saturate(1.05); }
    .journal-copy { position: absolute; z-index: 2; right: 0; bottom: 0; left: 0; padding: clamp(22px,4vw,38px); color: #fff; }
    .journal-number { display: block; margin-bottom: 10px; color: #ff9bc5; font-size: .7rem; font-weight: 800; letter-spacing: .15em; }
    .journal-copy h2 { margin: 0 0 7px; font-size: clamp(1.35rem,3vw,2.4rem); font-weight: 800; }
    .journal-copy p { max-width: 560px; margin: 0; color: rgba(255,255,255,.76); }
    .gallery-empty { padding: 90px 24px; text-align: center; background: #fff; border: 1px solid var(--border); border-radius: 24px; }
    .gallery-dialog { width: min(1120px,calc(100vw - 28px)); max-width: none; padding: 0; overflow: hidden; background: #171115; border: 0; border-radius: 22px; box-shadow: 0 32px 100px rgba(0,0,0,.5); }
    .gallery-dialog::backdrop { background: rgba(20,10,16,.82); backdrop-filter: blur(8px); }
    .gallery-dialog img { display: block; width: 100%; max-height: 78vh; object-fit: contain; background: #171115; }
    .gallery-dialog-copy { display: flex; align-items: center; justify-content: space-between; gap: 20px; padding: 18px 22px; color: #fff; }
    .gallery-dialog-copy h2 { margin: 0 0 3px; font-size: 1rem; }
    .gallery-dialog-copy p { margin: 0; color: rgba(255,255,255,.68); font-size: .8rem; }
    .gallery-close { display: grid; flex: 0 0 38px; width: 38px; height: 38px; place-items: center; color: #fff; background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.16); border-radius: 50%; }
    @media (max-width: 760px) {
        .gallery-page { padding: 38px 14px 12px; }
        .gallery-hero { grid-template-columns: 1fr; gap: 18px; }
        .journal-grid { grid-template-columns: 1fr; gap: 14px; }
        .journal-card, .journal-card:first-child { grid-column: auto; min-height: 440px; border-radius: 18px; }
    }
</style>
@endpush

@section('content')
<div class="gallery-page">
    <header class="gallery-hero">
        <div>
            <span class="section-kicker">The floral journal</span>
            <h1>Flowers as feeling, form, and atmosphere.</h1>
        </div>
        <p>A living edit of studio craft, celebration tables, and floral installations—made in Namibia, composed one stem at a time.</p>
    </header>

    @if($items->count())
        <div class="journal-grid">
            @foreach($items as $item)
                <button type="button" class="journal-card" data-gallery-open data-image="{{ $item->image_url }}" data-title="{{ $item->title ?: 'Floral story' }}" data-description="{{ $item->description }}" aria-label="Open {{ $item->title ?: 'gallery image' }}">
                    <img src="{{ $item->image_url }}" alt="{{ $item->title ?: 'Floral inspiration by /Namsa Florals' }}" loading="{{ $loop->first ? 'eager' : 'lazy' }}">
                    <span class="journal-copy">
                        <span class="journal-number">STORY {{ str_pad((string) ($loop->iteration + (($items->currentPage() - 1) * $items->perPage())), 2, '0', STR_PAD_LEFT) }}</span>
                        @if($item->title)<h2>{{ $item->title }}</h2>@endif
                        @if($item->description)<p>{{ $item->description }}</p>@endif
                    </span>
                </button>
            @endforeach
        </div>
        @if($items->hasPages())<div class="mt-5">{{ $items->links() }}</div>@endif
    @else
        <div class="gallery-empty">
            <span class="section-kicker">Coming into bloom</span>
            <h2 class="mt-2">New floral stories are being prepared.</h2>
            <p class="text-muted mb-4">In the meantime, explore the current flower collection.</p>
            <a href="{{ route('products.index') }}" class="buy-now-btn d-inline-flex text-decoration-none">Explore flowers</a>
        </div>
    @endif
</div>

<dialog class="gallery-dialog" id="galleryDialog" aria-labelledby="galleryDialogTitle">
    <img src="" alt="" id="galleryDialogImage">
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
    document.querySelectorAll('[data-gallery-open]').forEach(function (button) {
        button.addEventListener('click', function () {
            image.src = button.dataset.image;
            image.alt = button.dataset.title;
            title.textContent = button.dataset.title;
            description.textContent = button.dataset.description || '';
            dialog.showModal();
        });
    });
    document.getElementById('galleryDialogClose').addEventListener('click', function () { dialog.close(); });
    dialog.addEventListener('click', function (event) { if (event.target === dialog) dialog.close(); });
})();
</script>
@endsection
