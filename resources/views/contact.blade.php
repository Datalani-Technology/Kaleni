@extends('layouts.app')

@php
    $seoPage = 'contact';
    $structuredData = [\App\Services\SeoService::generateStructuredData('organization')];
@endphp

@section('title', 'Contact Our Florist - /Namsa Florals Namibia')

@push('styles')
<style>
    .contact-page { max-width: 1180px; margin: 0 auto; padding: 58px 24px 20px; }
    .contact-heading { display: grid; grid-template-columns: minmax(0,1fr) minmax(280px,.65fr); align-items: end; gap: 40px; margin-bottom: 36px; }
    .contact-heading h1 { max-width: 700px; margin: 8px 0 0; font-size: clamp(2.5rem,5vw,4.7rem); font-weight: 800; line-height: 1; letter-spacing: -.05em; }
    .contact-heading p { margin: 0; color: var(--muted); line-height: 1.75; }
    .contact-shell { display: grid; grid-template-columns: minmax(300px,.75fr) minmax(0,1.25fr); overflow: hidden; background: #fff; border: 1px solid var(--border); border-radius: 28px; box-shadow: 0 24px 65px rgba(52,27,40,.1); }
    .contact-details { position: relative; overflow: hidden; padding: clamp(30px,5vw,52px); color: #fff; background: #21171d; }
    .contact-details::after { content: ''; position: absolute; width: 300px; height: 300px; right: -160px; bottom: -150px; border: 45px solid rgba(180,35,99,.12); border-radius: 50%; }
    .contact-details > * { position: relative; z-index: 1; }
    .contact-details h2 { margin: 0 0 10px; font-size: 1.45rem; font-weight: 800; }
    .contact-details > p { margin: 0 0 34px; color: rgba(255,255,255,.65); line-height: 1.7; }
    .contact-list { display: grid; gap: 11px; }
    .contact-item { display: grid; grid-template-columns: 42px 1fr; align-items: center; gap: 13px; padding: 13px 0; color: #fff; text-decoration: none; }
    .contact-item-icon { display: grid; width: 42px; height: 42px; place-items: center; color: #ff84b9; background: rgba(255,255,255,.07); border: 1px solid rgba(255,255,255,.1); border-radius: 12px; }
    .contact-item small { display: block; margin-bottom: 2px; color: rgba(255,255,255,.52); font-size: .68rem; font-weight: 750; letter-spacing: .06em; text-transform: uppercase; }
    .contact-item span { font-size: .86rem; font-weight: 700; }
    .contact-hours { margin-top: 28px; padding-top: 25px; border-top: 1px solid rgba(255,255,255,.12); }
    .contact-hours h3 { margin-bottom: 12px; font-size: .78rem; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; }
    .hours-row { display: flex; justify-content: space-between; gap: 12px; padding: 4px 0; color: rgba(255,255,255,.68); font-size: .78rem; }
    .contact-social { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 25px; }
    .contact-social a { display: grid; width: 38px; height: 38px; place-items: center; color: #fff; background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.12); border-radius: 50%; text-decoration: none; }
    .contact-form-wrap { padding: clamp(30px,5vw,55px); }
    .contact-form-wrap h2 { margin: 0 0 7px; font-size: 1.55rem; font-weight: 800; }
    .contact-form-intro { margin-bottom: 26px; color: var(--muted); font-size: .87rem; }
    .contact-form-wrap .form-label { margin-bottom: 6px; font-size: .76rem; font-weight: 750; }
    .contact-form-wrap .form-control { min-height: 46px; border-color: var(--border); border-radius: 11px; font-size: .88rem; }
    .contact-form-wrap textarea.form-control { min-height: 130px; }
    .human-verify { display: flex; align-items: center; gap: 12px; padding: 12px 14px; background: #faf5f7; border: 1px solid var(--border); border-radius: 12px; }
    .human-verify .form-label { margin: 0; }
    .human-verify .form-control { width: 88px; flex: 0 0 88px; }
    @media (max-width: 860px) { .contact-heading, .contact-shell { grid-template-columns: 1fr; } .contact-heading { gap: 15px; } }
    @media (max-width: 520px) { .contact-page { padding: 36px 14px 10px; } .contact-shell { border-radius: 20px; } .human-verify { align-items: flex-start; flex-direction: column; } }
</style>
@endpush

@section('content')
<div class="contact-page">
    <header class="contact-heading">
        <div><span class="section-kicker">Talk to a real florist</span><h1>Let’s make the gesture feel exactly right.</h1></div>
        <p>Ask about a bouquet, delivery timing, or something completely personal. Our Windhoek team will help you shape the details.</p>
    </header>

    <div class="contact-shell">
        <aside class="contact-details">
            <h2>We are close by.</h2>
            <p>For the quickest response, call or send us a WhatsApp message during business hours.</p>
            <div class="contact-list">
                <div class="contact-item"><span class="contact-item-icon"><i class="bi bi-geo-alt"></i></span><span><small>Visit</small><span>{{ config('contact.address.name') }}<br>{{ config('contact.address.city') }}, {{ config('contact.address.country') }}</span></span></div>
                <a class="contact-item" href="tel:{{ preg_replace('/[^+0-9]/', '', config('contact.phone')) }}"><span class="contact-item-icon"><i class="bi bi-telephone"></i></span><span><small>Call</small><span>{{ config('contact.phone') }}</span></span></a>
                <a class="contact-item" href="mailto:{{ config('contact.email_info') }}"><span class="contact-item-icon"><i class="bi bi-envelope"></i></span><span><small>General enquiries</small><span>{{ config('contact.email_info') }}</span></span></a>
                <a class="contact-item" href="mailto:{{ config('contact.email_orders') }}"><span class="contact-item-icon"><i class="bi bi-bag-heart"></i></span><span><small>Order support</small><span>{{ config('contact.email_orders') }}</span></span></a>
            </div>
            <div class="contact-hours">
                <h3>Business hours</h3>
                <div class="hours-row"><span>Monday–Friday</span><strong>08:00–18:00</strong></div>
                <div class="hours-row"><span>Saturday</span><strong>09:00–16:00</strong></div>
                <div class="hours-row"><span>Sunday</span><strong>Closed</strong></div>
            </div>
            <div class="contact-social" aria-label="Social media">
                <a href="{{ config('social.facebook') }}" target="_blank" rel="noopener" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                <a href="{{ config('social.instagram') }}" target="_blank" rel="noopener" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                <a href="{{ config('social.tiktok') }}" target="_blank" rel="noopener" aria-label="TikTok"><i class="bi bi-music-note-beamed"></i></a>
                <a href="{{ config('social.whatsapp') }}" target="_blank" rel="noopener" aria-label="WhatsApp"><i class="bi bi-whatsapp"></i></a>
            </div>
        </aside>

        <section class="contact-form-wrap">
            <h2>Send a message</h2>
            <p class="contact-form-intro">Tell us what you have in mind. We will reply using the email address you provide.</p>
            <form action="{{ route('contact.store') }}" method="POST" id="contactForm">
                @csrf
                <div style="position:absolute;left:-9999px;opacity:0;pointer-events:none" aria-hidden="true"><label for="website">Website</label><input type="text" name="website" id="website" tabindex="-1" autocomplete="off"></div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Your name *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" minlength="2" maxlength="255" autocomplete="name" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email address *</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" maxlength="255" autocomplete="email" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label for="subject" class="form-label">Subject *</label>
                        <input type="text" class="form-control @error('subject') is-invalid @enderror" id="subject" name="subject" value="{{ old('subject') }}" minlength="3" maxlength="255" placeholder="Bouquet advice, delivery question, custom order…" required>
                        @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label for="message" class="form-label">Message *</label>
                        <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" minlength="10" maxlength="5000" placeholder="How can we help?" required>{{ old('message') }}</textarea>
                        @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        @error('rate_limit')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <div class="human-verify">
                            <label for="human_check" class="form-label flex-grow-1">A quick check: what is <strong>{{ $mathA }} + {{ $mathB }}</strong>?</label>
                            <input type="number" class="form-control @error('human_check') is-invalid @enderror" id="human_check" name="human_check" value="{{ old('human_check') }}" placeholder="Answer" inputmode="numeric" required>
                        </div>
                        @error('human_check')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12"><button type="submit" class="buy-now-btn" id="submitBtn"><span class="button-label">Send message</span> <i class="bi bi-arrow-right"></i></button></div>
                </div>
            </form>
        </section>
    </div>
</div>

<script>
document.getElementById('contactForm')?.addEventListener('submit', function () {
    var button = document.getElementById('submitBtn');
    if (!button) return;
    button.disabled = true;
    button.innerHTML = '<span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>Sending…';
});
</script>
@endsection
