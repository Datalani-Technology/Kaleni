@extends('layouts.app')

@php($seoPage = 'delivery')

@section('title', 'Flower Delivery Guide - /Namsa Florals Windhoek')

@section('content')
<div class="legal-page">
    <header class="legal-hero">
        <div><span class="section-kicker">From our hands to theirs</span><h1>Delivery guide</h1></div>
        <span class="legal-updated">Windhoek &amp; selected surrounding areas</span>
    </header>
    <div class="legal-layout">
        <nav class="legal-nav" aria-label="Delivery sections"><strong>On this page</strong><a href="#area">Delivery area</a><a href="#same-day">Same-day</a><a href="#timing">Timing</a><a href="#recipient">Recipient</a><a href="#changes">Changes</a></nav>
        <article class="legal-content">
            <section class="legal-section" id="area"><h2>Where we deliver</h2><p>We deliver within Windhoek and selected nearby areas. Enter the full street address, suburb, building name, and a useful landmark at checkout. Delivery outside our usual area is confirmed individually and may carry an additional charge.</p></section>
            <section class="legal-section" id="same-day"><h2>Same-day requests</h2><p>Same-day delivery is available when flowers, drivers, and preparation time allow. Place the order as early as possible, select today at checkout, and send the WhatsApp confirmation. Our florist will confirm whether the request can be met before payment is finalised.</p><div class="legal-callout">A same-day selection is a request, not an automatic guarantee. We will suggest the closest available alternative if capacity is full.</div></section>
            <section class="legal-section" id="timing"><h2>Delivery windows</h2><p>Choose morning, afternoon, or best available during checkout. Windows help us plan a route but are not exact appointments. Traffic, weather, access, and recipient availability can affect arrival time.</p></section>
            <section class="legal-section" id="recipient"><h2>Recipient details and access</h2><ul><li>Provide a working recipient phone number.</li><li>Add gate, reception, floor, office, or landmark instructions.</li><li>Tell us when a surprise must remain discreet.</li><li>Make sure someone can receive the flowers during the preferred window.</li></ul><p class="mt-3">If the recipient cannot be reached, we will contact the customer to arrange the safest next step. A second delivery attempt may cost extra.</p></section>
            <section class="legal-section" id="changes"><h2>Changing a delivery</h2><p>Contact us as soon as possible. We can usually update details before preparation or dispatch, but changes are not guaranteed after a driver has left. Call <a href="tel:{{ preg_replace('/[^+0-9]/', '', config('contact.phone')) }}">{{ config('contact.phone') }}</a> or message us on <a href="{{ config('social.whatsapp') }}" target="_blank" rel="noopener">WhatsApp</a>.</p></section>
        </article>
    </div>
</div>
@endsection
