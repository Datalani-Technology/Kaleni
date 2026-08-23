@extends('admin.layout')

@section('title', 'Edit Promo Code')
@section('sidebar_active', 'promo-codes')

@section('content')
<h1 class="mb-4">Edit promo code</h1>

<div class="card" style="max-width: 760px;">
    <div class="card-body">
        <form action="{{ route('admin.promo-codes.update', $promoCode) }}" method="POST">
            @csrf
            @method('PUT')
            @include('admin.promo-codes._form')
        </form>
    </div>
</div>
@endsection
