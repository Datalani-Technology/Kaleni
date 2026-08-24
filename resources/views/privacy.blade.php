@extends('layouts.app')

@php($seoPage = 'privacy')

@section('title', 'Privacy Policy - /Namsa Florals Namibia')

@section('content')
<div class="legal-page">
    <header class="legal-hero">
        <div><span class="section-kicker">Your information</span><h1>Privacy policy</h1></div>
        <span class="legal-updated">Last updated {{ now()->format('F j, Y') }}</span>
    </header>
    <div class="legal-layout">
        <nav class="legal-nav" aria-label="Privacy sections">
            <strong>On this page</strong>
            <a href="#collect">What we collect</a><a href="#use">How we use it</a><a href="#access">Access</a><a href="#retention">Retention</a><a href="#cookies">Cookies</a><a href="#rights">Your choices</a>
        </nav>
        <article class="legal-content">
            <section class="legal-section" id="collect"><h2>1. What we collect</h2><p>When you order or contact us, we may collect your name, email address, phone number, recipient details, delivery address, preferred delivery date, gift message, delivery instructions, and the contents of your enquiry. You can shop without creating a customer account.</p></section>
            <section class="legal-section" id="use"><h2>2. How we use information</h2><ul><li>Prepare, confirm, and deliver orders.</li><li>Contact you or the recipient about access, timing, substitutions, and payment.</li><li>Send order confirmations and receipts.</li><li>Respond to contact-form enquiries and provide support.</li><li>Maintain order, stock, accounting, and security records.</li></ul></section>
            <section class="legal-section" id="access"><h2>3. Who can access it</h2><p>Authorised /Namsa Florals staff can access the information needed for orders, delivery, and customer support. Administrative accounts use security controls including two-factor authentication and activity logging. We do not sell or rent personal information for advertising.</p><p>When payment is arranged through WhatsApp or another enabled provider, that service handles information under its own privacy terms. This website does not collect or store card numbers.</p></section>
            <section class="legal-section" id="retention"><h2>4. How long we keep it</h2><p>Order and contact records are retained only as long as reasonably needed for fulfilment, support, security, accounting, and legal obligations. Records that are no longer required should be deleted or anonymised.</p></section>
            <section class="legal-section" id="cookies"><h2>5. Cookies and measurement</h2><p>A session cookie keeps your cart connected while you browse. The system may also record basic visit information for operational analytics and security. We do not use third-party advertising cookies on the storefront.</p></section>
            <section class="legal-section" id="rights"><h2>6. Your choices</h2><p>You can ask what personal information we hold, request a correction, or request deletion where we are not legally required to retain a record. Email <a href="mailto:{{ config('contact.email_info') }}">{{ config('contact.email_info') }}</a> or call <a href="tel:{{ preg_replace('/[^+0-9]/', '', config('contact.phone')) }}">{{ config('contact.phone') }}</a>.</p><div class="legal-callout">For privacy requests, include enough information for us to identify the relevant order or enquiry. We may need to verify that the request is genuinely yours.</div></section>
            <section class="legal-section"><h2>7. Policy changes</h2><p>We may revise this policy when our services or legal obligations change. The current version and update date will always appear on this page.</p></section>
        </article>
    </div>
</div>
@endsection
