@extends('admin.layout')

@section('title', 'Security')
@section('sidebar_active', 'security')

@section('content')
<h1 class="mb-4">Security</h1>

<div class="card mb-4" style="max-width: 640px;">
    <div class="card-body">
        <h2 class="h5 mb-3"><i class="bi bi-shield-lock"></i> Two-Factor Authentication</h2>
        <p class="mb-1">
            Status:
            <span class="badge bg-success"><i class="bi bi-check-circle"></i> Enabled</span>
        </p>
        <p class="text-muted small mb-4">Confirmed on {{ $user->two_factor_confirmed_at->format('d M Y, H:i') }}.</p>

        <hr>

        <h3 class="h6 mt-4">Regenerate recovery codes</h3>
        <p class="text-muted small">Your old codes stop working immediately. Requires your current password.</p>
        <form action="{{ route('admin.2fa.regenerate-codes') }}" method="POST" class="row g-2 align-items-end" style="max-width: 420px;" data-confirm-title="Regenerate recovery codes?" data-confirm-message="Your existing recovery codes will stop working immediately." data-confirm-label="Regenerate codes" data-confirm-variant="warning">
            @csrf
            <div class="col">
                <label for="regen_password" class="form-label">Current password</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="regen_password" name="password" required>
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-primary"><i class="bi bi-arrow-repeat"></i> Regenerate</button>
            </div>
        </form>

        <hr class="my-4">

        <h3 class="h6 text-danger">Reset two-factor authentication</h3>
        <p class="text-muted small">Clears your current authenticator and recovery codes. You'll be required to set
            up two-factor authentication again immediately, with a new device if needed.</p>
        <form action="{{ route('admin.2fa.reset') }}" method="POST" class="row g-2 align-items-end" style="max-width: 420px;" data-confirm-title="Reset two-factor authentication?" data-confirm-message="Your authenticator and recovery codes will be cleared. You will need to set up two-factor authentication again immediately." data-confirm-label="Reset 2FA" data-confirm-variant="danger">
            @csrf
            <div class="col">
                <label for="reset_password" class="form-label">Current password</label>
                <input type="password" class="form-control" id="reset_password" name="password" required>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-danger"><i class="bi bi-arrow-counterclockwise"></i> Reset</button>
            </div>
        </form>
    </div>
</div>
@endsection
