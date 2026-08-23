<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#fdf1f2">
    <title>Admin Login - /Namsa Florals</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.css') }}">
    <style>
        body {
            font-family: 'Manrope', 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(160deg, #fffaf6 0%, #fdf1f2 45%, #f6f4ec 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        h1, h2, h3, h4, h5, h6 { font-family: 'Manrope', 'Segoe UI', Arial, sans-serif; }
        .login-card {
            border-radius: 22px;
            box-shadow: 0 26px 64px rgba(178,66,116,0.18);
        }
        .login-logo { max-height: 64px; width: auto; max-width: 180px; object-fit: contain; }
        .btn-admin { background: linear-gradient(135deg, #c22a70, #b42363); color: #fff; border: none; }
        .btn-admin:hover { background: linear-gradient(135deg, #b42363, #8d174b) !important; color: #fff !important; }
        a { color: #b42363; }
    </style>
    <link rel="stylesheet" href="{{ asset('css/admin-auth.css') }}">
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
                                <i class="bi bi-flower1" style="font-size: 3rem; color: #b42363;"></i>
                            @endif
                            <h2 class="mt-3" style="color: #35222c;">{{ $loginLogoText }}</h2>
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
    <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
