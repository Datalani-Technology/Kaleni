@extends('layouts.app')

@php($seoPage = 'cancellations')

@section('title', 'Cancellations & Refunds - Kaleni Catering Services Namibia')

@section('content')
<div class="legal-page">
    <header class="legal-hero">
        <div><span class="section-kicker">Fair, handled personally</span><h1>Cancellations &amp; refunds</h1></div>
        <span class="legal-updated">Report order issues within 24 hours</span>
    </header>
    <div class="legal-layout">
        <nav class="legal-nav" aria-label="Cancellations sections"><strong>On this page</strong><a href="#cancelling">Cancelling a booking</a><a href="#issues">Order issues</a><a href="#process">What to send</a><a href="#resolution">Resolution</a></nav>
        <article class="legal-content">
            <section class="legal-section" id="cancelling"><h2>Cancelling a booking</h2><p>Plans change. Contact us as soon as possible if you need to cancel or reschedule a booking. Cancellations made well ahead of the event date are easiest to accommodate in full. Cancellations made close to the event, or after preparation has started, may only be partially refundable since ingredients and staff time have already been committed.</p></section>
            <section class="legal-section" id="issues"><h2>If something is not right</h2><p>Prepared food is perishable, so we cannot normally accept returns for a change of mind once an order has been delivered. If an order arrives incomplete, materially different from what was agreed, or in poor condition, contact us within 24 hours of delivery.</p></section>
            <section class="legal-section" id="process"><h2>What to send</h2><ul><li>Your booking number.</li><li>A short explanation of the problem.</li><li>Clear photographs of the food received.</li><li>The best phone number for a quick response.</li></ul><p class="mt-3 mb-0">Send the details to <a href="mailto:{{ config('contact.email_orders') }}">{{ config('contact.email_orders') }}</a> or through <a href="{{ config('social.whatsapp') }}" target="_blank" rel="noopener">WhatsApp</a>.</p></section>
            <section class="legal-section" id="resolution"><h2>How we resolve it</h2><p>We assess each issue fairly based on the booking, delivery timing, and photographs provided. Depending on the circumstances, we may offer a replacement, partial replacement, store credit, or refund. Approved refunds are normally processed within 5 to 7 business days, although banks can take longer to display them.</p><div class="legal-callout mt-3">A deposit paid to secure an event date is generally non-refundable once preparation has begun, but is fully transferable to a new date where possible.</div></section>
        </article>
    </div>
</div>
@endsection
