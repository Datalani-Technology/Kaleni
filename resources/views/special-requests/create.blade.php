@extends('layouts.app')

@php $seoPage = 'special-requests'; @endphp

@section('title', 'Request a Special Item - Kaleni Catering Services')

@push('styles')
<style>
    .sr-page { max-width: 760px; margin: 0 auto; padding: 48px 24px 80px; }
    .sr-heading {
        position: relative; overflow: hidden;
        text-align: center; margin-bottom: 40px; padding: 54px 24px;
        background: linear-gradient(120deg, var(--primary-color), var(--primary-dark));
        box-shadow: 0 20px 50px rgba(104,11,28,.18);
    }
    .sr-heading::after {
        content: ''; position: absolute; width: 320px; height: 320px; right: -120px; top: -140px;
        border: 46px solid rgba(248,173,39,.1); border-radius: 50%; pointer-events: none;
    }
    .sr-heading > * { position: relative; z-index: 1; }
    .sr-heading .section-kicker { color: var(--secondary-color); }
    .sr-heading h1 { font-size: clamp(1.9rem, 4vw, 2.7rem); font-weight: 800; margin: 6px 0 10px; color: #fff; }
    .sr-heading p { max-width: 560px; margin: 0 auto; color: rgba(255,255,255,.86); }
    .sr-card { padding: 28px; background: #fff; border: 1px solid var(--border); border-radius: 22px; box-shadow: var(--shadow-sm); }
    .sr-card .form-label { font-weight: 750; font-size: .82rem; margin-bottom: 6px; }
    .sr-card .form-control, .sr-card .form-select { min-height: 46px; border-color: var(--border); border-radius: 11px; }
    .sr-submit { min-height: 50px; width: 100%; margin-top: 8px; }
</style>
@endpush

@section('content')
<div class="sr-heading">
    <span class="section-kicker">Not on the menu?</span>
    <h1>Request a special item</h1>
    <p>Tell us what you have in mind: a dish, a package, or a full event menu. Kaleni Catering Services will follow up with a quote.</p>
</div>

<div class="sr-page">
    <div class="sr-card">
        <form action="{{ route('special-requests.store') }}" method="POST">
            @csrf
            <input type="hidden" name="source" value="{{ \App\Models\SpecialRequest::SOURCE_SPECIAL_REQUEST_PAGE }}">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">Full name *</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="phone" class="form-label">Phone number *</label>
                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" required>
                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label for="email" class="form-label">Email address *</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label d-block">How often do you need this? *</label>
                    <div class="d-flex flex-wrap gap-4">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="is_recurring" id="frequency_once" value="0" {{ old('is_recurring', '0') == '0' ? 'checked' : '' }}>
                            <label class="form-check-label" for="frequency_once">Just once</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="is_recurring" id="frequency_recurring" value="1" {{ old('is_recurring') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="frequency_recurring">Recurring (e.g. weekday office lunches)</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="event_date" id="event_date_label" class="form-label">Date needed *</label>
                    <input type="date" class="form-control @error('event_date') is-invalid @enderror" id="event_date" name="event_date" min="{{ now()->toDateString() }}" value="{{ old('event_date') }}" required>
                    @error('event_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6" id="recurring_end_date_wrap" style="display: {{ old('is_recurring') == '1' ? 'block' : 'none' }};">
                    <label for="recurring_end_date" class="form-label">Last day needed *</label>
                    <input type="date" class="form-control @error('recurring_end_date') is-invalid @enderror" id="recurring_end_date" name="recurring_end_date" min="{{ now()->toDateString() }}" value="{{ old('recurring_end_date') }}">
                    @error('recurring_end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="guest_count" class="form-label">Number of people *</label>
                    <input type="number" min="1" max="5000" class="form-control @error('guest_count') is-invalid @enderror" id="guest_count" name="guest_count" value="{{ old('guest_count') }}" placeholder="1 or more" required>
                    @error('guest_count')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="occasion" class="form-label">Occasion *</label>
                    <input type="text" class="form-control @error('occasion') is-invalid @enderror" id="occasion" name="occasion" value="{{ old('occasion') }}" placeholder="Wedding, birthday, office lunch…" required>
                    @error('occasion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="budget_range" class="form-label">Budget range *</label>
                    <input type="text" class="form-control @error('budget_range') is-invalid @enderror" id="budget_range" name="budget_range" value="{{ old('budget_range') }}" placeholder="e.g. N$ 2,000 - N$ 4,000" required>
                    @error('budget_range')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label for="details" class="form-label">What would you like us to prepare? *</label>
                    <textarea class="form-control @error('details') is-invalid @enderror" id="details" name="details" rows="5" placeholder="Describe the dish, menu, or package you have in mind." required>{{ old('details') }}</textarea>
                    @error('details')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <button type="submit" class="buy-now-btn sr-submit"><i class="bi bi-envelope me-1"></i> Send request</button>
        </form>
    </div>
</div>

<script>
(function () {
    var onceRadio = document.getElementById('frequency_once');
    var recurringRadio = document.getElementById('frequency_recurring');
    var endDateWrap = document.getElementById('recurring_end_date_wrap');
    var endDateInput = document.getElementById('recurring_end_date');
    var eventDateLabel = document.getElementById('event_date_label');

    function sync() {
        var isRecurring = recurringRadio.checked;
        endDateWrap.style.display = isRecurring ? 'block' : 'none';
        endDateInput.required = isRecurring;
        eventDateLabel.textContent = isRecurring ? 'First day needed *' : 'Date needed *';
    }

    [onceRadio, recurringRadio].forEach(function (radio) {
        radio.addEventListener('change', sync);
    });
    sync();
})();
</script>
@endsection
