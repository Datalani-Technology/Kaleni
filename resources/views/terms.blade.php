@extends('layouts.app')

@php
    $seoPage = 'terms';
@endphp

@section('title', 'Terms and Conditions - /Namsa Florals Namibia')

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
            <h1 class="mb-4">Terms and Conditions</h1>
            
            <div class="card">
                <div class="card-body p-5">
                    <p class="text-muted">Last updated: {{ date('F d, Y') }}</p>
                    
                    <h4 class="mt-4 mb-3">1. Acceptance of Terms</h4>
                    <p>By accessing and using the /Namsa Florals website, you accept and agree to be bound by the terms and provision of this agreement.</p>
                    
                    <h4 class="mt-4 mb-3">2. Products and Services</h4>
                    <p>All products displayed on our website are subject to availability. We reserve the right to discontinue any product at any time. Prices are subject to change without notice.</p>
                    
                    <h4 class="mt-4 mb-3">3. Orders and Payment</h4>
                    <p>
                        - All orders are subject to product availability and acceptance by /Namsa Florals.<br>
                        - Payment can be made via DPO payment gateway or WhatsApp payment.<br>
                        - We accept payments in Namibian Dollars (NAD).<br>
                        - Orders will be processed only after payment confirmation.
                    </p>
                    
                    <h4 class="mt-4 mb-3">4. Delivery</h4>
                    <p>
                        - Delivery times are estimates and not guaranteed.<br>
                        - Delivery charges may apply depending on location.<br>
                        - We deliver within Windhoek and surrounding areas.<br>
                        - Customers are responsible for providing accurate delivery addresses.
                    </p>
                    
                    <h4 class="mt-4 mb-3">5. Returns and Refunds</h4>
                    <p>
                        - Fresh flowers are perishable items and cannot be returned unless damaged or incorrect.<br>
                        - Refunds will be processed within 5-7 business days.<br>
                        - Contact us within 24 hours of delivery for any issues.
                    </p>
                    
                    <h4 class="mt-4 mb-3">6. Product Quality</h4>
                    <p>We strive to provide fresh, high-quality flowers. However, due to the nature of fresh products, slight variations may occur.</p>
                    
                    <h4 class="mt-4 mb-3">7. Privacy Policy</h4>
                    <p>Your personal information will be used solely for order processing and delivery. We do not share your information with third parties.</p>
                    
                    <h4 class="mt-4 mb-3">8. Limitation of Liability</h4>
                    <p>/Namsa Florals shall not be liable for any indirect, incidental, special, or consequential damages arising from the use of our products or services.</p>
                    
                    <h4 class="mt-4 mb-3">9. Changes to Terms</h4>
                    <p>We reserve the right to modify these terms at any time. Changes will be effective immediately upon posting on the website.</p>
                    
                    <h4 class="mt-4 mb-3">10. Contact Information</h4>
                    <p>
                        For any questions regarding these terms, please contact us:<br>
                        Email: {{ config('contact.email_info') }}<br>
                        Phone: {{ config('contact.phone') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
