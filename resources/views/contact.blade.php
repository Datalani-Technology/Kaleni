@extends('layouts.app')

@php
    $seoPage = 'contact';
    $structuredData = [
        \App\Services\SeoService::generateStructuredData('organization'),
    ];
@endphp

@section('title', 'Contact Us - /Namsa Florals Flower Shop Namibia')

@push('styles')
<style>
    .social-btn {
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        border-radius: 25px;
        color: white;
        border: none;
        transition: transform 0.3s, filter 0.3s;
    }
    .social-btn:hover { color: white; transform: translateY(-2px); filter: brightness(1.1); }
    .social-btn.social-fb { background: #1877F2; }
    .social-btn.social-ig { background: #E4405F; }
    .social-btn.social-tt { background: #EE1D52; }
    .social-btn.social-wa { background: #25D366; }
    .contact-form-compact .form-label { margin-bottom: 0.25rem; font-size: 0.9rem; }
    .human-verify .form-label strong { font-weight: 600; }
    @media (max-width: 768px) {
        .contact-page .card-body { padding: 1.5rem !important; }
    }
    @media (max-width: 400px) {
        .contact-page h1 { font-size: 1.5rem; }
        .contact-page .card-body { padding: 1.25rem !important; }
        .contact-page h5 { font-size: 1rem; }
    }
</style>
@endpush

@section('content')
<div class="container my-5 contact-page">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h1 class="mb-4">Contact Us</h1>
            
            <div class="card">
                <div class="card-body p-5">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <h5 class="mb-3"><i class="bi bi-geo-alt-fill"></i> Address</h5>
                            <p>
                                {{ config('contact.address.name') }}<br>
                                {{ config('contact.address.city') }}, {{ config('contact.address.country') }}@if(config('contact.address.po_box'))<br>
                                P.O. Box {{ config('contact.address.po_box') }}@endif
                            </p>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <h5 class="mb-3"><i class="bi bi-telephone-fill"></i> Phone</h5>
                            <p>{{ config('contact.phone') }}</p>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <h5 class="mb-3"><i class="bi bi-envelope-fill"></i> Email</h5>
                            <p>
                                Info: {{ config('contact.email_info') }}<br>
                                Orders: {{ config('contact.email_orders') }}
                            </p>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <h5 class="mb-3"><i class="bi bi-clock-fill"></i> Business Hours</h5>
                            <p>
                                Monday - Friday: 8:00 AM - 6:00 PM<br>
                                Saturday: 9:00 AM - 4:00 PM<br>
                                Sunday: Closed
                            </p>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="mb-4">
                        <h5 class="mb-3"><i class="bi bi-share-fill"></i> Follow Us on Social Media</h5>
                        <div class="d-flex gap-3 flex-wrap contact-social-wrap">
                            <a href="{{ config('social.facebook') }}" target="_blank" class="social-btn social-fb">
                                <i class="bi bi-facebook"></i> Facebook
                            </a>
                            <a href="https://www.instagram.com/namsa.florals" target="_blank" class="social-btn social-ig">
                                <i class="bi bi-instagram"></i> Instagram
                            </a>
                            <a href="{{ config('social.tiktok') }}" target="_blank" class="social-btn social-tt">
                                <i class="bi bi-tiktok"></i> TikTok
                            </a>
                            <a href="{{ config('social.whatsapp') }}" target="_blank" class="social-btn social-wa">
                                <i class="bi bi-whatsapp"></i> WhatsApp
                            </a>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <h5 class="mb-3">Send us a Message</h5>
                    
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('contact.store') }}" method="POST" id="contactForm" class="contact-form-compact">
                        @csrf
                        
                        <div style="position: absolute; left: -9999px; opacity: 0; pointer-events: none;" aria-hidden="true">
                            <label for="website">Website (leave blank)</label>
                            <input type="text" name="website" id="website" tabindex="-1" autocomplete="off">
                        </div>

                        <div class="row g-2 mb-2">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Your Name *</label>
                                <input type="text" class="form-control form-control-sm @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" 
                                       required minlength="2" maxlength="255">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Your Email *</label>
                                <input type="email" class="form-control form-control-sm @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email') }}" 
                                       required maxlength="255">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-2">
                            <label for="subject" class="form-label">Subject *</label>
                            <input type="text" class="form-control form-control-sm @error('subject') is-invalid @enderror" 
                                   id="subject" name="subject" value="{{ old('subject') }}" 
                                   required minlength="3" maxlength="255">
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-2">
                            <label for="message" class="form-label">Message *</label>
                            <textarea class="form-control form-control-sm @error('message') is-invalid @enderror" 
                                      id="message" name="message" rows="3" 
                                      required minlength="10" maxlength="5000" placeholder="Your message (10–5000 characters)">{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            @error('rate_limit')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3 human-verify">
                            <label for="human_check" class="form-label">Verify you're human: <strong>What is {{ $mathA }} + {{ $mathB }}?</strong></label>
                            <input type="number" class="form-control form-control-sm w-auto @error('human_check') is-invalid @enderror" 
                                   id="human_check" name="human_check" value="{{ old('human_check') }}" 
                                   required placeholder="Answer" inputmode="numeric" style="max-width: 100px;">
                            @error('human_check')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <button type="submit" class="buy-now-btn btn-sm" style="background: #333; color: white; padding: 10px 24px;" id="submitBtn">
                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            Send Message
                        </button>
                    </form>

                    <script>
                        // Prevent double submission
                        document.getElementById('contactForm').addEventListener('submit', function(e) {
                            const btn = document.getElementById('submitBtn');
                            const spinner = btn.querySelector('.spinner-border');
                            btn.disabled = true;
                            spinner.classList.remove('d-none');
                            btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...';
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
