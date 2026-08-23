@extends('admin.layout')

@section('title', 'Set Up Two-Factor Authentication')
@section('sidebar_active', 'security')

@section('content')
<h1 class="mb-4">Set Up Two-Factor Authentication</h1>

<div class="alert alert-warning">
    <i class="bi bi-shield-exclamation"></i>
    Two-factor authentication is required for every admin account. You won't be able to
    use the rest of the panel until this is confirmed.
</div>

@if($errors->any())
    <div class="alert alert-danger">
        @foreach($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<div class="row g-4">
    <div class="col-12 col-lg-6">
        <div class="card h-100">
            <div class="card-body">
                <h2 class="h5 mb-3">1. Scan this QR code</h2>
                <p class="text-muted small">Use Google Authenticator, Microsoft Authenticator, Authy, or any
                    TOTP-compatible app.</p>
                <div class="bg-white border rounded p-3 d-inline-block">
                    {!! $qrSvg !!}
                </div>
                <p class="text-muted small mt-3 mb-1">Can't scan? Enter this key manually:</p>
                <code class="user-select-all d-inline-block p-2 bg-light rounded">{{ $secret }}</code>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-6">
        <div class="card h-100">
            <div class="card-body">
                <h2 class="h5 mb-3">2. Enter the 6-digit code</h2>
                <form action="{{ route('admin.2fa.enable') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="code" class="form-label">Authentication code</label>
                        <input type="text" inputmode="numeric" autocomplete="one-time-code" class="form-control @error('code') is-invalid @enderror" id="code" name="code" maxlength="6" required autofocus>
                        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Confirm & Enable</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
