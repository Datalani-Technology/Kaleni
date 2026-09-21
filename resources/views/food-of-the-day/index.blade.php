@extends('layouts.app')

@php $seoPage = 'food-of-the-day'; @endphp

@section('title', 'Food of the Day - Kaleni Catering Services')

@push('styles')
<style>
    .fotd-page { max-width: 1120px; margin: 0 auto; padding: 48px 24px 90px; }

    .fotd-heading { display: flex; align-items: flex-end; justify-content: space-between; gap: 24px; margin-bottom: 40px; }
    .fotd-heading h1 { margin: 6px 0 10px; font-size: clamp(2rem, 4vw, 3rem); font-weight: 800; }
    .fotd-heading p { max-width: 520px; margin: 0; color: var(--muted); }
    .fotd-today-chip { flex: 0 0 auto; padding: 8px 14px; color: var(--primary-dark); background: var(--primary-soft); border-radius: 999px; font-size: .74rem; font-weight: 800; white-space: nowrap; }

    /* ---------- Spotlight: today's special ---------- */
    .fotd-today {
        position: relative;
        overflow: hidden;
        display: grid;
        grid-template-columns: minmax(0,280px) 1fr;
        gap: 34px;
        align-items: center;
        padding: 34px;
        margin-bottom: 56px;
        background: linear-gradient(120deg, var(--primary-color), var(--primary-dark));
        border-radius: 26px;
        color: #fff;
        box-shadow: 0 28px 64px rgba(104,11,28,.24);
    }
    .fotd-today::after {
        content: '';
        position: absolute;
        width: 280px; height: 280px;
        right: -110px; top: -120px;
        border: 42px solid rgba(248,173,39,.1);
        border-radius: 50%;
        pointer-events: none;
    }
    .fotd-today > * { position: relative; z-index: 1; }
    .fotd-today-media { position: relative; }
    .fotd-today-img { width: 100%; aspect-ratio: 1/1; object-fit: cover; border-radius: 18px; box-shadow: 0 16px 34px rgba(0,0,0,.3); }
    .fotd-today-badge { display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; margin-bottom: 12px; color: #4A0814; background: var(--secondary-color); border-radius: 999px; font-size: .68rem; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; }
    .fotd-today-category { display: inline-flex; margin: 0 0 12px 8px; padding: 5px 11px; color: #fff; background: rgba(255,255,255,.14); border: 1px solid rgba(255,255,255,.22); border-radius: 999px; font-size: .66rem; font-weight: 750; letter-spacing: .05em; text-transform: uppercase; }
    .fotd-today h2 { margin: 0 0 10px; color: #fff; font-size: clamp(1.6rem, 3vw, 2.1rem); line-height: 1.15; }
    .fotd-today p { margin: 0 0 18px; color: rgba(255,255,255,.86); line-height: 1.75; max-width: 52ch; }
    .fotd-today-meta { display: flex; flex-wrap: wrap; align-items: center; gap: 18px; margin-bottom: 20px; }
    .fotd-today-price { font-size: 1.5rem; font-weight: 800; }
    .fotd-today-serves { display: flex; align-items: center; gap: 7px; color: rgba(255,255,255,.75); font-size: .84rem; font-weight: 650; }
    .fotd-today-btn { display: inline-flex; align-items: center; gap: 8px; padding: 13px 24px; color: var(--primary-dark) !important; background: #fff; border-radius: 999px; font-weight: 800; text-decoration: none; transition: transform .15s ease, box-shadow .2s ease; }
    .fotd-today-btn:hover { transform: translateY(-1px); box-shadow: 0 10px 26px rgba(0,0,0,.25); }

    .fotd-empty-spotlight {
        display: flex; align-items: center; gap: 26px;
        padding: 34px; margin-bottom: 56px;
        background: #fff; border: 1px dashed var(--border); border-radius: 26px;
    }
    .fotd-empty-spotlight-icon { display: grid; flex: 0 0 72px; width: 72px; height: 72px; place-items: center; color: var(--primary-color); background: var(--primary-soft); border-radius: 50%; font-size: 1.7rem; }
    .fotd-empty-spotlight h2 { margin: 0 0 6px; font-size: 1.25rem; font-weight: 800; }
    .fotd-empty-spotlight p { margin: 0 0 14px; color: var(--muted); }

    /* ---------- Week strip ---------- */
    .fotd-week-head { display: flex; align-items: baseline; justify-content: space-between; gap: 16px; margin-bottom: 22px; }
    .fotd-week-head h3 { margin: 0; font-size: 1.3rem; font-weight: 800; }
    .fotd-week-head span { color: var(--muted); font-size: .8rem; }
    .fotd-week-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(190px, 1fr)); gap: 20px; }

    .fotd-day-card { display: flex; flex-direction: column; overflow: hidden; background: #fff; border: 1px solid var(--border); border-radius: 18px; box-shadow: 0 10px 26px rgba(41,33,31,.05); transition: transform .2s ease, box-shadow .2s ease; }
    .fotd-day-card:hover { transform: translateY(-3px); box-shadow: 0 16px 34px rgba(41,33,31,.1); }
    .fotd-day-card.is-today { border-color: var(--secondary-color); box-shadow: 0 0 0 2px var(--secondary-color) inset, 0 10px 26px rgba(41,33,31,.05); }
    .fotd-day-head { display: flex; align-items: center; justify-content: space-between; gap: 8px; padding: 12px 14px; }
    .fotd-day-date { color: var(--ink); font-size: .78rem; font-weight: 800; text-transform: uppercase; letter-spacing: .04em; }
    .fotd-day-today-tag { padding: 2px 9px; color: #4A0814; background: var(--secondary-color); border-radius: 999px; font-size: .62rem; font-weight: 800; letter-spacing: .04em; text-transform: uppercase; }
    .fotd-day-media { position: relative; }
    .fotd-day-img { width: 100%; aspect-ratio: 4/3; object-fit: cover; display: block; }
    .fotd-day-category { position: absolute; left: 10px; bottom: 10px; padding: 4px 9px; color: #fff; background: rgba(41,33,31,.72); border-radius: 999px; font-size: .6rem; font-weight: 750; letter-spacing: .04em; text-transform: uppercase; }
    .fotd-day-body { display: flex; flex-direction: column; flex: 1; padding: 14px; }
    .fotd-day-name { margin: 0 0 6px; font-size: .92rem; font-weight: 800; line-height: 1.35; }
    .fotd-day-price { margin-top: auto; padding-top: 8px; color: var(--primary-dark); font-weight: 800; font-size: .88rem; }
    .fotd-day-link { display: inline-flex; align-items: center; gap: 4px; margin-top: 8px; color: var(--primary-color); font-size: .78rem; font-weight: 750; text-decoration: none; }
    .fotd-day-link:hover { text-decoration: underline; }

    .fotd-day-card.is-unplanned { align-items: center; justify-content: center; padding: 22px 14px; text-align: center; background: var(--bg); border-style: dashed; }
    .fotd-day-card.is-unplanned .fotd-day-head { padding-bottom: 0; }
    .fotd-day-unplanned-icon { display: grid; width: 42px; height: 42px; margin: 10px auto 10px; place-items: center; color: #c9beb8; background: #fff; border: 1px solid var(--border); border-radius: 50%; font-size: 1.1rem; }
    .fotd-day-unplanned-text { margin: 0 0 10px; color: var(--muted); font-size: .8rem; }
    .fotd-day-unplanned-link { color: var(--primary-color); font-size: .78rem; font-weight: 750; text-decoration: none; }
    .fotd-day-unplanned-link:hover { text-decoration: underline; }

    /* ---------- Closing CTA ---------- */
    .fotd-cta {
        display: flex; align-items: center; justify-content: space-between; gap: 24px;
        margin-top: 56px; padding: 30px 34px;
        background: #21160f; border-radius: 24px; color: #fff;
    }
    .fotd-cta h3 { margin: 0 0 4px; color: #fff; font-size: 1.15rem; font-weight: 800; }
    .fotd-cta p { margin: 0; color: rgba(255,255,255,.7); font-size: .86rem; }
    .fotd-cta .buy-now-btn { width: auto; padding: 0 22px; min-height: 48px; text-decoration: none; }

    @media (max-width: 640px) {
        .fotd-heading { align-items: flex-start; flex-direction: column; }
        .fotd-today { grid-template-columns: 1fr; text-align: center; padding: 26px; }
        .fotd-today-img { max-width: 220px; margin: 0 auto; }
        .fotd-today-meta { justify-content: center; }
        .fotd-today-category { margin-left: 0; }
        .fotd-empty-spotlight { flex-direction: column; text-align: center; }
        .fotd-cta { flex-direction: column; text-align: center; }
    }
</style>
@endpush

@section('content')
<div class="fotd-page">
    <div class="fotd-heading">
        <div>
            <span class="section-kicker">From Chef K's kitchen</span>
            <h1>Food of the Day</h1>
            <p>A different home-style special every day. See what's cooking today, plus the full week ahead so you can plan around it.</p>
        </div>
        <span class="fotd-today-chip">{{ \Illuminate\Support\Carbon::today()->format('l, j F') }}</span>
    </div>

    @if($todayEntry)
        <div class="fotd-today">
            <div class="fotd-today-media">
                <img src="{{ $todayEntry->effective_image_url ?? asset('images/kaleni/brand/kaleni-logo.jpg') }}" alt="{{ $todayEntry->effective_title }}" class="fotd-today-img">
            </div>
            <div>
                <span class="fotd-today-badge"><i class="bi bi-stars" aria-hidden="true"></i> Today's special</span>
                @if($todayEntry->menuItem?->category)
                    <span class="fotd-today-category">{{ $todayEntry->menuItem->category }}</span>
                @endif
                <h2>{{ $todayEntry->effective_title }}</h2>
                @if($todayEntry->effective_description)
                    <p>{{ $todayEntry->effective_description }}</p>
                @endif
                <div class="fotd-today-meta">
                    @if($todayEntry->effective_price !== null)
                        <span class="fotd-today-price">N$ {{ number_format((float) $todayEntry->effective_price, 2) }}</span>
                    @endif
                    @if($todayEntry->menuItem?->serves_count)
                        <span class="fotd-today-serves"><i class="bi bi-people" aria-hidden="true"></i> Serves {{ $todayEntry->menuItem->serves_count }}</span>
                    @endif
                </div>
                @if($todayEntry->menu_item_id)
                    <a href="{{ route('menu.show', $todayEntry->menu_item_id) }}" class="fotd-today-btn"><i class="bi bi-bag-plus" aria-hidden="true"></i> Order today's special</a>
                @else
                    <a href="{{ route('special-requests.create') }}" class="fotd-today-btn"><i class="bi bi-chat-dots" aria-hidden="true"></i> Ask about today's special</a>
                @endif
            </div>
        </div>
    @else
        <div class="fotd-empty-spotlight">
            <span class="fotd-empty-spotlight-icon"><i class="bi bi-egg-fried" aria-hidden="true"></i></span>
            <div>
                <h2>No special scheduled for today</h2>
                <p>Check back soon for today's surprise, or browse the full menu. There's always something good cooking.</p>
                <a href="{{ route('menu.index') }}" class="text-link">View full menu <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
            </div>
        </div>
    @endif

    <div class="fotd-week-head">
        <h3>This week's line-up</h3>
        <span>{{ $week->first()->date->format('j M') }} to {{ $week->last()->date->format('j M') }}</span>
    </div>
    <div class="fotd-week-grid">
        @foreach($week as $day)
            @php $entry = $day->entry; @endphp
            @if($entry)
                <article class="fotd-day-card {{ $day->date->isToday() ? 'is-today' : '' }}">
                    <div class="fotd-day-head">
                        <span class="fotd-day-date">{{ $day->date->format('D, j M') }}</span>
                        @if($day->date->isToday())<span class="fotd-day-today-tag">Today</span>@endif
                    </div>
                    <div class="fotd-day-media">
                        <img src="{{ $entry->effective_image_url ?? asset('images/kaleni/brand/kaleni-logo.jpg') }}" alt="{{ $entry->effective_title }}" class="fotd-day-img">
                        @if($entry->menuItem?->category)
                            <span class="fotd-day-category">{{ $entry->menuItem->category }}</span>
                        @endif
                    </div>
                    <div class="fotd-day-body">
                        <h4 class="fotd-day-name">{{ $entry->effective_title }}</h4>
                        @if($entry->effective_price !== null)
                            <span class="fotd-day-price">N$ {{ number_format((float) $entry->effective_price, 2) }}</span>
                        @endif
                        @if($entry->menu_item_id)
                            <a href="{{ route('menu.show', $entry->menu_item_id) }}" class="fotd-day-link">View dish <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                        @endif
                    </div>
                </article>
            @else
                <article class="fotd-day-card is-unplanned">
                    <div class="fotd-day-head">
                        <span class="fotd-day-date">{{ $day->date->format('D, j M') }}</span>
                        @if($day->date->isToday())<span class="fotd-day-today-tag">Today</span>@endif
                    </div>
                    <span class="fotd-day-unplanned-icon"><i class="bi bi-question-lg" aria-hidden="true"></i></span>
                    <p class="fotd-day-unplanned-text">Not planned yet</p>
                    <a href="{{ route('special-requests.create') }}" class="fotd-day-unplanned-link">Ask Chef K &rarr;</a>
                </article>
            @endif
        @endforeach
    </div>

    <div class="fotd-cta">
        <div>
            <h3>Don't see what you're after?</h3>
            <p>Tell us what you have in mind and Chef K will follow up with a quote.</p>
        </div>
        <a href="{{ route('special-requests.create') }}" class="buy-now-btn"><i class="bi bi-chat-dots me-1" aria-hidden="true"></i> Send a special request</a>
    </div>
</div>
@endsection
