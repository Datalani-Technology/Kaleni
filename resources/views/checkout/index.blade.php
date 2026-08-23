@extends('layouts.app')

@section('title', 'Checkout - /Namsa Florals')

@push('styles')
<style>
    .checkout-inner { max-width: 1180px; margin: 0 auto; padding: 0 24px; }
    .checkout-header { display: flex; align-items: end; justify-content: space-between; gap: 24px; margin-bottom: 30px; }
    .checkout-header h1 { margin: 4px 0 0; font-size: clamp(2.1rem,4vw,3.2rem); font-weight: 800; }
    .checkout-progress { display: flex; align-items: center; gap: 8px; color: var(--muted); font-size: .78rem; font-weight: 700; }
    .checkout-progress strong { color: var(--primary-dark); }
    .checkout-card { overflow: hidden; background: rgba(255,255,255,.94); border: 1px solid var(--border); border-radius: 22px; box-shadow: 0 14px 42px rgba(52,27,40,.07); }
    .checkout-card-section { padding: 25px; border-bottom: 1px solid var(--border); }
    .checkout-card-section:last-child { border-bottom: 0; }
    .checkout-section-head { display: flex; gap: 13px; margin-bottom: 20px; }
    .checkout-section-number { display: grid; flex: 0 0 34px; width: 34px; height: 34px; place-items: center; color: #fff; background: var(--ink); border-radius: 50%; font-size: .75rem; font-weight: 800; }
    .checkout-section-head h2 { margin: 0 0 3px; font-size: 1.06rem; font-weight: 800; }
    .checkout-section-head p { margin: 0; color: var(--muted); font-size: .8rem; }
    .checkout-page .form-label { margin-bottom: 6px; font-size: .76rem; font-weight: 750; }
    .checkout-page .form-control, .checkout-page .form-select { min-height: 46px; border-color: var(--border); border-radius: 11px; font-size: .9rem; }
    .checkout-page textarea.form-control { min-height: auto; }
    .optional-label { color: var(--muted); font-size: .68rem; font-weight: 600; }
    .payment-choice { display: flex; gap: 13px; padding: 15px; background: #f5fbf7; border: 1px solid #cde9d6; border-radius: 14px; }
    .payment-choice input { margin-top: 4px; accent-color: #16864b; }
    .payment-choice strong { display: block; margin-bottom: 2px; }
    .payment-choice span { color: var(--muted); font-size: .79rem; }
    .checkout-submit { min-height: 52px; font-size: .9rem; }
    .checkout-summary { position: sticky; top: 122px; padding: 24px; }
    .checkout-summary h2 { margin-bottom: 20px; font-size: 1.2rem; font-weight: 800; }
    .checkout-summary-item { display: grid; grid-template-columns: 58px 1fr auto; align-items: center; gap: 12px; padding: 12px 0; border-bottom: 1px solid var(--border); }
    .checkout-summary-item img { width: 58px; height: 58px; object-fit: cover; border-radius: 10px; }
    .checkout-summary-item h3 { margin: 0 0 2px; font-size: .84rem; font-weight: 800; }
    .checkout-summary-item p { margin: 0; color: var(--muted); font-size: .75rem; }
    .checkout-summary-price { font-size: .82rem; font-weight: 800; white-space: nowrap; }
    .checkout-total { display: flex; align-items: end; justify-content: space-between; gap: 16px; padding-top: 20px; }
    .checkout-total span { color: var(--muted); font-size: .8rem; }
    .checkout-total strong { display: block; color: var(--primary-dark); font-size: 1.45rem; }
    .checkout-assurance { display: grid; gap: 9px; margin-top: 22px; padding-top: 18px; border-top: 1px solid var(--border); color: var(--muted); font-size: .75rem; }
    .checkout-assurance span { display: flex; align-items: center; gap: 8px; }
    .checkout-assurance i { color: var(--primary-color); }
    @media (max-width: 991px) { .checkout-summary { position: static; margin-top: 20px; } }
    @media (max-width: 600px) {
        .checkout-inner { padding: 0 14px; }
        .checkout-header { align-items: flex-start; flex-direction: column; }
        .checkout-card-section, .checkout-summary { padding: 19px; }
        .checkout-progress { flex-wrap: wrap; }
    }
</style>
@endpush

@section('content')
<div class="checkout-inner my-5 checkout-page">
    <div class="checkout-header">
        <div>
            <span class="section-kicker">Almost there</span>
            <h1>Delivery details</h1>
        </div>
        <div class="checkout-progress" aria-label="Checkout progress">
            <span>Cart</span><i class="bi bi-chevron-right"></i><strong>Details</strong><i class="bi bi-chevron-right"></i><span>WhatsApp confirmation</span>
        </div>
    </div>

    <form action="{{ route('checkout.process') }}" method="POST" id="checkoutForm">
        @csrf
        <div class="row g-4 align-items-start">
            <div class="col-lg-7">
                <div class="checkout-card">
                    <section class="checkout-card-section">
                        <div class="checkout-section-head">
                            <span class="checkout-section-number">01</span>
                            <div><h2>Your details</h2><p>We use these details for order updates and the receipt.</p></div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="customer_name" class="form-label">Full name *</label>
                                <input type="text" class="form-control @error('customer_name') is-invalid @enderror" id="customer_name" name="customer_name" value="{{ old('customer_name') }}" autocomplete="name" required>
                                @error('customer_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="customer_phone" class="form-label">Phone number *</label>
                                <input type="tel" class="form-control @error('customer_phone') is-invalid @enderror" id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}" autocomplete="tel" required>
                                @error('customer_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label for="customer_email" class="form-label">Email address *</label>
                                <input type="email" class="form-control @error('customer_email') is-invalid @enderror" id="customer_email" name="customer_email" value="{{ old('customer_email') }}" autocomplete="email" required>
                                @error('customer_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </section>

                    <section class="checkout-card-section">
                        <div class="checkout-section-head">
                            <span class="checkout-section-number">02</span>
                            <div><h2>Recipient &amp; delivery</h2><p>Leave recipient fields blank when the flowers are for you.</p></div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="recipient_name" class="form-label">Recipient name <span class="optional-label">Optional</span></label>
                                <input type="text" class="form-control @error('recipient_name') is-invalid @enderror" id="recipient_name" name="recipient_name" value="{{ old('recipient_name') }}">
                                @error('recipient_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="recipient_phone" class="form-label">Recipient phone <span class="optional-label">Optional</span></label>
                                <input type="tel" class="form-control @error('recipient_phone') is-invalid @enderror" id="recipient_phone" name="recipient_phone" value="{{ old('recipient_phone') }}">
                                @error('recipient_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label for="delivery_address" class="form-label">Delivery address *</label>
                                <textarea class="form-control @error('delivery_address') is-invalid @enderror" id="delivery_address" name="delivery_address" rows="3" autocomplete="street-address" placeholder="Street, suburb, building or landmark" required>{{ old('delivery_address') }}</textarea>
                                @error('delivery_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="delivery_date" class="form-label">Delivery date *</label>
                                <input type="date" class="form-control @error('delivery_date') is-invalid @enderror" id="delivery_date" name="delivery_date" min="{{ now()->toDateString() }}" max="{{ now()->addDays(30)->toDateString() }}" value="{{ old('delivery_date', now()->toDateString()) }}" required>
                                @error('delivery_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="delivery_window" class="form-label">Preferred window *</label>
                                <select class="form-select @error('delivery_window') is-invalid @enderror" id="delivery_window" name="delivery_window" required>
                                    <option value="morning" {{ old('delivery_window') === 'morning' ? 'selected' : '' }}>Morning · 09:00–12:00</option>
                                    <option value="afternoon" {{ old('delivery_window') === 'afternoon' ? 'selected' : '' }}>Afternoon · 12:00–17:00</option>
                                    <option value="anytime" {{ old('delivery_window', 'anytime') === 'anytime' ? 'selected' : '' }}>Anytime · best available</option>
                                </select>
                                @error('delivery_window')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label for="delivery_instructions" class="form-label">Delivery notes <span class="optional-label">Optional</span></label>
                                <textarea class="form-control @error('delivery_instructions') is-invalid @enderror" id="delivery_instructions" name="delivery_instructions" rows="2" placeholder="Gate code, reception desk, landmark, or call-on-arrival note">{{ old('delivery_instructions') }}</textarea>
                                @error('delivery_instructions')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </section>

                    <section class="checkout-card-section">
                        <div class="checkout-section-head">
                            <span class="checkout-section-number">03</span>
                            <div><h2>Personal message</h2><p>We will include it with the flowers exactly as written.</p></div>
                        </div>
                        <label for="gift_message" class="form-label">Card message <span class="optional-label">Optional · 500 characters</span></label>
                        <textarea class="form-control @error('gift_message') is-invalid @enderror" id="gift_message" name="gift_message" rows="3" maxlength="500" placeholder="With love, congratulations, thinking of you…">{{ old('gift_message') }}</textarea>
                        @error('gift_message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </section>

                    @php $dpoReady = (bool) config('services.dpo.company_token'); @endphp
                    <section class="checkout-card-section">
                        <div class="checkout-section-head">
                            <span class="checkout-section-number">04</span>
                            <div><h2>Confirm with our florist</h2><p>Final delivery availability and payment are confirmed personally.</p></div>
                        </div>
                        @if($dpoReady)
                            <label class="payment-choice mb-2" for="payment_dpo">
                                <input type="radio" name="payment_method" id="payment_dpo" value="dpo" {{ old('payment_method') === 'dpo' ? 'checked' : '' }} required>
                                <span><strong><i class="bi bi-credit-card text-primary me-1"></i> Pay online now</strong><span>Secure card payment via DPO — your order is confirmed instantly.</span></span>
                            </label>
                        @endif
                        <label class="payment-choice" for="payment_whatsapp">
                            <input type="radio" name="payment_method" id="payment_whatsapp" value="whatsapp" {{ !$dpoReady || old('payment_method', 'whatsapp') === 'whatsapp' ? 'checked' : '' }} required>
                            <span><strong><i class="bi bi-whatsapp text-success me-1"></i> WhatsApp confirmation</strong><span>After placing the order, WhatsApp opens with your complete order details ready to send.</span></span>
                        </label>
                        @error('payment_method')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
                        <button type="submit" class="buy-now-btn checkout-submit w-100 mt-3" id="checkoutSubmit"><i class="bi bi-bag-check me-1" aria-hidden="true"></i> <span id="checkoutSubmitLabel">Place order &amp; continue to WhatsApp</span></button>
                    </section>
                </div>
            </div>

            <div class="col-lg-5">
                <aside class="checkout-card checkout-summary" aria-labelledby="orderSummaryTitle">
                    <h2 id="orderSummaryTitle">Your order</h2>
                    @foreach($cartItems as $item)
                        <div class="checkout-summary-item">
                            @if($item->product->image)
                                <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}">
                            @else
                                <span class="product-image-placeholder" aria-hidden="true"><i class="bi bi-flower1"></i></span>
                            @endif
                            <div><h3>{{ $item->product->name }}</h3><p>Quantity {{ $item->quantity }}</p></div>
                            <span class="checkout-summary-price">N$ {{ number_format($item->product->price * $item->quantity, 2) }}</span>
                        </div>
                    @endforeach
                    <div class="checkout-promo mt-2">
                        <label for="promoCodeInput" class="form-label small mb-1">Promo code</label>
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control" id="promoCodeInput" placeholder="Enter code" value="{{ $appliedPromo->code ?? '' }}" {{ $appliedPromo ? 'readonly' : '' }} style="text-transform: uppercase;">
                            @if($appliedPromo)
                                <button type="button" class="btn btn-outline-danger" id="promoRemoveBtn">Remove</button>
                            @else
                                <button type="button" class="btn btn-outline-secondary" id="promoApplyBtn">Apply</button>
                            @endif
                        </div>
                        <div id="promoMessage" class="small mt-1 {{ $appliedPromo ? 'text-success' : '' }}">
                            @if($appliedPromo)
                                "{{ $appliedPromo->code }}" applied — you saved N$ {{ number_format($discount, 2) }}.
                            @endif
                        </div>
                    </div>
                    @if($discount > 0)
                        <div class="checkout-total" style="padding-top: 12px;">
                            <div><span>Subtotal</span><strong style="font-size: 1rem; color: var(--ink);">N$ {{ number_format($subtotal, 2) }}</strong></div>
                        </div>
                        <div class="checkout-total" style="padding-top: 4px;">
                            <div><span>Discount ({{ $appliedPromo->code }})</span><strong style="font-size: 1rem; color: #16864b;">&minus;N$ {{ number_format($discount, 2) }}</strong></div>
                        </div>
                    @endif
                    <div class="checkout-total" style="{{ $discount > 0 ? 'padding-top: 4px;' : '' }}">
                        <div><span>Order total</span><strong>N$ {{ number_format($total, 2) }}</strong></div>
                        <span>Delivery confirmed separately</span>
                    </div>
                    <div class="checkout-assurance">
                        <span><i class="bi bi-check-circle-fill"></i> Freshly arranged for your order</span>
                        <span><i class="bi bi-check-circle-fill"></i> Same-day Windhoek delivery subject to availability</span>
                        <span><i class="bi bi-shield-check"></i> Your details are used only to fulfil this order</span>
                    </div>
                </aside>
            </div>
        </div>
    </form>
</div>

<script>
document.getElementById('checkoutForm')?.addEventListener('submit', function () {
    var button = document.getElementById('checkoutSubmit');
    if (!button) return;
    button.disabled = true;
    button.innerHTML = '<span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>Preparing your order…';
});

document.querySelectorAll('input[name="payment_method"]').forEach(function (radio) {
    radio.addEventListener('change', function () {
        var label = document.getElementById('checkoutSubmitLabel');
        if (label) {
            label.textContent = this.value === 'dpo' ? 'Place order & pay now' : 'Place order & continue to WhatsApp';
        }
    });
});

document.getElementById('promoApplyBtn')?.addEventListener('click', function () {
    var btn = this;
    var input = document.getElementById('promoCodeInput');
    var msg = document.getElementById('promoMessage');
    var code = (input.value || '').trim();
    if (!code) {
        msg.textContent = 'Enter a code first.';
        msg.className = 'small mt-1 text-danger';
        return;
    }
    btn.disabled = true;
    fetch('{{ route('cart.apply-promo') }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: JSON.stringify({ code: code }),
    })
        .then(function (r) { return r.json().then(function (data) { return { ok: r.ok, data: data }; }); })
        .then(function (res) {
            if (res.ok && res.data.success) {
                location.reload();
                return;
            }
            msg.textContent = (res.data && res.data.message) || 'Could not apply that code.';
            msg.className = 'small mt-1 text-danger';
            btn.disabled = false;
        })
        .catch(function () {
            msg.textContent = 'Something went wrong. Please try again.';
            msg.className = 'small mt-1 text-danger';
            btn.disabled = false;
        });
});

document.getElementById('promoRemoveBtn')?.addEventListener('click', function () {
    this.disabled = true;
    fetch('{{ route('cart.remove-promo') }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
    }).then(function () { location.reload(); });
});
</script>
@endsection
