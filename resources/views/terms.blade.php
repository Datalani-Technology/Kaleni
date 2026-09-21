@extends('layouts.app')

@php($seoPage = 'terms')

@section('title', 'Terms and Conditions - Kaleni Catering Services Namibia')

@section('content')
<div class="legal-page">
    <header class="legal-hero">
        <div><span class="section-kicker">Clear, human terms</span><h1>Terms &amp; conditions</h1></div>
        <span class="legal-updated">Last updated {{ now()->format('F j, Y') }}</span>
    </header>
    <div class="legal-layout">
        <nav class="legal-nav" aria-label="Terms sections">
            <strong>On this page</strong>
            <a href="#bookings">Bookings</a><a href="#payment">Payment</a><a href="#delivery">Delivery</a><a href="#food">Food &amp; substitutions</a><a href="#cancellations">Cancellations</a><a href="#privacy">Privacy</a><a href="#contact">Contact</a>
        </nav>
        <article class="legal-content">
            <section class="legal-section" id="bookings"><h2>1. Bookings and availability</h2><p>By placing an order or booking, you ask Kaleni Catering Services to prepare and deliver the selected menu items for your chosen event date and serving period. Bookings are subject to acceptance, ingredient availability, kitchen capacity, and confirmation from our team.</p><p>Prices are shown in Namibian dollars (NAD) and may change before a booking is placed. A confirmed booking keeps the price shown in its order summary unless you approve a requested change.</p></section>
            <section class="legal-section" id="payment"><h2>2. Payment and confirmation</h2><p>Booking currently creates a record and opens WhatsApp so our team can confirm availability and arrange payment. A booking is processed after payment and event details are confirmed.</p><div class="legal-callout">Card or gateway payment will only be offered when it appears as an enabled option during booking. A future payment option is not considered available merely because it is mentioned elsewhere.</div></section>
            <section class="legal-section" id="delivery"><h2>3. Delivery</h2><p>We deliver within Windhoek and selected surrounding areas, free of charge unless otherwise agreed. Serving periods are preferences rather than guarantees and depend on traffic, venue access, weather, and kitchen capacity.</p><ul><li>Provide a complete event address, a working on-site contact phone number, and any access instructions.</li><li>Same-day requests are subject to the day's cutoff and capacity.</li><li>Additional delivery charges may apply depending on location and will be confirmed before payment.</li></ul><p class="mt-3 mb-0">Read the full <a href="{{ route('delivery') }}">delivery &amp; event setup guide</a>.</p></section>
            <section class="legal-section" id="food"><h2>4. Food and substitutions</h2><p>Dishes are prepared fresh and quantities, garnish, and side dishes may vary slightly from photography. If an ingredient is unavailable, we may propose a substitution of similar style and value before fulfilment. Major changes will be discussed with you.</p></section>
            <section class="legal-section" id="cancellations"><h2>5. Cancellations and refunds</h2><p>Prepared food is perishable and cannot normally be returned because of a change of mind once delivered. If a booking arrives incomplete, materially incorrect, or in poor condition, contact us within 24 hours with the booking number and clear photographs so we can assess a replacement, store credit, or refund.</p><p class="mb-0">See <a href="{{ route('cancellations') }}">cancellations &amp; refunds</a> for the complete process.</p></section>
            <section class="legal-section" id="privacy"><h2>6. Privacy</h2><p>We use personal information to manage bookings, delivery, payment confirmation, and support. Read our <a href="{{ route('privacy') }}">privacy policy</a> for details about information, access, and retention.</p></section>
            <section class="legal-section"><h2>7. Liability and changes</h2><p>To the extent permitted by applicable law, Kaleni Catering Services is not responsible for indirect or consequential loss. Nothing in these terms removes rights that cannot legally be excluded. We may update these terms, with changes applying from the date published on this page.</p></section>
            <section class="legal-section" id="contact"><h2>8. Contact</h2><p>Questions about a booking or these terms? Email <a href="mailto:{{ config('contact.email_info') }}">{{ config('contact.email_info') }}</a> or call <a href="tel:{{ preg_replace('/[^+0-9]/', '', config('contact.phone')) }}">{{ config('contact.phone') }}</a>.</p></section>
        </article>
    </div>
</div>
@endsection
