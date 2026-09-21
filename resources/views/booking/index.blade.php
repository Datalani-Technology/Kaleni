@extends('layouts.app')

@section('title', 'Book Your Catering - Kaleni Catering Services')

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
    .duration-mode-group { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 10px; }
    .duration-mode-option { position: relative; }
    .duration-mode-option input { position: absolute; opacity: 0; inset: 0; margin: 0; cursor: pointer; }
    .duration-mode-option span {
        display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 4px;
        min-height: 64px; padding: 10px 8px; text-align: center;
        color: var(--ink); background: #fff; border: 1.5px solid var(--border); border-radius: 12px;
        font-size: .78rem; font-weight: 750;
    }
    .duration-mode-option span i { font-size: 1.15rem; color: var(--muted); }
    .duration-mode-option input:checked + span { color: var(--primary-dark); background: var(--primary-soft); border-color: var(--primary-color); }
    .duration-mode-option input:checked + span i { color: var(--primary-color); }
    .duration-mode-option input:focus-visible + span { outline: 2px solid var(--primary-color); outline-offset: 2px; }

    /* Booking review/confirmation modal */
    .booking-confirm-card { border: 0; border-radius: 22px; }
    .booking-confirm-list { display: grid; gap: 12px; margin: 0 0 18px; padding: 16px; background: var(--bg); border-radius: 14px; }
    .booking-confirm-list div { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; }
    .booking-confirm-list dt { flex: 0 0 auto; color: var(--muted); font-size: .76rem; font-weight: 700; text-transform: uppercase; letter-spacing: .03em; }
    .booking-confirm-list dd { margin: 0; color: var(--ink); font-size: .88rem; font-weight: 700; text-align: right; }

    @media (max-width: 991px) { .checkout-summary { position: static; margin-top: 20px; } }
    @media (max-width: 600px) {
        .checkout-inner { padding: 0 14px; }
        .checkout-header { align-items: flex-start; flex-direction: column; }
        .checkout-card-section, .checkout-summary { padding: 19px; }
        .checkout-progress { flex-wrap: wrap; }
        .duration-mode-group { grid-template-columns: 1fr; }
        .booking-confirm-list div { flex-direction: column; gap: 2px; }
        .booking-confirm-list dd { text-align: left; }
    }
</style>
@endpush

