@extends('layouts.app')

@php
    $seoPage = 'products';
    $structuredData = [\App\Services\SeoService::generateStructuredData('website')];
    $structuredData[] = \App\Services\SeoService::generateStructuredData('breadcrumb', ['items' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => route('home')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Products', 'item' => route('products.index')],
    ]]);
@endphp

@section('title', 'Buy Fresh Flowers Online in Namibia | /Namsa Florals Flower Shop')

@push('styles')
<style>
    .search-results-bar { margin-bottom: 20px; padding: 15px; background: white; border-radius: 12px; }
    .search-clear-link { color: var(--primary-color); text-decoration: none; margin-left: 10px; }
    .search-clear-link:hover { text-decoration: underline; }
    .catalog-heading { margin-bottom: 30px; display: flex; align-items: flex-end; justify-content: space-between; gap: 24px; }
    .catalog-heading h1 { margin: 0; font-size: clamp(2rem, 4vw, 3.2rem); font-weight: 800; }
    .catalog-heading p { max-width: 580px; margin: 8px 0 0; color: var(--muted); }
    .catalog-count { flex: 0 0 auto; padding: 8px 12px; color: var(--primary-dark); background: var(--primary-soft); border-radius: 999px; font-size: .72rem; font-weight: 800; }
    .catalog-categories { display: flex; gap: 9px; margin: -8px 0 18px; padding-bottom: 4px; overflow-x: auto; scrollbar-width: none; }
    .catalog-categories::-webkit-scrollbar { display: none; }
    .category-chip { flex: 0 0 auto; padding: 8px 13px; color: var(--ink); background: #fff; border: 1px solid var(--border); border-radius: 999px; font-size: .78rem; font-weight: 750; text-decoration: none; transition: .2s ease; }
    .category-chip:hover, .category-chip.active { color: #fff; background: var(--ink); border-color: var(--ink); }
    .catalog-toolbar { margin-bottom: 28px; padding: 16px; background: rgba(255,255,255,.9); border: 1px solid var(--border); border-radius: 18px; box-shadow: 0 10px 30px rgba(52,27,40,.05); }
    .catalog-filter-form { display: grid; grid-template-columns: minmax(155px,1fr) repeat(2,minmax(100px,.6fr)) minmax(155px,.85fr) auto auto; align-items: end; gap: 12px; }
    .catalog-filter-form label:not(.stock-toggle) { display: block; margin-bottom: 5px; color: var(--muted); font-size: .7rem; font-weight: 800; letter-spacing: .06em; text-transform: uppercase; }
    .catalog-filter-form .form-control, .catalog-filter-form .form-select { min-height: 43px; border-color: var(--border); border-radius: 11px; font-size: .86rem; }
    .stock-toggle { display: flex; align-items: center; gap: 8px; min-height: 43px; padding: 0 4px; font-size: .82rem; font-weight: 700; white-space: nowrap; }
    .stock-toggle input { accent-color: var(--primary-color); }
    .filter-apply { min-height: 43px; padding: 0 18px; color: #fff; background: var(--ink); border: 0; border-radius: 11px; font-size: .82rem; font-weight: 800; }
    .filter-reset { align-self: center; color: var(--primary-dark); font-size: .78rem; font-weight: 750; text-decoration: none; }
    .catalog-empty { grid-column: 1 / -1; padding: 70px 20px; text-align: center; background: #fff; border: 1px solid var(--border); border-radius: var(--radius); }
    .catalog-empty i { display: block; margin-bottom: 12px; color: #d4b6c3; font-size: 2.6rem; }
    @media (max-width: 900px) { .catalog-filter-form { grid-template-columns: repeat(2,minmax(0,1fr)); } .filter-apply { width: 100%; } }
    @media (max-width: 520px) {
        .search-results-bar { padding: 12px; margin-bottom: 16px; font-size: .9rem; }
        .search-clear-link { display: inline-block; margin-left: 0; margin-top: 6px; }
        .catalog-heading { align-items: flex-start; flex-direction: column; gap: 12px; }
        .catalog-filter-form, .products-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
@include('components.breadcrumbs', ['breadcrumbs' => [
    ['name' => 'Home', 'url' => route('home')],
    ['name' => 'Products', 'url' => route('products.index')],
]])

<div class="products-container">
    <div class="catalog-heading">
        <div>
            <span class="section-kicker">The flower collection</span>
            <h1>Find the perfect arrangement</h1>
            <p>Fresh flowers, thoughtfully arranged in Windhoek for celebrations, affection, comfort, and everyday joy.</p>
        </div>
        <span class="catalog-count">{{ $products->total() }} {{ \Illuminate\Support\Str::plural('arrangement', $products->total()) }}</span>
    </div>

    <nav class="catalog-categories" aria-label="Flower categories">
        <a href="{{ route('products.index', array_filter(['search' => request('search'), 'sort' => request('sort'), 'in_stock' => request('in_stock')])) }}" class="category-chip {{ request('category') ? '' : 'active' }}">All flowers</a>
        @foreach($categories as $category)
            <a href="{{ route('products.index', array_filter(['search' => request('search'), 'category' => $category, 'sort' => request('sort'), 'in_stock' => request('in_stock')])) }}" class="category-chip {{ request('category') === $category ? 'active' : '' }}">{{ $category }}</a>
        @endforeach
    </nav>

    <div class="catalog-toolbar">
        <form action="{{ route('products.index') }}" method="GET" class="catalog-filter-form">
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
                    <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Name A–Z</option>
                </select>
            </div>
            <label class="stock-toggle mb-0" for="in_stock">
                <input type="checkbox" name="in_stock" id="in_stock" value="1" {{ request()->boolean('in_stock') ? 'checked' : '' }}>
                In stock only
            </label>
            <button type="submit" class="filter-apply">Apply filters</button>
            @if(request()->hasAny(['search','category','min_price','max_price','sort','in_stock']))
                <a href="{{ route('products.index') }}" class="filter-reset">Reset all</a>
            @endif
        </form>
    </div>

    @if(request('search'))
        <div class="search-results-bar">
            <p class="mb-0"><i class="bi bi-search me-1" aria-hidden="true"></i> Search results for <strong>&ldquo;{{ request('search') }}&rdquo;</strong>
                <a href="{{ route('products.index') }}" class="search-clear-link">Clear search</a>
            </p>
        </div>
    @endif

    <div class="products-grid">
        @forelse($products as $product)
            <article class="product-card">
                <a href="{{ route('products.show', $product->id) }}" class="product-media-link">
                    @if($product->category)<span class="product-card-badge">{{ $product->category }}</span>@endif
                    <x-product-image :product="$product" alt="{{ $product->name }} - Buy Fresh Flowers Online Namibia" />
                </a>
                <div class="product-info">
                    <h2 class="product-name"><a href="{{ route('products.show', $product->id) }}" class="product-name-link">{{ $product->name }}</a></h2>
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
        @empty
            <div class="catalog-empty">
                <i class="bi bi-flower1" aria-hidden="true"></i>
                <p class="mb-2 fw-bold">No arrangements found</p>
                <p class="text-muted mb-3">Try another filter or browse the full collection.</p>
                <a href="{{ route('products.index') }}" class="text-link justify-content-center">View all flowers <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
            </div>
        @endforelse
    </div>

    @if($products->hasPages())
        <div style="margin-top: 40px; text-align: center;">{{ $products->links() }}</div>
    @endif
</div>
@endsection
