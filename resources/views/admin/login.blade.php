<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#1a1a1a">
    <title>Admin Login - /Namsa Florals</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 50%, #1a1a1a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        }
        .login-logo { max-height: 64px; width: auto; max-width: 180px; object-fit: contain; }
        .btn-admin { background: #1a1a1a; color: #fff; border: none; }
        .btn-admin:hover { background: #333 !important; color: #fff !important; }
    </style>
</head>
<body>
    @php
        $loginLogoPath = \App\Models\Setting::get('logo_path');
        $loginLogoText = \App\Models\Setting::get('logo_text', '/Namsa Florals');
    @endphp
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card login-card">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            @if($loginLogoPath)
                                <img src="{{ asset('storage/' . $loginLogoPath) }}" alt="{{ $loginLogoText }}" class="login-logo d-block mx-auto">
                            @else
                                <i class="bi bi-flower1" style="font-size: 3rem; color: #333;"></i>
                            @endif
                            <h2 class="mt-3" style="color: #111;">{{ $loginLogoText }}</h2>
                            <p class="text-muted">Admin Login</p>
                        </div>

                        @if(session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <form method="POST" action="{{ route('admin.login') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autofocus>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="mt-1">
                                    <a href="{{ route('admin.forgot-password') }}" class="small text-decoration-none">Forgot password?</a>
                                </div>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">Remember me</label>
                            </div>
                            @if(session('status'))
                                <div class="alert alert-success mb-3">{{ session('status') }}</div>
                            @endif
                            <button type="submit" class="btn btn-admin w-100 btn-lg">
                                <i class="bi bi-box-arrow-in-right"></i> Login
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
