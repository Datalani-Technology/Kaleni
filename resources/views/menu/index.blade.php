@extends('layouts.app')

@php
    $seoPage = 'menu';
    $structuredData = [\App\Services\SeoService::generateStructuredData('website')];
    $structuredData[] = \App\Services\SeoService::generateStructuredData('breadcrumb', ['items' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => route('home')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Menu', 'item' => route('menu.index')],
    ]]);

    // Ribbon colour per category — cycles through the brand palette so every
    // category reads distinctly, same treatment "sold out" always overrides.
    $categoryTagColors = [
        'Lunch & Dinner Packs' => 'var(--secondary-dark)',
        'Individual Meals' => 'var(--primary-color)',
        'Sharing Platters' => 'var(--ink)',
        'Traditional' => '#8A4A1F',
    ];
@endphp

@section('title', 'Menu: Lunch & Dinner Packs, Platters | Kaleni Catering Services')

@push('styles')
<style>
    .search-results-bar { margin-bottom: 20px; padding: 15px; background: white; border-radius: 12px; }
    .search-clear-link { color: var(--primary-color); text-decoration: none; margin-left: 10px; }
    .search-clear-link:hover { text-decoration: underline; }
    .catalog-heading {
        position: relative; overflow: hidden;
        margin-bottom: 40px; padding: 54px 24px; text-align: center;
        background: linear-gradient(120deg, var(--primary-color), var(--primary-dark));
        box-shadow: 0 20px 50px rgba(104,11,28,.18);
    }
    .catalog-heading::after {
        content: ''; position: absolute; width: 320px; height: 320px; right: -120px; top: -140px;
        border: 46px solid rgba(248,173,39,.1); border-radius: 50%; pointer-events: none;
    }
    .catalog-heading > * { position: relative; z-index: 1; }
    .catalog-heading .section-kicker { color: var(--secondary-color); }
    .catalog-heading h1 { margin: 0; font-size: clamp(2rem, 4vw, 3.2rem); font-weight: 800; color: #fff; }
    .catalog-heading p { max-width: 580px; margin: 8px auto 0; color: rgba(255,255,255,.86); }
    .catalog-count { display: inline-block; margin-top: 18px; padding: 8px 12px; color: var(--primary-dark); background: var(--secondary-color); border-radius: 999px; font-size: .72rem; font-weight: 800; }

    /* Category tab bar — icon over label, active tab picked out in burgundy */
    .menu-tabs {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: clamp(20px, 5vw, 56px);
        padding: 22px 16px;
        margin: 28px 0 36px;
        background: #fff;
        border-top: 1px solid var(--border);
        border-bottom: 1px solid var(--border);
    }
    .menu-tab { display: flex; flex-direction: column; align-items: center; gap: 9px; min-width: 60px; color: #b3a8a2; text-decoration: none; }
    .menu-tab i { font-size: 1.65rem; }
    .menu-tab span { font-size: .74rem; font-weight: 800; letter-spacing: .03em; text-transform: uppercase; }
    .menu-tab:hover { color: var(--primary-color); }
    .menu-tab.active { color: var(--primary-color); }

    .catalog-toolbar { margin-bottom: 28px; padding: 16px; background: rgba(255,255,255,.9); border: 1px solid var(--border); border-radius: 18px; box-shadow: 0 10px 30px rgba(41,33,31,.05); }
    .catalog-filter-form { display: grid; grid-template-columns: minmax(155px,1fr) repeat(2,minmax(100px,.6fr)) minmax(155px,.85fr) auto auto; align-items: end; gap: 12px; }
    .catalog-filter-form label:not(.stock-toggle) { display: block; margin-bottom: 5px; color: var(--muted); font-size: .7rem; font-weight: 800; letter-spacing: .06em; text-transform: uppercase; }
    .catalog-filter-form .form-control, .catalog-filter-form .form-select { min-height: 43px; border-color: var(--border); border-radius: 11px; font-size: .86rem; }
    .stock-toggle { display: flex; align-items: center; gap: 8px; min-height: 43px; padding: 0 4px; font-size: .82rem; font-weight: 700; white-space: nowrap; }
    .stock-toggle input { accent-color: var(--primary-color); }
    .filter-apply { min-height: 43px; padding: 0 18px; color: #fff; background: var(--ink); border: 0; border-radius: 11px; font-size: .82rem; font-weight: 800; }
    .filter-reset { align-self: center; color: var(--primary-dark); font-size: .78rem; font-weight: 750; text-decoration: none; }
    .catalog-empty { padding: 70px 20px; text-align: center; background: #fff; border: 1px solid var(--border); border-radius: var(--radius); }
    .catalog-empty i { display: block; margin-bottom: 12px; color: #d9b9ab; font-size: 2.6rem; }

    /* Horizontal menu list cards — image left, details right */
    /* auto-fit with a 400px floor: collapses to a single column whenever a
       2-up row would squeeze the fixed-width image + text below a readable
       size, instead of relying on a hand-picked breakpoint that can drift
       out of sync with the card's actual min-content math. */
    .menu-list-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(400px, 100%), 1fr)); gap: 28px 32px; }
    .menu-list-card { display: flex; gap: 20px; padding: 14px; background: #fff; border: 1px solid var(--border); border-radius: 12px; box-shadow: 0 10px 28px rgba(41,33,31,.05); }
    .menu-list-card-media { position: relative; flex: 0 0 190px; width: 190px; height: 190px; overflow: hidden; border-radius: 8px; display: block; background: #f4ece7; }
    .menu-list-card-media img { width: 100%; height: 100%; object-fit: cover; }
    .menu-list-tag { position: absolute; left: 0; bottom: 14px; padding: 5px 12px; color: #fff; background: var(--ink); font-size: .64rem; font-weight: 800; letter-spacing: .05em; text-transform: uppercase; }
    .menu-list-card-body { flex: 1; min-width: 0; display: flex; flex-direction: column; padding-top: 2px; }
    .menu-list-card-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 10px; margin-bottom: 8px; }
    .menu-list-card-head h3 { min-width: 0; margin: 0; font-size: 1.1rem; font-weight: 800; line-height: 1.3; }
    .menu-list-card-head h3 a { color: var(--ink); text-decoration: none; }
    .menu-list-card-head h3 a:hover { color: var(--primary-color); }
    .menu-list-price { flex: 0 0 auto; color: var(--primary-color); font-size: 1.02rem; font-weight: 800; white-space: nowrap; }
    .menu-list-desc { margin: 0 0 14px; color: var(--muted); font-size: .84rem; line-height: 1.6; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .menu-list-buy { margin-top: auto; }
    .menu-list-buy button, .menu-list-buy .menu-list-buy-disabled {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 9px 18px;
        border: 0; border-radius: 999px;
        font-size: .76rem; font-weight: 800; letter-spacing: .03em; text-transform: uppercase;
        text-decoration: none; cursor: pointer;
        max-width: 100%;
        transition: background .2s ease, box-shadow .2s ease, transform .15s ease;
    }
    .menu-list-buy button {
        color: var(--primary-dark);
        background: var(--secondary-color);
    }
    .menu-list-buy button:hover {
        background: #ffbd4a;
        box-shadow: 0 8px 20px rgba(248,173,39,.3);
        transform: translateY(-1px);
    }
    .menu-list-buy .menu-list-buy-disabled { color: #a89890; background: #f2ece8; cursor: default; }

    @media (max-width: 900px) { .catalog-filter-form { grid-template-columns: repeat(2,minmax(0,1fr)); } .filter-apply { width: 100%; } }
    @media (max-width: 400px) {
        .menu-list-buy button, .menu-list-buy .menu-list-buy-disabled { width: 100%; justify-content: center; }
    }
    @media (max-width: 520px) {
        .search-results-bar { padding: 12px; margin-bottom: 16px; font-size: .9rem; }
        .search-clear-link { display: inline-block; margin-left: 0; margin-top: 6px; }
        .catalog-heading { padding: 36px 20px; }
        .catalog-filter-form { grid-template-columns: 1fr; }
        .menu-list-card { flex-direction: column; }
        .menu-list-card-media { width: 100%; height: 200px; }
    }
</style>
@endpush

@section('content')
@include('components.breadcrumbs', ['breadcrumbs' => [
    ['name' => 'Home', 'url' => route('home')],
    ['name' => 'Menu', 'url' => route('menu.index')],
]])

<div class="catalog-heading">
    <span class="section-kicker">The Kaleni menu</span>
    <h1>Find your next meal</h1>
    <p>Home-style Namibian dishes, packs and platters, made fresh in Windhoek for everyday meals and full events.</p>
    <span class="catalog-count">{{ $menuItems->total() }} {{ \Illuminate\Support\Str::plural('dish', $menuItems->total()) }}</span>
</div>

<div class="products-container">
    <nav class="menu-tabs" aria-label="Menu categories">
        <a href="{{ route('menu.index', array_filter(['search' => request('search'), 'sort' => request('sort'), 'in_stock' => request('in_stock')])) }}" class="menu-tab {{ request('category') ? '' : 'active' }}">
            <i class="bi bi-grid" aria-hidden="true"></i>
            <span>Full menu</span>
        </a>
        @foreach($categories as $category)
            <a href="{{ route('menu.index', array_filter(['search' => request('search'), 'category' => $category, 'sort' => request('sort'), 'in_stock' => request('in_stock')])) }}" class="menu-tab {{ request('category') === $category ? 'active' : '' }}">
                <i class="bi {{ \App\Http\Controllers\MenuController::CATEGORY_ICONS[$category] ?? 'bi-egg-fried' }}" aria-hidden="true"></i>
                <span>{{ $category }}</span>
            </a>
        @endforeach
    </nav>

    <div class="catalog-toolbar">
        <form action="{{ route('menu.index') }}" method="GET" class="catalog-filter-form">
            @if(request('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif
            <div>
                <label for="category">Category</label>
                <select class="form-select" name="category" id="category">
                    <option value="">All categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>{{ $category }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="min_price">Min price</label>
                <input class="form-control" type="number" min="0" step="10" name="min_price" id="min_price" value="{{ request('min_price') }}" placeholder="N$ 0">
            </div>
            <div>
                <label for="max_price">Max price</label>
                <input class="form-control" type="number" min="0" step="10" name="max_price" id="max_price" value="{{ request('max_price') }}" placeholder="Any">
            </div>
            <div>
                <label for="sort">Sort by</label>
                <select class="form-select" name="sort" id="sort">
                    <option value="featured" {{ request('sort', 'featured') === 'featured' ? 'selected' : '' }}>Featured first</option>
                    <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Newest</option>
                    <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Price: low to high</option>
                    <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Price: high to low</option>
                    <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Name A to Z</option>
                </select>
            </div>
            <label class="stock-toggle mb-0" for="in_stock">
                <input type="checkbox" name="in_stock" id="in_stock" value="1" {{ request()->boolean('in_stock') ? 'checked' : '' }}>
                Available now
            </label>
            <button type="submit" class="filter-apply">Apply filters</button>
            @if(request()->hasAny(['search','category','min_price','max_price','sort','in_stock']))
                <a href="{{ route('menu.index') }}" class="filter-reset">Reset all</a>
            @endif
        </form>
    </div>

    @if(request('search'))
        <div class="search-results-bar">
            <p class="mb-0"><i class="bi bi-search me-1" aria-hidden="true"></i> Search results for <strong>&ldquo;{{ request('search') }}&rdquo;</strong>
                <a href="{{ route('menu.index') }}" class="search-clear-link">Clear search</a>
            </p>
        </div>
    @endif

    @if($menuItems->count())
        <div class="menu-list-grid">
            @foreach($menuItems as $item)
                <article class="menu-list-card">
                    <a href="{{ route('menu.show', $item->id) }}" class="menu-list-card-media">
                        <x-menu-item-image :menu-item="$item" alt="{{ $item->name }} - Kaleni Catering Services menu" />
                        @if($item->stock <= 0)
                            <span class="menu-list-tag" style="background:#6b6b6b;">Sold out</span>
                        @elseif($item->category)
                            <span class="menu-list-tag" style="background: {{ $categoryTagColors[$item->category] ?? 'var(--ink)' }};">{{ $item->category }}</span>
                        @endif
                    </a>
                    <div class="menu-list-card-body">
                        <div class="menu-list-card-head">
                            <h3><a href="{{ route('menu.show', $item->id) }}">{{ $item->name }}</a></h3>
                            <span class="menu-list-price">N$ {{ number_format($item->price, 2) }}</span>
                        </div>
                        <p class="menu-list-desc">{{ $item->description ?: 'A home-style dish, made fresh by Kaleni Catering Services.' }}</p>
                        <div class="menu-list-buy">
                            @if($item->stock > 0)
                                <form action="{{ route('cart.add') }}" method="POST" data-add-to-cart>
                                    @csrf
                                    <input type="hidden" name="menu_item_id" value="{{ $item->id }}">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit">Add to order <i class="bi bi-chevron-right" aria-hidden="true"></i></button>
                                </form>
                            @else
                                <span class="menu-list-buy-disabled">Out of stock</span>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        @if($menuItems->hasPages())
            <div style="margin-top: 40px; text-align: center;">{{ $menuItems->links() }}</div>
        @endif
    @else
        <div class="catalog-empty">
            <i class="bi bi-egg-fried" aria-hidden="true"></i>
            <p class="mb-2 fw-bold">No dishes found</p>
            <p class="text-muted mb-3">Try another filter, or browse the full menu.</p>
            <a href="{{ route('menu.index') }}" class="text-link justify-content-center">View full menu <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
        </div>
    @endif
</div>
@endsection
