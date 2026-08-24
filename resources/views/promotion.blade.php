@extends('layouts.app')

@php($seoPage = 'promotion')

@section('title', 'Seasonal Flowers & Florist Perks - /Namsa Florals')

@push('styles')
<style>
    .promotion-page { max-width: 1240px; margin: 0 auto; padding: 58px 24px 20px; }
    .promotion-hero { display: grid; grid-template-columns: minmax(0,1fr) minmax(340px,.78fr); overflow: hidden; background: #21171d; border-radius: 30px; box-shadow: 0 24px 70px rgba(42,20,32,.16); }
    .promotion-copy { align-self: center; padding: clamp(36px,6vw,74px); color: #fff; }
    .promotion-copy .section-kicker { color: #ff8dbd; }
    .promotion-copy h1 { max-width: 680px; margin: 12px 0 18px; color: #fff; font-size: clamp(2.5rem,5.5vw,5.2rem); font-weight: 800; line-height: .98; letter-spacing: -.055em; }
    .promotion-copy p { max-width: 600px; margin: 0 0 26px; color: rgba(255,255,255,.7); line-height: 1.75; }
    .promotion-actions { display: flex; flex-wrap: wrap; gap: 10px; }
    .promotion-actions .buy-now-btn { width: auto; min-width: 180px; text-decoration: none; }
    .promotion-secondary { display: inline-flex; min-height: 48px; align-items: center; justify-content: center; gap: 8px; padding: 0 18px; color: #fff; border: 1px solid rgba(255,255,255,.24); border-radius: 11px; font-size: .82rem; font-weight: 800; text-decoration: none; }
    .promotion-art { position: relative; min-height: 610px; overflow: hidden; }
    .promotion-art::after { content: ''; position: absolute; inset: 0; background: linear-gradient(90deg,#21171d 0%,transparent 28%), linear-gradient(0deg,rgba(33,23,29,.3),transparent 45%); }
    .promotion-art img { width: 100%; height: 100%; object-fit: cover; }
    .promotion-benefits { display: grid; grid-template-columns: repeat(3,minmax(0,1fr)); gap: 16px; margin-top: 24px; }
    .promotion-benefit { padding: 24px; background: #fff; border: 1px solid var(--border); border-radius: 18px; }
    .promotion-benefit i { display: grid; width: 42px; height: 42px; margin-bottom: 18px; place-items: center; color: var(--primary-dark); background: var(--primary-soft); border-radius: 12px; }
    .promotion-benefit h2 { margin: 0 0 7px; font-size: 1rem; font-weight: 800; }
    .promotion-benefit p { margin: 0; color: var(--muted); font-size: .82rem; line-height: 1.65; }
    .catalog-panel { margin-top: 34px; padding: clamp(22px,4vw,42px); background: #fff; border: 1px solid var(--border); border-radius: 24px; box-shadow: 0 14px 38px rgba(52,27,40,.06); }
    .catalog-panel-head { display: flex; align-items: end; justify-content: space-between; gap: 20px; margin-bottom: 20px; }
    .catalog-panel h2 { margin: 5px 0 0; font-size: clamp(1.5rem,3vw,2.2rem); font-weight: 800; }
    .catalog-panel iframe { width: 100%; min-height: 650px; border: 0; border-radius: 14px; background: #f3eff1; }
    @media (max-width: 900px) {
        .promotion-hero { grid-template-columns: 1fr; }
        .promotion-art { min-height: 440px; order: -1; }
        .promotion-art::after { background: linear-gradient(0deg,#21171d 0%,transparent 35%); }
        .promotion-benefits { grid-template-columns: 1fr; }
    }
    @media (max-width: 540px) { .promotion-page { padding: 36px 14px 10px; } .promotion-hero { border-radius: 20px; } .promotion-art { min-height: 350px; } .catalog-panel-head { align-items: flex-start; flex-direction: column; } }
</style>
@endpush

@section('content')
<div class="promotion-page">
    <section class="promotion-hero">
        <div class="promotion-copy">
            <span class="section-kicker">The seasonal edit</span>
            <h1>Beautiful gestures, thoughtfully timed.</h1>
            <p>Discover florist-selected flowers for the season, same-day Windhoek delivery when available, and personal help choosing the right arrangement.</p>
            <div class="promotion-actions">
                <a href="{{ route('products.index', ['sort' => 'featured', 'in_stock' => 1]) }}" class="buy-now-btn">Shop the current edit <i class="bi bi-arrow-right"></i></a>
                <a href="{{ config('social.whatsapp') }}" target="_blank" rel="noopener" class="promotion-secondary"><i class="bi bi-whatsapp"></i> Ask our florist</a>
            </div>
        </div>
        <div class="promotion-art"><img src="{{ asset('images/catalog/namsa-mixed-arrangement-v2.webp') }}" alt="Seasonal mixed flower arrangement" fetchpriority="high"></div>
    </section>

    <section class="promotion-benefits" aria-label="Current florist perks">
        <article class="promotion-benefit"><i class="bi bi-truck"></i><h2>Same-day, when available</h2><p>Order early for the best chance of same-day delivery within Windhoek.</p></article>
        <article class="promotion-benefit"><i class="bi bi-chat-dots"></i><h2>A personal note, included</h2><p>Add a card message at checkout and we will prepare it with your flowers.</p></article>
        <article class="promotion-benefit"><i class="bi bi-flower1"></i><h2>Made for the moment</h2><p>Every arrangement is prepared to order, with thoughtful seasonal substitutions when needed.</p></article>
    </section>

    @if($pdfPath)
        <section class="catalog-panel">
            <div class="catalog-panel-head">
                <div><span class="section-kicker">Current catalogue</span><h2>Browse the latest selection</h2></div>
                <a href="{{ asset('storage/' . $pdfPath) }}" target="_blank" rel="noopener" class="btn btn-primary"><i class="bi bi-file-earmark-pdf"></i> Open PDF</a>
            </div>
            <iframe src="{{ asset('storage/' . $pdfPath) }}#toolbar=1" title="Current /Namsa Florals promotion catalogue"></iframe>
        </section>
    @endif
</div>
@endsection
