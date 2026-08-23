@extends('layouts.app')

@php
    $seoPage = 'privacy';
@endphp

@section('title', 'Privacy Policy - /Namsa Florals Namibia')

@push('styles')
<style>
    @media (max-width: 768px) { .terms-page .card-body { padding: 1.5rem !important; } }
    @media (max-width: 400px) {
        .terms-page h1 { font-size: 1.5rem; }
        .terms-page .card-body { padding: 1.25rem !important; }
        .terms-page h4 { font-size: 1.1rem; }
        .terms-page p { font-size: 0.9rem; }
    }
</style>
@endpush

@section('content')
<div class="container my-5 terms-page">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <h1 class="mb-4">Privacy Policy</h1>

            <div class="card">
                <div class="card-body p-5">
                    <p class="text-muted">Last updated: {{ date('F d, Y') }}</p>

                    <h4 class="mt-4 mb-3">1. What we collect</h4>
                    <p>
                        When you place an order or contact us, we collect: your name, email address, phone
                        number, and delivery address. We do not require you to create an account or log in
                        to shop with us.
                    </p>

                    <h4 class="mt-4 mb-3">2. Why we collect it</h4>
                    <p>
                        - To process, fulfil, and deliver your order.<br>
                        - To send you an order confirmation and, where relevant, your receipt.<br>
                        - To respond to enquiries submitted through our contact form.<br>
                        - To keep a record of your order history, so our team can assist you faster if you
                        order again or need support.
                    </p>

                    <h4 class="mt-4 mb-3">3. Who can see it</h4>
                    <p>
                        Your information is only accessible to authorised /Namsa Florals staff who manage
                        orders, delivery, and customer support. Staff accounts are protected by two-factor
                        authentication, and access to customer records is logged. We do not sell, rent, or
                        share your personal information with third parties for marketing purposes.
                    </p>
                    <p>
                        Payments made via the DPO payment gateway are processed directly by DPO Group under
                        their own privacy and security terms — we do not store your card details.
                    </p>

                    <h4 class="mt-4 mb-3">4. How long we keep it</h4>
                    <p>
                        We retain order and contact records for as long as needed to fulfil orders, handle
                        support requests, and meet our accounting and legal obligations.
                    </p>

                    <h4 class="mt-4 mb-3">5. Your rights</h4>
                    <p>
                        You can ask us what information we hold about you, ask us to correct it, or ask us
                        to delete it (subject to what we're legally required to keep, e.g. for tax records).
                        Contact us using the details below to make a request.
                    </p>

                    <h4 class="mt-4 mb-3">6. Cookies</h4>
                    <p>
                        We use a session cookie to remember your shopping cart while you browse. We don't
                        use tracking or advertising cookies.
                    </p>

                    <h4 class="mt-4 mb-3">7. Changes to this policy</h4>
                    <p>We may update this policy from time to time. Changes take effect immediately upon posting on this page.</p>

                    <h4 class="mt-4 mb-3">8. Contact us</h4>
                    <p>
                        For any questions about this policy or your personal information, contact us:<br>
                        Email: {{ config('contact.email_info') }}<br>
                        Phone: {{ config('contact.phone') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
