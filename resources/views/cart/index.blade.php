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
    @media (max-width: 768px) {
        .cart-table-wrap { display: none; }
        .cart-mobile-list { display: block; }
        .cart-summary-card { margin-top: 1rem; }
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
    <h1 class="mb-4">Shopping Cart</h1>
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" style="max-width: 1200px; margin: 0 auto 20px;">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" style="max-width: 1200px; margin: 0 auto 20px;">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

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
                                                        <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 5px; margin-right: 15px;">
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
                                            <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}" class="cart-mobile-img">
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
        <div class="text-center" style="padding: 40px 20px;">
            <i class="bi bi-cart-x" style="font-size: 3rem; color: #ccc;"></i>
            <p style="font-size: 1.1rem; color: #666; margin-top: 15px; margin-bottom: 20px;">Your cart is empty</p>
            <a href="{{ route('products.index') }}" class="buy-now-btn" style="background: #333; color: white; max-width: 200px; margin: 0 auto; display: block; text-decoration: none;">
                Browse Products
            </a>
        </div>
    @endif
</div>
@endsection
