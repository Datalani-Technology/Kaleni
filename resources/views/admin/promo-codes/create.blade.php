@extends('admin.layout')

@section('title', 'New Promo Code')
@section('sidebar_active', 'promo-codes')

@section('content')
<h1 class="mb-4">New promo code</h1>

<div class="card" style="max-width: 760px;">
    <div class="card-body">
        <form action="{{ route('admin.promo-codes.store') }}" method="POST">
            @csrf
            @include('admin.promo-codes._form')
        </form>
    </div>
</div>
@endsection
