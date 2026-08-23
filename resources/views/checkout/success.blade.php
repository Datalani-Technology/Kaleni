@extends('layouts.app')

@section('title', 'Order Success - /Namsa Florals')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="card">
                <div class="card-body py-5">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
                    <h1 class="mt-3">Order Placed Successfully!</h1>
                    <p class="lead">Thank you for your order. We'll process it shortly.</p>
                    
                    <div class="alert alert-info mt-4">
                        <strong>Order Number:</strong> {{ $order->order_number }}<br>
                        <strong>Total Amount:</strong> N$ {{ number_format($order->total_amount, 2) }}<br>
                        <strong>Payment Method:</strong> {{ strtoupper($order->payment_method) }}<br>
                        <strong>Status:</strong> {{ ucfirst($order->order_status) }}
                    </div>

                    <p>You will receive a confirmation email shortly.</p>

                    <div class="d-flex flex-wrap justify-content-center gap-2 mb-3">
                        <a href="{{ route('checkout.receipt', $order->order_number) }}" target="_blank" class="buy-now-btn" style="background: #d63384; color: white; max-width: 250px; display: inline-block; text-decoration: none;">
                            <i class="bi bi-receipt"></i> View / Print Receipt
                        </a>
                    </div>

                    <a href="{{ route('products.index') }}" class="buy-now-btn" style="background: #333; color: white; max-width: 250px; margin: 0 auto; display: block; text-decoration: none;">
                        Continue Shopping
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
