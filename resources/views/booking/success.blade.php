@extends('layouts.app')

@php
    $isQuickOrder = $booking->order_type === \App\Models\Booking::ORDER_TYPE_QUICK_ORDER;
@endphp

@section('title', ($isQuickOrder ? 'Order' : 'Booking') . ' Confirmed - Kaleni Catering Services')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="card">
                <div class="card-body py-5">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
                    <h1 class="mt-3">{{ $isQuickOrder ? 'Order' : 'Booking' }} Confirmed!</h1>
                    <p class="lead">Thank you for {{ $isQuickOrder ? 'ordering from' : 'booking with' }} Kaleni Catering Services. We'll be in touch shortly.</p>

                    <div class="alert alert-info mt-4">
                        <strong>{{ $isQuickOrder ? 'Order' : 'Booking' }} Number:</strong> {{ $booking->booking_number }}<br>
                        @if($isQuickOrder)
                            <strong>Fulfillment:</strong> {{ ucfirst($booking->fulfillment_method ?: 'N/A') }}<br>
                        @else
                            <strong>Schedule:</strong> {{ $booking->schedule_summary }}<br>
                        @endif
                        <strong>Total Amount:</strong> {{ (float) $booking->total_amount > 0 ? 'N$ ' . number_format($booking->total_amount, 2) : 'To be confirmed' }}<br>
                        <strong>Payment Method:</strong> {{ $booking->payment_method_label }}<br>
                        <strong>Status:</strong> {{ ucfirst($booking->booking_status) }}
                    </div>

                    <p>You will receive a confirmation email shortly.</p>

                    <div class="d-flex flex-wrap justify-content-center gap-2 mb-3">
                        <a href="{{ route('booking.receipt', $booking->booking_number) }}" target="_blank" class="buy-now-btn" style="background: #680B1C; color: white; max-width: 250px; display: inline-block; text-decoration: none;">
                            <i class="bi bi-receipt"></i> View / Print Receipt
                        </a>
                    </div>

                    <a href="{{ route('menu.index') }}" class="buy-now-btn" style="background: #333; color: white; max-width: 250px; margin: 0 auto; display: block; text-decoration: none;">
                        Continue Browsing
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
