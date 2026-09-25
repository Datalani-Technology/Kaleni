<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#fdf1f2">
    <title>Forgot Password - Kaleni Catering Services Admin</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
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
                            <i class="bi bi-key" style="font-size: 2.5rem; color: #680B1C;"></i>
                            <h2 class="mt-3">Forgot password?</h2>
                            <p class="text-muted">Enter your admin email and we’ll send a reset link.</p>
                        </div>
                        @if(session('status'))
                            <div class="alert alert-success">{{ session('status') }}</div>
                        @endif
                        @if(session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif
                        @if($errors->any())
                            <div class="alert alert-danger">{{ $errors->first() }}</div>
                        @endif
                        <form method="POST" action="{{ route('admin.forgot-password.send') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label">Email address</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autofocus>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <button type="submit" class="btn btn-admin w-100 btn-lg"><i class="bi bi-envelope-fill"></i> Send reset link</button>
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
