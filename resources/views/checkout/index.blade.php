@extends('layouts.app')

@section('title', 'Checkout - /Namsa Florals')

@push('styles')
<style>
    .checkout-inner { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
    .checkout-form-card { max-width: 420px; }
    .checkout-form-card .card-header { padding: 0.5rem 1rem; font-size: 1rem; }
    .checkout-form-card .card-body { padding: 1rem; }
    .checkout-form-card .form-label { margin-bottom: 0.25rem; font-size: 0.9rem; }
    .checkout-form-card .form-control, .checkout-form-card .form-select { font-size: 0.9rem; }
    .checkout-form-card .mb-field { margin-bottom: 0.75rem; }
    .checkout-form-card .mb-payment { margin-bottom: 1rem; }
    .checkout-form-card .btn-submit { padding: 10px 20px; font-size: 1rem; }
    .checkout-summary-card .card-header { padding: 0.5rem 1rem; font-size: 1rem; }
    .checkout-summary-card .card-body { padding: 1rem; }
    @media (max-width: 768px) {
        .checkout-form-card { max-width: none; }
        .checkout-summary-card { margin-top: 1rem; }
    }
    @media (max-width: 400px) {
        .checkout-inner { padding: 0 12px; }
        .checkout-page h1 { font-size: 1.5rem; }
    }
</style>
@endpush

@section('content')
<div class="checkout-inner my-5 checkout-page">
    <h1 class="mb-4">Checkout</h1>

    @if($cartItems->count() == 0)
        <div class="alert alert-warning mb-4">
            <strong>No products in cart.</strong> Please add products to your cart first.
        </div>
        <a href="{{ route('products.index') }}" class="buy-now-btn d-inline-block" style="background: #333; color: white; text-decoration: none; padding: 10px 24px;">
            Browse Products
        </a>
    @else
    <div class="row">
        <div class="col-md-6 col-lg-5">
            <div class="card mb-4 checkout-form-card">
                <div class="card-header py-2">
                    <h5 class="mb-0">Customer Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('checkout.process') }}" method="POST" id="checkoutForm">
                        @csrf
                        <div class="row g-2">
                            <div class="col-md-6 mb-field">
                                <label for="customer_name" class="form-label">Full Name *</label>
                                <input type="text" class="form-control form-control-sm" id="customer_name" name="customer_name" value="{{ old('customer_name') }}" required>
                                @error('customer_name')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-field">
                                <label for="customer_email" class="form-label">Email *</label>
                                <input type="email" class="form-control form-control-sm" id="customer_email" name="customer_email" value="{{ old('customer_email') }}" required>
                                @error('customer_email')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="mb-field">
                            <label for="customer_phone" class="form-label">Phone Number *</label>
                            <input type="text" class="form-control form-control-sm" id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}" required>
                            @error('customer_phone')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-field">
                            <label for="delivery_address" class="form-label">Delivery Address *</label>
                            <textarea class="form-control form-control-sm" id="delivery_address" name="delivery_address" rows="2" required>{{ old('delivery_address') }}</textarea>
                            @error('delivery_address')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-payment">
                            <label class="form-label">Payment Method *</label>
                            <div class="form-check mb-1">
                                <input class="form-check-input" type="radio" name="payment_method" id="payment_dpo" value="dpo" disabled>
                                <label class="form-check-label text-muted" for="payment_dpo">
                                    Pay with DPO (Credit/Debit Card) <span class="text-muted" style="font-size: 0.85em;">— Coming soon</span>
                                </label>
                            </div>
                            <div class="form-check mb-1">
                                <input class="form-check-input" type="radio" name="payment_method" id="payment_whatsapp" value="whatsapp" {{ old('payment_method', 'whatsapp') === 'whatsapp' ? 'checked' : '' }} required>
                                <label class="form-check-label" for="payment_whatsapp">Pay via WhatsApp</label>
                            </div>
                            <p class="text-muted small mt-1 mb-0">
                                <i class="bi bi-whatsapp text-success"></i> You’ll be taken to WhatsApp to confirm and arrange payment.
                            </p>
                        </div>

                        <button type="submit" class="buy-now-btn w-100 btn-submit" style="background: #333; color: white;">
                            Complete Order
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="card checkout-summary-card">
                <div class="card-header py-2">
                    <h5 class="mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    @foreach($cartItems as $item)
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ $item->product->name }} x{{ $item->quantity }}</span>
                            <span>N$ {{ number_format($item->product->price * $item->quantity, 2) }}</span>
                        </div>
                    @endforeach
                    <hr class="my-2">
                    <div class="d-flex justify-content-between">
                        <strong>Total:</strong>
                        <strong style="font-size: 1.15rem; color: #333;">N$ {{ number_format($total, 2) }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