@section('content')
<div class="checkout-inner my-5 checkout-page">
    <div class="checkout-header">
        <div>
            <span class="section-kicker">Almost there</span>
            <h1>Booking details</h1>
        </div>
        <div class="checkout-progress" aria-label="Booking progress">
            <span>Order</span><i class="bi bi-chevron-right"></i><strong>Event details</strong><i class="bi bi-chevron-right"></i><span>WhatsApp confirmation</span>
        </div>
    </div>

    <form action="{{ route('booking.store') }}" method="POST" id="checkoutForm">
        @csrf
        <div class="row g-4 align-items-start">
            <div class="col-lg-7">
                <div class="checkout-card">
                    <section class="checkout-card-section">
                        <div class="checkout-section-head">
                            <span class="checkout-section-number">01</span>
                            <div><h2>Your details</h2><p>We use these details for booking updates and the receipt.</p></div>
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
                            <div><h2>Event details</h2><p>Tell us when, where, and how many guests we're catering for.</p></div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="event_date" class="form-label">Event date *</label>
                                <input type="date" class="form-control @error('event_date') is-invalid @enderror" id="event_date" name="event_date" min="{{ now()->toDateString() }}" max="{{ now()->addDays(90)->toDateString() }}" value="{{ old('event_date', now()->toDateString()) }}" required>
                                @error('event_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="serving_period" class="form-label">Serving period *</label>
                                <select class="form-select @error('serving_period') is-invalid @enderror" id="serving_period" name="serving_period" required>
                                    <option value="breakfast" {{ old('serving_period') === 'breakfast' ? 'selected' : '' }}>Breakfast</option>
                                    <option value="lunch" {{ old('serving_period', 'lunch') === 'lunch' ? 'selected' : '' }}>Lunch</option>
                                    <option value="dinner" {{ old('serving_period') === 'dinner' ? 'selected' : '' }}>Dinner</option>
                                    <option value="full_day" {{ old('serving_period') === 'full_day' ? 'selected' : '' }}>Full day</option>
                                    <option value="custom" {{ old('serving_period') === 'custom' ? 'selected' : '' }}>Custom (see notes)</option>
                                </select>
                                @error('serving_period')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label d-block">How long do you need us? *</label>
                                <div class="duration-mode-group" role="radiogroup" aria-label="Booking duration">
                                    <label class="duration-mode-option">
                                        <input type="radio" name="duration_mode" value="hours" {{ old('duration_mode') === 'hours' ? 'checked' : '' }} required>
                                        <span><i class="bi bi-clock-history" aria-hidden="true"></i> A few hours</span>
                                    </label>
                                    <label class="duration-mode-option">
                                        <input type="radio" name="duration_mode" value="full_day" {{ old('duration_mode', 'full_day') === 'full_day' ? 'checked' : '' }} required>
                                        <span><i class="bi bi-sun" aria-hidden="true"></i> The whole day</span>
                                    </label>
                                    <label class="duration-mode-option">
                                        <input type="radio" name="duration_mode" value="multi_day" {{ old('duration_mode') === 'multi_day' ? 'checked' : '' }} required>
                                        <span><i class="bi bi-calendar-range" aria-hidden="true"></i> Multiple days</span>
                                    </label>
                                </div>
                                @error('duration_mode')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="start_time" class="form-label">Start time <span class="optional-label" id="startTimeOptionalTag">Optional</span></label>
                                <input type="time" class="form-control @error('start_time') is-invalid @enderror" id="start_time" name="start_time" value="{{ old('start_time') }}">
                                @error('start_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6" id="durationHoursWrap" hidden>
                                <label for="duration_hours" class="form-label">Hours needed *</label>
                                <select class="form-select @error('duration_hours') is-invalid @enderror" id="duration_hours" name="duration_hours">
                                    @foreach([2, 3, 4, 5, 6, 8, 10, 12] as $hrs)
                                        <option value="{{ $hrs }}" {{ (int) old('duration_hours') === $hrs ? 'selected' : '' }}>{{ $hrs }} hours</option>
                                    @endforeach
                                </select>
                                @error('duration_hours')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6" id="endDateWrap" hidden>
                                <label for="end_date" class="form-label">Last day of the event *</label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date') }}">
                                @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="guest_count" class="form-label">Number of guests *</label>
                                <input type="number" min="1" max="5000" class="form-control @error('guest_count') is-invalid @enderror" id="guest_count" name="guest_count" value="{{ old('guest_count', 1) }}" required>
                                @error('guest_count')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="event_type" class="form-label">Event type *</label>
                                <select class="form-select @error('event_type') is-invalid @enderror" id="event_type" name="event_type" required>
                                    <option value="">Choose one</option>
                                    @foreach(['Wedding', 'Corporate', 'Birthday', 'Baby Shower', 'Funeral', 'Other'] as $type)
                                        <option value="{{ $type }}" {{ old('event_type') === $type ? 'selected' : '' }}>{{ $type }}</option>
                                    @endforeach
                                </select>
                                @error('event_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label for="event_address" class="form-label">Event / delivery address *</label>
                                <textarea class="form-control @error('event_address') is-invalid @enderror" id="event_address" name="event_address" rows="3" autocomplete="street-address" placeholder="Venue name, street, suburb, or landmark" required>{{ old('event_address') }}</textarea>
                                @error('event_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="onsite_contact_name" class="form-label">On-site contact name <span class="optional-label">Optional</span></label>
                                <input type="text" class="form-control @error('onsite_contact_name') is-invalid @enderror" id="onsite_contact_name" name="onsite_contact_name" value="{{ old('onsite_contact_name') }}">
                                @error('onsite_contact_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="onsite_contact_phone" class="form-label">On-site contact phone <span class="optional-label">Optional</span></label>
                                <input type="tel" class="form-control @error('onsite_contact_phone') is-invalid @enderror" id="onsite_contact_phone" name="onsite_contact_phone" value="{{ old('onsite_contact_phone') }}">
                                @error('onsite_contact_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label for="event_notes" class="form-label">Event notes <span class="optional-label">Optional</span></label>
                                <textarea class="form-control @error('event_notes') is-invalid @enderror" id="event_notes" name="event_notes" rows="2" placeholder="Gate code, setup requirements, dietary notes, or a custom serving time">{{ old('event_notes') }}</textarea>
                                @error('event_notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </section>

                    <section class="checkout-card-section">
                        <div class="checkout-section-head">
                            <span class="checkout-section-number">03</span>
                            <div><h2>Special message</h2><p>Add a note for the card or the team preparing your order.</p></div>
                        </div>
                        <label for="special_message" class="form-label">Message <span class="optional-label">Optional · 500 characters</span></label>
                        <textarea class="form-control @error('special_message') is-invalid @enderror" id="special_message" name="special_message" rows="3" maxlength="500" placeholder="Happy birthday, congratulations, thank you…">{{ old('special_message') }}</textarea>
                        @error('special_message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </section>

                    @php $dpoReady = (bool) config('services.dpo.company_token'); @endphp
                    <section class="checkout-card-section">
                        <div class="checkout-section-head">
                            <span class="checkout-section-number">04</span>
                            <div><h2>Confirm with Kaleni Catering</h2><p>Final availability and payment are confirmed personally.</p></div>
                        </div>
                        @if($dpoReady)
                            <label class="payment-choice mb-2" for="payment_dpo">
                                <input type="radio" name="payment_method" id="payment_dpo" value="dpo" {{ old('payment_method') === 'dpo' ? 'checked' : '' }} required>
                                <span><strong><i class="bi bi-credit-card text-primary me-1"></i> Pay online now</strong><span>Secure card payment via DPO. Your booking is confirmed instantly.</span></span>
                            </label>
                        @endif
                        <label class="payment-choice" for="payment_whatsapp">
                            <input type="radio" name="payment_method" id="payment_whatsapp" value="whatsapp" {{ !$dpoReady || old('payment_method', 'whatsapp') === 'whatsapp' ? 'checked' : '' }} required>
                            <span><strong><i class="bi bi-whatsapp text-success me-1"></i> WhatsApp confirmation</strong><span>After booking, WhatsApp opens with your complete booking details ready to send.</span></span>
                        </label>
                        @error('payment_method')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
                        <button type="submit" class="buy-now-btn checkout-submit w-100 mt-3" id="checkoutSubmit"><i class="bi bi-bag-check me-1" aria-hidden="true"></i> Review &amp; confirm booking</button>
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
                        <div><span>Booking total</span><strong>N$ {{ number_format($total, 2) }}</strong></div>
                        <span>Confirmed separately</span>
                    </div>
                    <div class="checkout-assurance">
                        <span><i class="bi bi-check-circle-fill"></i> Cooked fresh for your event</span>
                        <span><i class="bi bi-check-circle-fill"></i> Free Windhoek delivery subject to availability</span>
                        <span><i class="bi bi-shield-check"></i> Your details are used only to fulfil this booking</span>
                    </div>
                </aside>
            </div>
        </div>
    </form>
</div>

<div class="modal fade" id="bookingConfirmModal" tabindex="-1" aria-labelledby="bookingConfirmTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content booking-confirm-card">
            <div class="modal-body p-4 p-md-5">
                <span class="section-kicker">Please review</span>
                <h2 class="h4 mb-3" id="bookingConfirmTitle">Confirm your booking</h2>
                <dl class="booking-confirm-list">
                    <div><dt>When</dt><dd id="confirmSchedule"></dd></div>
                    <div><dt>Guests</dt><dd id="confirmGuests"></dd></div>
                    <div><dt>Event type</dt><dd id="confirmEventType"></dd></div>
                    <div><dt>Address</dt><dd id="confirmAddress"></dd></div>
                    <div><dt>Total</dt><dd id="confirmTotal"></dd></div>
                </dl>
                <p class="text-muted small mb-4">Availability and final payment are still confirmed personally by our team after you submit.</p>
                <div class="d-flex flex-column-reverse flex-sm-row justify-content-sm-end gap-2">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Edit details</button>
                    <button type="button" class="buy-now-btn" style="width:auto; padding: 0 22px;" id="bookingConfirmSubmit">Yes, confirm &amp; book</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var form = document.getElementById('checkoutForm');
    if (!form) return;

    // --- Duration mode: show/hide + require only the fields that apply ---
    var startTimeInput = document.getElementById('start_time');
    var startTimeOptionalTag = document.getElementById('startTimeOptionalTag');
    var durationHoursWrap = document.getElementById('durationHoursWrap');
    var durationHoursInput = document.getElementById('duration_hours');
    var endDateWrap = document.getElementById('endDateWrap');
    var endDateInput = document.getElementById('end_date');
    var eventDateInput = document.getElementById('event_date');

    function applyDurationMode() {
        var checked = form.querySelector('input[name="duration_mode"]:checked');
        var mode = checked ? checked.value : 'full_day';

        durationHoursWrap.hidden = mode !== 'hours';
        durationHoursInput.required = mode === 'hours';
        if (mode !== 'hours') durationHoursInput.selectedIndex = 0;

        endDateWrap.hidden = mode !== 'multi_day';
        endDateInput.required = mode === 'multi_day';
        if (mode !== 'multi_day') endDateInput.value = '';

        startTimeInput.required = mode === 'hours';
        if (startTimeOptionalTag) startTimeOptionalTag.hidden = mode === 'hours';
    }

    form.querySelectorAll('input[name="duration_mode"]').forEach(function (radio) {
        radio.addEventListener('change', applyDurationMode);
    });
    applyDurationMode();

    // Keep the multi-day end date from ever being picked before the event date.
    eventDateInput.addEventListener('change', function () {
        endDateInput.min = eventDateInput.value;
        if (endDateInput.value && endDateInput.value < eventDateInput.value) {
            endDateInput.value = eventDateInput.value;
        }
    });
    if (eventDateInput.value) endDateInput.min = eventDateInput.value;

    // --- Review-before-submit: the form never actually posts until the
    // client has seen a plain-English summary and explicitly confirmed it. ---
    var confirmModalEl = document.getElementById('bookingConfirmModal');
    var confirmed = false;

    // Created lazily (not at parse time): this inline script runs while the
    // browser is still parsing the page, before bootstrap.bundle.min.js at
    // the bottom of <body> has executed, so window.bootstrap isn't defined
    // yet here — by the time a user actually submits, it always is.
    function getConfirmModal() {
        return window.bootstrap ? bootstrap.Modal.getOrCreateInstance(confirmModalEl) : null;
    }

    function formatDate(value) {
        if (!value) return '';
        var d = new Date(value + 'T00:00:00');
        if (isNaN(d)) return value;
        return d.toLocaleDateString('en-GB', { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' });
    }

    function formatTime(value) {
        if (!value) return '';
        var d = new Date('1970-01-01T' + value + ':00');
        if (isNaN(d)) return '';
        return d.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
    }

    function buildScheduleSummary() {
        var mode = (form.querySelector('input[name="duration_mode"]:checked') || {}).value || 'full_day';
        var date = formatDate(eventDateInput.value) || 'Date to be confirmed';
        var time = formatTime(startTimeInput.value);

        if (mode === 'hours') {
            var hrs = durationHoursInput.value;
            return date + (time ? ' at ' + time : '') + (hrs ? ' for ' + hrs + ' hour' + (hrs == 1 ? '' : 's') : '');
        }
        if (mode === 'multi_day') {
            var end = formatDate(endDateInput.value);
            return end ? date + ' to ' + end : date + ' (multiple days)';
        }
        return date + (time ? ' from ' + time : '') + ' (full day)';
    }

    form.addEventListener('submit', function (event) {
        if (confirmed) return; // second, real submission after the modal confirm

        event.preventDefault();
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        document.getElementById('confirmSchedule').textContent = buildScheduleSummary();
        document.getElementById('confirmGuests').textContent = (document.getElementById('guest_count').value || 'Not specified') + ' guests';
        document.getElementById('confirmEventType').textContent = document.getElementById('event_type').value || 'Not specified';
        document.getElementById('confirmAddress').textContent = document.getElementById('event_address').value || 'Not specified';
        document.getElementById('confirmTotal').textContent = 'N$ {{ number_format($total, 2) }}';

        var payDpo = document.getElementById('payment_dpo');
        var confirmBtn = document.getElementById('bookingConfirmSubmit');
        confirmBtn.textContent = (payDpo && payDpo.checked) ? 'Yes, confirm & pay now' : 'Yes, confirm & book';

        var modal = getConfirmModal();
        if (modal) {
            modal.show();
        } else {
            // Bootstrap failed to load for some reason — don't trap the client, submit anyway.
            confirmed = true;
            form.requestSubmit();
        }
    });

    document.getElementById('bookingConfirmSubmit').addEventListener('click', function () {
        var btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>Booking…';
        confirmed = true;
        var modal = getConfirmModal();
        if (modal) modal.hide();
        form.requestSubmit();
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
