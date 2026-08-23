@extends('layouts.app')

@section('title', 'Shopping Cart - /Namsa Florals')

@push('styles')
<style>
    .cart-inner {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }
    .cart-table-wrap { display: block; }
    .cart-mobile-list { display: none; }
    .cart-mobile-item {
        padding: 1rem 0;
        border-bottom: 1px solid #eee;
    }
    .cart-mobile-item:last-of-type { border-bottom: none; }
    .cart-mobile-img { width: 56px; height: 56px; object-fit: cover; border-radius: 6px; flex-shrink: 0; }
    .cart-mobile-qty { width: 64px; padding: 6px 8px; font-size: 0.9rem; }
    .cart-page-header { display: flex; align-items: end; justify-content: space-between; gap: 20px; margin-bottom: 28px; }
    .cart-page-header h1 { margin: 4px 0 0; font-size: clamp(2rem, 4vw, 3.1rem); font-weight: 800; }
    .cart-page-header p { max-width: 390px; margin: 0; color: var(--muted); text-align: right; }
    .empty-cart-card { position: relative; overflow: hidden; padding: clamp(48px, 8vw, 86px) 24px; text-align: center; background: linear-gradient(145deg, #fff 0%, #fdf1f6 100%); border: 1px solid var(--border); border-radius: 26px; box-shadow: 0 18px 55px rgba(52,27,40,.08); }
    .empty-cart-card::after { content: ''; position: absolute; width: 240px; height: 240px; right: -95px; top: -105px; border: 36px solid rgba(180,35,99,.055); border-radius: 50%; }
    .empty-cart-icon { display: grid; width: 74px; height: 74px; margin: 0 auto 20px; place-items: center; color: var(--primary-dark); background: #fff; border: 1px solid var(--border); border-radius: 50%; box-shadow: 0 14px 32px rgba(52,27,40,.1); font-size: 1.8rem; }
    .empty-cart-card h2 { margin-bottom: 10px; font-size: clamp(1.65rem, 3vw, 2.3rem); font-weight: 800; }
    .empty-cart-card p { max-width: 500px; margin: 0 auto 24px; color: var(--muted); }
    .cart-recommendations { margin-top: 64px; }
    .cart-recommendations-head { display: flex; align-items: end; justify-content: space-between; gap: 16px; margin-bottom: 22px; }
    .cart-recommendations h2 { margin: 4px 0 0; font-size: clamp(1.65rem, 3vw, 2.35rem); font-weight: 800; }
    @media (max-width: 768px) {
        .cart-table-wrap { display: none; }
        .cart-mobile-list { display: block; }
        .cart-summary-card { margin-top: 1rem; }
        .cart-page-header, .cart-recommendations-head { align-items: flex-start; flex-direction: column; }
        .cart-page-header p { text-align: left; }
    }
    @media (max-width: 400px) {
        .cart-inner { padding: 0 12px; }
        .cart-page h1 { font-size: 1.5rem; }
        .cart-mobile-img { width: 48px; height: 48px; }
        .cart-mobile-qty { width: 56px; font-size: 0.85rem; }
    }
</style>
@endpush

@section('content')
<div class="cart-inner my-5 cart-page">
    <div class="cart-page-header">
        <div>
            <span class="section-kicker">Your selection</span>
            <h1>Shopping cart</h1>
        </div>
        <p>Review your flowers, then choose the recipient and preferred delivery time at checkout.</p>
    </div>

    @if($cartItems->count() > 0)
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <div class="cart-table-wrap">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Subtotal</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cartItems as $item)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($item->product->image)
                                                        <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 5px; margin-right: 15px;">
                                                    @endif
                                                    <div>
                                                        <strong>{{ $item->product->name }}</strong>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>N$ {{ number_format($item->product->price, 2) }}</td>
                                            <td>
                                                <form action="{{ route('cart.update', $item->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $item->product->stock }}" style="width: 70px;" onchange="this.form.submit()">
                                                </form>
                                            </td>
                                            <td>N$ {{ number_format($item->product->price * $item->quantity, 2) }}</td>
                                            <td>
                                                <form action="{{ route('cart.remove', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove this item from your cart?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Remove from cart">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="cart-mobile-list">
                            @foreach($cartItems as $item)
                                <div class="cart-mobile-item">
                                    <div class="d-flex align-items-start gap-2">
                                        @if($item->product->image)
                                            <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}" class="cart-mobile-img">
                                        @endif
                                        <div class="flex-grow-1 min-w-0">
                                            <strong class="d-block">{{ $item->product->name }}</strong>
                                            <span class="text-muted">N$ {{ number_format($item->product->price, 2) }} each</span>
                                            <div class="d-flex align-items-center justify-content-between mt-2 flex-wrap gap-2">
                                                <form action="{{ route('cart.update', $item->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $item->product->stock }}" class="cart-mobile-qty" onchange="this.form.submit()">
                                                </form>
                                                <span class="fw-bold">N$ {{ number_format($item->product->price * $item->quantity, 2) }}</span>
                                                <form action="{{ route('cart.remove', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove this item from your cart?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" aria-label="Remove" title="Remove from cart"><i class="bi bi-trash"></i></button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <form action="{{ route('cart.clear') }}" method="POST" class="mt-3" onsubmit="return confirm('Clear your entire cart? All items will be removed. This cannot be undone.');">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger">Clear Cart</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card cart-summary-card">
                    <div class="card-body">
                        <h5 class="card-title">Order Summary</h5>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Subtotal:</span>
                            <strong>N$ {{ number_format($total, 2) }}</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <span><strong>Total:</strong></span>
                            <strong style="font-size: 1.3rem; color: #333;">N$ {{ number_format($total, 2) }}</strong>
                        </div>
                        <a href="{{ route('checkout.index') }}" class="buy-now-btn w-100" style="background: #333; color: white; text-align: center; text-decoration: none; display: block;">
                            Proceed to Checkout
                        </a>
                        <a href="{{ route('products.index') }}" class="buy-now-btn w-100 mt-2" style="background: #666; color: white; text-align: center; text-decoration: none; display: block;">
                            Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="empty-cart-card">
            <span class="empty-cart-icon"><i class="bi bi-bag-heart" aria-hidden="true"></i></span>
            <h2>Something beautiful belongs here.</h2>
            <p>Begin with one of our florist-curated arrangements. Every order can include a recipient, delivery window, and personal message.</p>
            <a href="{{ route('products.index') }}" class="buy-now-btn" style="max-width: 220px; margin: 0 auto; display: block; text-decoration: none;">
                Explore the collection
            </a>
        </div>
    @endif

    @if($suggestedProducts->isNotEmpty())
        <section class="cart-recommendations" aria-labelledby="cartRecommendationsTitle">
            <div class="cart-recommendations-head">
                <div>
                    <span class="section-kicker">Florist favourites</span>
                    <h2 id="cartRecommendationsTitle">A beautiful place to start</h2>
                </div>
                <a href="{{ route('products.index') }}" class="text-link">View all flowers <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
            </div>
            <div class="products-grid">
                @foreach($suggestedProducts as $product)
                    <article class="product-card">
                        <a href="{{ route('products.show', $product) }}" class="product-media-link">
                            @if($product->category)<span class="product-card-badge">{{ $product->category }}</span>@endif
                            <x-product-image :product="$product" />
                        </a>
                        <div class="product-info">
                            <h3 class="product-name"><a href="{{ route('products.show', $product) }}" class="product-name-link">{{ $product->name }}</a></h3>
                            <div class="product-price">N$ {{ number_format($product->price, 2) }}</div>
                            <form action="{{ route('cart.add') }}" method="POST" data-add-to-cart>
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="buy-now-btn"><i class="bi bi-bag-plus" aria-hidden="true"></i> Add to cart</button>
                            </form>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection
