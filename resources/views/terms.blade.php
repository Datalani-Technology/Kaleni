@extends('layouts.app')

@php($seoPage = 'terms')

@section('title', 'Terms and Conditions - /Namsa Florals Namibia')

@section('content')
<div class="legal-page">
    <header class="legal-hero">
        <div><span class="section-kicker">Clear, human terms</span><h1>Terms &amp; conditions</h1></div>
        <span class="legal-updated">Last updated {{ now()->format('F j, Y') }}</span>
    </header>
    <div class="legal-layout">
        <nav class="legal-nav" aria-label="Terms sections">
            <strong>On this page</strong>
            <a href="#orders">Orders</a><a href="#payment">Payment</a><a href="#delivery">Delivery</a><a href="#flowers">Fresh flowers</a><a href="#returns">Returns</a><a href="#privacy">Privacy</a><a href="#contact">Contact</a>
        </nav>
        <article class="legal-content">
            <section class="legal-section" id="orders"><h2>1. Orders and availability</h2><p>By placing an order, you ask /Namsa Florals to prepare and deliver the selected products. Orders are subject to acceptance, flower availability, delivery capacity, and confirmation from our team.</p><p>Prices are shown in Namibian dollars (NAD) and may change before an order is placed. A confirmed order keeps the price shown in its order summary unless you approve a requested change.</p></section>
            <section class="legal-section" id="payment"><h2>2. Payment and confirmation</h2><p>Checkout currently creates an order and opens WhatsApp so our florist can confirm delivery availability and arrange payment. An order is processed after payment and delivery details are confirmed.</p><div class="legal-callout">Card or gateway payment will only be offered when it appears as an enabled option during checkout. A future payment option is not considered available merely because it is mentioned elsewhere.</div></section>
            <section class="legal-section" id="delivery"><h2>3. Delivery</h2><p>We deliver within Windhoek and selected surrounding areas. Delivery windows are preferences rather than guarantees and depend on traffic, recipient availability, weather, and florist capacity.</p><ul><li>Provide a complete address, working recipient phone number, and any access instructions.</li><li>Same-day requests are subject to the day’s cutoff and capacity.</li><li>Additional delivery charges may apply depending on location and will be confirmed before payment.</li></ul><p class="mt-3 mb-0">Read the full <a href="{{ route('delivery') }}">delivery guide</a>.</p></section>
            <section class="legal-section" id="flowers"><h2>4. Fresh flowers and substitutions</h2><p>Flowers are natural, seasonal products. Colour, bloom stage, stem count, wrapping, and foliage may vary slightly from photography. If a flower is unavailable, we may propose a substitution of similar style and value before fulfilment. Major changes will be discussed with you.</p></section>
            <section class="legal-section" id="returns"><h2>5. Problems, returns, and refunds</h2><p>Fresh flowers are perishable and cannot normally be returned because of a change of mind. If an order arrives damaged, materially incorrect, or in poor condition, contact us within 24 hours with the order number and clear photographs so we can assess a replacement, store credit, or refund.</p><p class="mb-0">See <a href="{{ route('returns') }}">returns &amp; flower care</a> for the complete process.</p></section>
            <section class="legal-section" id="privacy"><h2>6. Privacy</h2><p>We use personal information to manage orders, delivery, payment confirmation, and support. Read our <a href="{{ route('privacy') }}">privacy policy</a> for details about information, access, and retention.</p></section>
            <section class="legal-section"><h2>7. Liability and changes</h2><p>To the extent permitted by applicable law, /Namsa Florals is not responsible for indirect or consequential loss. Nothing in these terms removes rights that cannot legally be excluded. We may update these terms, with changes applying from the date published on this page.</p></section>
            <section class="legal-section" id="contact"><h2>8. Contact</h2><p>Questions about an order or these terms? Email <a href="mailto:{{ config('contact.email_info') }}">{{ config('contact.email_info') }}</a> or call <a href="tel:{{ preg_replace('/[^+0-9]/', '', config('contact.phone')) }}">{{ config('contact.phone') }}</a>.</p></section>
        </article>
    </div>
</div>
@endsection
