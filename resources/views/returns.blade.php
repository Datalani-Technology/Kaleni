@extends('layouts.app')

@php($seoPage = 'returns')

@section('title', 'Returns & Flower Care - /Namsa Florals Namibia')

@section('content')
<div class="legal-page">
    <header class="legal-hero">
        <div><span class="section-kicker">Freshness, handled fairly</span><h1>Returns &amp; flower care</h1></div>
        <span class="legal-updated">Report order issues within 24 hours</span>
    </header>
    <div class="legal-layout">
        <nav class="legal-nav" aria-label="Returns sections"><strong>On this page</strong><a href="#issues">Order issues</a><a href="#process">What to send</a><a href="#resolution">Resolution</a><a href="#care">Flower care</a></nav>
        <article class="legal-content">
            <section class="legal-section" id="issues"><h2>If something is not right</h2><p>Fresh flowers are perishable, so we cannot normally accept returns for a change of mind. If an arrangement arrives damaged, materially different from what was agreed, or in poor condition, contact us within 24 hours of delivery.</p></section>
            <section class="legal-section" id="process"><h2>What to send</h2><ul><li>Your order number.</li><li>A short explanation of the problem.</li><li>Clear photographs of the complete arrangement and the affected flowers.</li><li>The best phone number for a quick response.</li></ul><p class="mt-3 mb-0">Send the details to <a href="mailto:{{ config('contact.email_orders') }}">{{ config('contact.email_orders') }}</a> or through <a href="{{ config('social.whatsapp') }}" target="_blank" rel="noopener">WhatsApp</a>.</p></section>
            <section class="legal-section" id="resolution"><h2>How we resolve it</h2><p>We assess each issue fairly based on the order, delivery timing, photographs, and the natural character of the flowers. Depending on the circumstances, we may offer a replacement, partial replacement, store credit, or refund. Approved refunds are normally processed within 5–7 business days, although banks can take longer to display them.</p></section>
            <section class="legal-section" id="care"><h2>Help the flowers last</h2><ul><li>Place cut flowers in a clean vase with fresh, cool water.</li><li>Trim stems at an angle before arranging them in the vase.</li><li>Remove leaves that sit below the water line.</li><li>Keep flowers away from direct sun, heat, strong drafts, and ripening fruit.</li><li>Refresh the water and re-trim stems every two days.</li></ul><div class="legal-callout mt-3">Some flowers arrive slightly closed so they can open naturally and last longer. This is a sign of freshness, not a defect.</div></section>
        </article>
    </div>
</div>
@endsection
