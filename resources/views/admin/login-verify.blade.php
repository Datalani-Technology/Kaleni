<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#fdf1f2">
    <title>Verify Identity - /Namsa Florals</title>
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
        .login-card { border-radius: 22px; box-shadow: 0 26px 64px rgba(178,66,116,0.18); }
        .btn-admin { background: linear-gradient(135deg, #c22a70, #b42363); color: #fff; border: none; }
        .btn-admin:hover { background: linear-gradient(135deg, #b42363, #8d174b) !important; color: #fff !important; }
    </style>
    <link rel="stylesheet" href="{{ asset('css/admin-auth.css') }}">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card login-card">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <i class="bi bi-shield-lock" style="font-size: 3rem; color: #b42363;"></i>
                            <h2 class="mt-3" style="color: #35222c;">Verify It's You</h2>
                            <p class="text-muted">Enter the 6-digit code from your authenticator app</p>
                        </div>

                        @if($errors->any())
                            <div class="alert alert-danger">
                                @foreach($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif

                        <form method="POST" action="{{ route('admin.2fa.challenge.verify') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="code" class="form-label">Authentication or recovery code</label>
                                <input type="text" inputmode="numeric" autocomplete="one-time-code" class="form-control @error('code') is-invalid @enderror" id="code" name="code" maxlength="20" required autofocus>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-admin w-100 btn-lg">
                                <i class="bi bi-box-arrow-in-right"></i> Verify
                            </button>
                        </form>
                        <p class="text-center text-muted small mt-3 mb-0">
                            Lost your device? Enter one of your saved recovery codes instead.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
