@extends('layouts.app')

@section('title', 'Order Food - Kaleni Catering Services')

@push('styles')
<style>
    .checkout-inner { max-width: 1180px; margin: 0 auto; padding: 0 24px; }
    .checkout-header { display: flex; align-items: end; justify-content: space-between; gap: 24px; margin-bottom: 30px; }
    .checkout-header h1 { margin: 4px 0 0; font-size: clamp(2.1rem,4vw,3.2rem); font-weight: 800; }
    .checkout-progress { display: flex; align-items: center; gap: 8px; color: var(--muted); font-size: .78rem; font-weight: 700; }
    .checkout-progress strong { color: var(--primary-dark); }
    .checkout-card { overflow: hidden; background: rgba(255,255,255,.94); border: 1px solid var(--border); border-radius: 22px; box-shadow: 0 14px 42px rgba(41,33,31,.07); }
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
    .fulfillment-group { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }
    .fulfillment-option { position: relative; }
    .fulfillment-option input { position: absolute; opacity: 0; inset: 0; margin: 0; cursor: pointer; }
    .fulfillment-option span {
        display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 4px;
        min-height: 64px; padding: 10px 8px; text-align: center;
        color: var(--ink); background: #fff; border: 1.5px solid var(--border); border-radius: 12px;
        font-size: .78rem; font-weight: 750;
    }
    .fulfillment-option span i { font-size: 1.15rem; color: var(--muted); }
    .fulfillment-option input:checked + span { color: var(--primary-dark); background: var(--primary-soft); border-color: var(--primary-color); }
    .fulfillment-option input:checked + span i { color: var(--primary-color); }
    .fulfillment-option input:focus-visible + span { outline: 2px solid var(--primary-color); outline-offset: 2px; }

    /* Order review/confirmation modal */
    .booking-confirm-card { border: 0; border-radius: 22px; }
    .booking-confirm-list { display: grid; gap: 12px; margin: 0 0 18px; padding: 16px; background: var(--bg); border-radius: 14px; }
    .booking-confirm-list div { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; }
    .booking-confirm-list dt { flex: 0 0 auto; color: var(--muted); font-size: .76rem; font-weight: 700; text-transform: uppercase; letter-spacing: .03em; }
    .booking-confirm-list dd { margin: 0; color: var(--ink); font-size: .88rem; font-weight: 700; text-align: right; }

    .empty-booking-card { position: relative; overflow: hidden; padding: clamp(48px, 8vw, 86px) 24px; text-align: center; background: linear-gradient(145deg, #fff 0%, #fdf3ee 100%); border: 1px solid var(--border); border-radius: 26px; box-shadow: 0 18px 55px rgba(41,33,31,.08); }
    .empty-booking-card::after { content: ''; position: absolute; width: 240px; height: 240px; right: -95px; top: -105px; border: 36px solid rgba(104,11,28,.055); border-radius: 50%; }
    .empty-booking-icon { display: grid; width: 74px; height: 74px; margin: 0 auto 20px; place-items: center; color: var(--primary-dark); background: #fff; border: 1px solid var(--border); border-radius: 50%; box-shadow: 0 14px 32px rgba(41,33,31,.1); font-size: 1.8rem; position: relative; z-index: 1; }
    .empty-booking-card h2 { position: relative; z-index: 1; margin-bottom: 10px; font-size: clamp(1.65rem, 3vw, 2.3rem); font-weight: 800; }
    .empty-booking-card p { position: relative; z-index: 1; max-width: 500px; margin: 0 auto 24px; color: var(--muted); }

    @media (max-width: 991px) { .checkout-summary { position: static; margin-top: 20px; } }
    @media (max-width: 600px) {
        .checkout-inner { padding: 0 14px; }
        .checkout-header { align-items: flex-start; flex-direction: column; }
        .checkout-card-section, .checkout-summary { padding: 19px; }
        .checkout-progress { flex-wrap: wrap; }
        .fulfillment-group { grid-template-columns: 1fr; }
        .booking-confirm-list div { flex-direction: column; gap: 2px; }
        .booking-confirm-list dd { text-align: left; }
    }
</style>
@endpush

@section('content')
@if($cartItems->isEmpty())
<div class="checkout-inner my-5 checkout-page">
    <div class="checkout-header">
        <div>
            <span class="section-kicker">Almost there</span>
            <h1>Order details</h1>
        </div>
    </div>
    <div class="empty-booking-card">
        <span class="empty-booking-icon"><i class="bi bi-basket" aria-hidden="true"></i></span>
        <h2>Let's start with your menu.</h2>
        <p>An order is built around the dishes you choose — browse the menu and add what you'd like, then come back here to arrange pickup or delivery.</p>
        <a href="{{ route('menu.index') }}" class="buy-now-btn" style="max-width: 260px; margin: 0 auto; display: block; text-decoration: none;">Browse the Menu</a>
    </div>
</div>
@else
<div class="checkout-inner my-5 checkout-page">
    <div class="checkout-header">
        <div>
            <span class="section-kicker">Almost there</span>
            <h1>Order details</h1>
        </div>
        <div class="checkout-progress" aria-label="Order progress">
            <span>Order</span><i class="bi bi-chevron-right"></i><strong>Pickup / delivery</strong><i class="bi bi-chevron-right"></i><span>WhatsApp confirmation</span>
        </div>
    </div>

    <form action="{{ route('order.store') }}" method="POST" id="quickOrderForm">
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
                            <div><h2>Pickup or delivery?</h2><p>No event date or guest count needed — just tell us how to get your food to you.</p></div>
                        </div>
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="fulfillment-group" role="radiogroup" aria-label="Fulfillment method">
                                    <label class="fulfillment-option">
                                        <input type="radio" name="fulfillment_method" value="pickup" {{ old('fulfillment_method', 'pickup') === 'pickup' ? 'checked' : '' }} required>
                                        <span><i class="bi bi-bag-check" aria-hidden="true"></i> I'll pick it up</span>
                                    </label>
                                    <label class="fulfillment-option">
                                        <input type="radio" name="fulfillment_method" value="delivery" {{ old('fulfillment_method') === 'delivery' ? 'checked' : '' }} required>
                                        <span><i class="bi bi-truck" aria-hidden="true"></i> Deliver to me</span>
                                    </label>
                                </div>
                                @error('fulfillment_method')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12" id="deliveryAddressWrap" hidden>
                                <label for="event_address" class="form-label">Delivery address *</label>
                                <textarea class="form-control @error('event_address') is-invalid @enderror" id="event_address" name="event_address" rows="3" autocomplete="street-address" placeholder="Street, suburb, or landmark">{{ old('event_address') }}</textarea>
                                @error('event_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label for="event_notes" class="form-label">Order notes <span class="optional-label">Optional</span></label>
                                <textarea class="form-control @error('event_notes') is-invalid @enderror" id="event_notes" name="event_notes" rows="2" placeholder="Dietary notes, preferred pickup/delivery time, etc.">{{ old('event_notes') }}</textarea>
                                @error('event_notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </section>

                    @php $dpoReady = (bool) config('services.dpo.company_token'); @endphp
                    <section class="checkout-card-section">
                        <div class="checkout-section-head">
                            <span class="checkout-section-number">03</span>
                            <div><h2>Confirm with Kaleni Catering</h2><p>Final availability and payment are confirmed personally.</p></div>
                        </div>
                        @if($dpoReady)
                            <label class="payment-choice mb-2" for="payment_dpo">
                                <input type="radio" name="payment_method" id="payment_dpo" value="dpo" {{ old('payment_method') === 'dpo' ? 'checked' : '' }} required>
                                <span><strong><i class="bi bi-credit-card text-primary me-1"></i> Pay online now</strong><span>Secure card payment via DPO. Your order is confirmed instantly.</span></span>
                            </label>
                        @endif
                        <label class="payment-choice" for="payment_whatsapp">
                            <input type="radio" name="payment_method" id="payment_whatsapp" value="whatsapp" {{ !$dpoReady || old('payment_method', 'whatsapp') === 'whatsapp' ? 'checked' : '' }} required>
                            <span><strong><i class="bi bi-whatsapp text-success me-1"></i> WhatsApp confirmation</strong><span>After ordering, WhatsApp opens with your complete order details ready to send.</span></span>
                        </label>
                        @error('payment_method')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
                        <button type="submit" class="buy-now-btn checkout-submit w-100 mt-3" id="checkoutSubmit"><i class="bi bi-bag-check me-1" aria-hidden="true"></i> Review &amp; confirm order</button>
                    </section>
                </div>
            </div>

            <div class="col-lg-5">
                <aside class="checkout-card checkout-summary" aria-labelledby="orderSummaryTitle">
                    <h2 id="orderSummaryTitle">Your order</h2>
                    @foreach($cartItems as $item)
                        <div class="checkout-summary-item">
                            @if($item->menuItem->image)
                                <img src="{{ $item->menuItem->image_url }}" alt="{{ $item->menuItem->name }}">
                            @else
                                <span class="product-image-placeholder" aria-hidden="true"><i class="bi bi-egg-fried"></i></span>
                            @endif
                            <div><h3>{{ $item->menuItem->name }}</h3><p>Quantity {{ $item->quantity }}</p></div>
                            <span class="checkout-summary-price">N$ {{ number_format($item->menuItem->price * $item->quantity, 2) }}</span>
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
                                "{{ $appliedPromo->code }}" applied, you saved N$ {{ number_format($discount, 2) }}.
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
                        <span>Confirmed separately</span>
                    </div>
                    <div class="checkout-assurance">
                        <span><i class="bi bi-check-circle-fill"></i> Cooked fresh for your order</span>
                        <span><i class="bi bi-check-circle-fill"></i> Free Windhoek delivery subject to availability</span>
                        <span><i class="bi bi-shield-check"></i> Your details are used only to fulfil this order</span>
                    </div>
                </aside>
            </div>
        </div>
    </form>
</div>
@endif

<div class="modal fade" id="orderConfirmModal" tabindex="-1" aria-labelledby="orderConfirmTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content booking-confirm-card">
            <div class="modal-body p-4 p-md-5">
                <span class="section-kicker">Please review</span>
                <h2 class="h4 mb-3" id="orderConfirmTitle">Confirm your order</h2>
                <dl class="booking-confirm-list">
                    <div><dt>Fulfillment</dt><dd id="confirmFulfillment"></dd></div>
                    <div><dt>Address</dt><dd id="confirmAddress"></dd></div>
                    <div><dt>Total</dt><dd id="confirmTotal"></dd></div>
                </dl>
                <p class="text-muted small mb-4">Availability and final payment are still confirmed personally by our team after you submit.</p>
                <div class="d-flex flex-column-reverse flex-sm-row justify-content-sm-end gap-2">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Edit details</button>
                    <button type="button" class="buy-now-btn" style="width:auto; padding: 0 22px;" id="orderConfirmSubmit">Yes, confirm &amp; order</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var form = document.getElementById('quickOrderForm');
    if (!form) return;

    var deliveryAddressWrap = document.getElementById('deliveryAddressWrap');
    var deliveryAddressInput = document.getElementById('event_address');

    function applyFulfillmentMethod() {
        var checked = form.querySelector('input[name="fulfillment_method"]:checked');
        var method = checked ? checked.value : 'pickup';

        deliveryAddressWrap.hidden = method !== 'delivery';
        deliveryAddressInput.required = method === 'delivery';
        if (method !== 'delivery') deliveryAddressInput.value = '';
    }

    form.querySelectorAll('input[name="fulfillment_method"]').forEach(function (radio) {
        radio.addEventListener('change', applyFulfillmentMethod);
    });
    applyFulfillmentMethod();

    // --- Review-before-submit, same pattern as the event booking checkout. ---
    var confirmModalEl = document.getElementById('orderConfirmModal');
    var confirmed = false;

    function getConfirmModal() {
        return window.bootstrap ? bootstrap.Modal.getOrCreateInstance(confirmModalEl) : null;
    }

    form.addEventListener('submit', function (event) {
        if (confirmed) return;

        event.preventDefault();
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        var method = (form.querySelector('input[name="fulfillment_method"]:checked') || {}).value || 'pickup';
        document.getElementById('confirmFulfillment').textContent = method === 'delivery' ? 'Delivery' : 'Pickup';
        document.getElementById('confirmAddress').textContent = method === 'delivery' ? (deliveryAddressInput.value || 'Not specified') : 'Pickup at Kaleni Catering Services';
        document.getElementById('confirmTotal').textContent = 'N$ {{ number_format($total ?? 0, 2) }}';

        var payDpo = document.getElementById('payment_dpo');
        var confirmBtn = document.getElementById('orderConfirmSubmit');
        confirmBtn.textContent = (payDpo && payDpo.checked) ? 'Yes, confirm & pay now' : 'Yes, confirm & order';

        var modal = getConfirmModal();
        if (modal) {
            modal.show();
        } else {
            confirmed = true;
            form.requestSubmit();
        }
    });

    document.getElementById('orderConfirmSubmit').addEventListener('click', function () {
        var btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>Ordering…';
        var modal = getConfirmModal();
        if (modal) modal.hide();

        // WhatsApp is a hand-off, not the destination — open a blank tab
        // now, synchronously within this click, so the browser's popup
        // blocker doesn't reject it once the async fetch below resolves
        // (same pattern used for the admin "send quote via WhatsApp"
        // button). The main tab stays on the site and lands on its own
        // confirmation page with a pop-up success message instead of being
        // navigated away with nothing to show for it.
        var whatsappChecked = document.getElementById('payment_whatsapp');
        var whatsappTab = (whatsappChecked && whatsappChecked.checked) ? window.open('', '_blank') : null;

        // Submitted via fetch so the redirect to WhatsApp/DPO happens as a
        // plain script-initiated navigation afterwards, not as the tail end
        // of this form's own submission — the site's CSP restricts a form's
        // own submission chain to same-origin destinations, and payment
        // hand-off necessarily leaves the site. Any failure here (validation,
        // stock, etc.) falls back to a normal submission so the existing
        // server-rendered error handling still applies unchanged.
        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: { 'Accept': 'application/json' },
        })
            .then(function (r) {
                if (!r.ok) throw new Error('non-ok response');
                return r.json();
            })
            .then(function (data) {
                if (!data.redirect) throw new Error('missing redirect');
                if (whatsappTab && data.whatsapp_redirect) {
                    whatsappTab.location.href = data.whatsapp_redirect;
                } else if (whatsappTab) {
                    whatsappTab.close();
                }
                window.location.href = data.redirect;
            })
            .catch(function () {
                if (whatsappTab) whatsappTab.close();
                confirmed = true;
                form.requestSubmit();
            });
    });
})();
</script>

<script>
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
