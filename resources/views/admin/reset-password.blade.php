<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#fdf1f2">
    <title>Set new password - Kaleni Catering Services Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.css') }}">
    <style>
        body { background: linear-gradient(160deg, #fffaf6 0%, #fdf1f2 45%, #f6f4ec 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card { border-radius: 22px; box-shadow: 0 26px 64px rgba(104,11,28,0.18); }
        .btn-admin { background: linear-gradient(135deg, #8a1424, #680B1C); color: #fff; border: none; }
        .btn-admin:hover { background: linear-gradient(135deg, #680B1C, #4A0814) !important; color: #fff !important; }
    </style>
    <link rel="stylesheet" href="{{ asset('css/admin-auth.css') }}">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <i class="bi bi-shield-lock" style="font-size: 2.5rem; color: #680B1C;"></i>
                            <h2 class="mt-3">Set new password</h2>
                            <p class="text-muted">Enter your new password below.</p>
                        </div>
                        @if($errors->any())
                            <div class="alert alert-danger">{{ $errors->first() }}</div>
                        @endif
                        <form method="POST" action="{{ route('admin.reset-password.store') }}">
                            @csrf
                            <input type="hidden" name="email" value="{{ $email }}">
                            <input type="hidden" name="token" value="{{ $token }}">
                            <div class="mb-3">
                                <label for="password" class="form-label">New password</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required autofocus>
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <small class="text-muted">At least 8 characters.</small>
                            </div>
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirm password</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                            </div>
                            <button type="submit" class="btn btn-admin w-100 btn-lg"><i class="bi bi-check-lg"></i> Update password</button>
                        </form>
                        <p class="text-center mt-3 mb-0">
                            <a href="{{ route('admin.login') }}" class="text-decoration-none"><i class="bi bi-arrow-left"></i> Back to login</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
