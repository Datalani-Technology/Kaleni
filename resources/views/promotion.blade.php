@extends('layouts.app')

@php($seoPage = 'promotion')

@section('title', 'Promotions & Packages - Kaleni Catering Services')

@push('styles')
<style>
    .promotion-page { max-width: 1240px; margin: 0 auto; padding: 58px 24px 20px; }
    .promotion-hero { display: grid; grid-template-columns: minmax(0,1fr) minmax(340px,.78fr); overflow: hidden; background: #21160f; border-radius: 30px; box-shadow: 0 24px 70px rgba(33,22,15,.18); }
    .promotion-copy { align-self: center; padding: clamp(36px,6vw,74px); color: #fff; }
    .promotion-copy .section-kicker { color: #F8AD27; }
    .promotion-copy h1 { max-width: 680px; margin: 12px 0 18px; color: #fff; font-size: clamp(2.5rem,5.5vw,5.2rem); font-weight: 800; line-height: .98; letter-spacing: -.055em; }
    .promotion-copy p { max-width: 600px; margin: 0 0 26px; color: rgba(255,255,255,.7); line-height: 1.75; }
    .promotion-actions { display: flex; flex-wrap: wrap; gap: 10px; }
    .promotion-actions .buy-now-btn { width: auto; min-width: 180px; text-decoration: none; }
    .promotion-secondary { display: inline-flex; min-height: 48px; align-items: center; justify-content: center; gap: 8px; padding: 0 18px; color: #fff; border: 1px solid rgba(255,255,255,.24); border-radius: 11px; font-size: .82rem; font-weight: 800; text-decoration: none; }
    .promotion-art { position: relative; min-height: 610px; overflow: hidden; }
    .promotion-art::after { content: ''; position: absolute; inset: 0; background: linear-gradient(90deg,#21160f 0%,transparent 28%), linear-gradient(0deg,rgba(33,22,15,.3),transparent 45%); }
    .promotion-art img { width: 100%; height: 100%; object-fit: cover; }
    .promotion-benefits { display: grid; grid-template-columns: repeat(3,minmax(0,1fr)); gap: 16px; margin-top: 24px; }
    .promotion-benefit { padding: 24px; background: #fff; border: 1px solid var(--border); border-radius: 18px; }
    .promotion-benefit i { display: grid; width: 42px; height: 42px; margin-bottom: 18px; place-items: center; color: var(--primary-dark); background: var(--primary-soft); border-radius: 12px; }
    .promotion-benefit h2 { margin: 0 0 7px; font-size: 1rem; font-weight: 800; }
    .promotion-benefit p { margin: 0; color: var(--muted); font-size: .82rem; line-height: 1.65; }

    /* ---------- Ready-made packages: the actual "select a package" action ---------- */
    .packages-section { margin-top: 56px; }
    .packages-empty { padding: 48px 24px; text-align: center; background: var(--bg); border-radius: 18px; }

    /* ---------- Custom package callout: the other action (bespoke quote) ---------- */
    .custom-callout { margin-top: 44px; display: grid; grid-template-columns: auto 1fr auto; align-items: center; gap: 22px; padding: clamp(22px,4vw,34px); background: var(--primary-soft); border: 1px solid #ecd9a8; border-radius: 22px; }
    .custom-callout-icon { display: grid; flex: 0 0 auto; width: 56px; height: 56px; place-items: center; color: #fff; background: var(--primary-color); border-radius: 16px; font-size: 1.4rem; }
    .custom-callout h2 { margin: 0 0 4px; font-size: 1.15rem; font-weight: 800; }
    .custom-callout p { margin: 0; color: var(--muted); font-size: .88rem; max-width: 60ch; }
    .custom-callout .buy-now-btn { width: auto; min-width: 200px; padding: 0 22px; text-decoration: none; }

    .catalog-panel { margin-top: 44px; padding: clamp(22px,4vw,42px); background: #fff; border: 1px solid var(--border); border-radius: 24px; box-shadow: 0 14px 38px rgba(41,33,31,.06); }
    .catalog-panel-head { display: flex; align-items: end; justify-content: space-between; gap: 20px; margin-bottom: 20px; }
    .catalog-panel h2 { margin: 5px 0 0; font-size: clamp(1.5rem,3vw,2.2rem); font-weight: 800; }
    .catalog-panel iframe { width: 100%; min-height: 650px; border: 0; border-radius: 14px; background: #f3efe9; }
    @media (max-width: 900px) {
        .promotion-hero { grid-template-columns: 1fr; }
        .promotion-art { min-height: 440px; order: -1; }
        .promotion-art::after { background: linear-gradient(0deg,#21160f 0%,transparent 35%); }
        .promotion-benefits { grid-template-columns: 1fr; }
        .custom-callout { grid-template-columns: auto 1fr; }
        .custom-callout .buy-now-btn { grid-column: 1 / -1; width: 100%; }
    }
    @media (max-width: 540px) { .promotion-page { padding: 36px 14px 10px; } .promotion-hero { border-radius: 20px; } .promotion-art { min-height: 350px; } .catalog-panel-head { align-items: flex-start; flex-direction: column; } .custom-callout { grid-template-columns: 1fr; text-align: center; } .custom-callout-icon { margin: 0 auto; } }
</style>
@endpush

@section('content')
<div class="promotion-page">
    <section class="promotion-hero">
        <div class="promotion-copy">
            <span class="section-kicker">Packages &amp; specials</span>
            <h1>Ready-made packs and platters, ready to order.</h1>
            <p>Pick a package below and add it straight to your order, no planning needed. Feeding a wedding, funeral, or office event instead? Skip ahead and request a custom package.</p>
            <div class="promotion-actions">
                <a href="#packages" class="buy-now-btn">See today's packages <i class="bi bi-arrow-down"></i></a>
                <a href="{{ route('special-requests.create') }}" class="promotion-secondary"><i class="bi bi-stars"></i> Request a custom package</a>
            </div>
        </div>
        <div class="promotion-art"><img src="{{ asset('images/kaleni/menu/braai-platter.png') }}" alt="Kaleni Catering braai platter" fetchpriority="high"></div>
    </section>

    <section class="promotion-benefits" aria-label="Ordering a package with Kaleni">
        <article class="promotion-benefit"><i class="bi bi-truck"></i><h2>Free delivery, always</h2><p>Free delivery around Windhoek on every pack and platter, no minimum order.</p></article>
        <article class="promotion-benefit"><i class="bi bi-bag-check"></i><h2>Order directly, no waiting</h2><p>Add a package straight to your order below. No quote needed for what's in stock today.</p></article>
        <article class="promotion-benefit"><i class="bi bi-egg-fried"></i><h2>Made for the moment</h2><p>Every dish is prepared to order, with thoughtful substitutions when needed.</p></article>
    </section>

    <section class="packages-section" id="packages" aria-labelledby="packages-title">
        <div class="section-heading">
            <div class="section-heading-copy">
                <span class="section-kicker">Select a package</span>
                <h2 class="section-title" id="packages-title">Today's ready-made packages</h2>
                <p class="section-subtitle">Priced and in stock now. Add one straight to your order below.</p>
            </div>
            <a href="{{ route('menu.index') }}" class="text-link">View full menu <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
        </div>

        @if($packages->count() > 0)
            <div class="products-grid">
                @foreach($packages as $item)
                    @include('menu._card', ['item' => $item])
                @endforeach
            </div>
        @else
            <div class="packages-empty">
                <p class="text-muted mb-3">No ready-made packages are in stock right now.</p>
                <a href="{{ route('menu.index') }}" class="text-link justify-content-center">Browse the full menu instead <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
            </div>
        @endif
    </section>

    <section class="custom-callout" aria-labelledby="custom-callout-title">
        <div class="custom-callout-icon"><i class="bi bi-stars" aria-hidden="true"></i></div>
        <div>
            <h2 id="custom-callout-title">Planning an event instead of an everyday order?</h2>
            <p>Tell us the occasion, guest count, and budget, and Chef K will put together a custom package and quote, no ready-made pack required.</p>
        </div>
        <a href="{{ route('special-requests.create') }}" class="buy-now-btn">Request a custom package</a>
    </section>

    @if($pdfPath)
        <section class="catalog-panel">
            <div class="catalog-panel-head">
                <div><span class="section-kicker">Printable version</span><h2>Prefer a PDF menu?</h2></div>
                <a href="{{ asset('storage/' . $pdfPath) }}" target="_blank" rel="noopener" class="btn btn-primary"><i class="bi bi-file-earmark-pdf"></i> Open PDF</a>
            </div>
            <iframe src="{{ asset('storage/' . $pdfPath) }}#toolbar=1" title="Current Kaleni Catering Services promotion catalogue"></iframe>
        </section>
    @endif
</div>
@endsection
