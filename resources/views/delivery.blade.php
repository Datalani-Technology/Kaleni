@extends('layouts.app')

@php($seoPage = 'delivery')

@section('title', 'Delivery & Event Setup - Kaleni Catering Services Windhoek')

@section('content')
<div class="legal-page">
    <header class="legal-hero">
        <div><span class="section-kicker">From our kitchen to your table</span><h1>Delivery &amp; event setup</h1></div>
        <span class="legal-updated">Windhoek &amp; selected surrounding areas</span>
    </header>
    <div class="legal-layout">
        <nav class="legal-nav" aria-label="Delivery sections"><strong>On this page</strong><a href="#area">Delivery area</a><a href="#same-day">Same-day &amp; events</a><a href="#timing">Serving times</a><a href="#onsite">On-site contact</a><a href="#changes">Changes</a></nav>
        <article class="legal-content">
            <section class="legal-section" id="area"><h2>Where we deliver</h2><p>We deliver free of charge within Windhoek and selected nearby areas. Give the full street address, suburb, venue name, and a useful landmark when you book. Delivery outside our usual area is confirmed individually and may carry an additional charge.</p></section>
            <section class="legal-section" id="same-day"><h2>Same-day orders &amp; event bookings</h2><p>Same-day pack orders are available when kitchen capacity allows. For full event catering, book as early as possible so we can plan quantities, staff, and delivery around your date and guest count. Our team will confirm availability before payment is finalised.</p><div class="legal-callout">A requested date and serving period is a request, not an automatic guarantee. We will suggest the closest available alternative if capacity is full.</div></section>
            <section class="legal-section" id="timing"><h2>Serving periods</h2><p>Choose breakfast, lunch, dinner, full day, or a custom time when you book. These help us plan preparation and delivery but are not exact appointments. Traffic, venue access, and kitchen volume can affect arrival time.</p></section>
            <section class="legal-section" id="onsite"><h2>On-site contact &amp; access</h2><ul><li>Provide a working on-site contact phone number for the day of the event.</li><li>Add gate, reception, floor, or landmark instructions in the event notes.</li><li>Make sure someone can receive and set up the food during the agreed period.</li></ul><p class="mt-3">If the on-site contact cannot be reached, we will contact the customer to arrange the safest next step. A second delivery attempt may cost extra.</p></section>
            <section class="legal-section" id="changes"><h2>Changing a booking</h2><p>Contact us as soon as possible. We can usually update guest count, timing, or the menu before preparation begins, but changes are not guaranteed once cooking has started or a driver has left. Call <a href="tel:{{ preg_replace('/[^+0-9]/', '', config('contact.phone')) }}">{{ config('contact.phone') }}</a> or message us on <a href="{{ config('social.whatsapp') }}" target="_blank" rel="noopener">WhatsApp</a>.</p></section>
        </article>
    </div>
</div>
@endsection
