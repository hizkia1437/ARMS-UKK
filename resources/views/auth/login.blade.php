<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ARMS (Asset Reservation & Maintenance System)</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 & Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f6f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .auth-card {
            max-width: 440px;
            width: 100%;
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            background-color: #ffffff;
            overflow: hidden;
        }

        .auth-header {
            background-color: #1e293b;
            color: #ffffff;
            padding: 2rem 1.5rem;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="auth-card">
    <div class="auth-header">
        <div class="d-inline-flex align-items-center justify-content-center bg-primary rounded-circle mb-3" style="width: 48px; height: 48px;">
            <i class="bi bi-box-seam-fill fs-4 text-white"></i>
        </div>
        <h4 class="fw-bold mb-1">ARMS Application</h4>
        <p class="text-slate-300 small mb-0 opacity-75">Asset Reservation & Maintenance System</p>
    </div>

    <div class="p-4">
        @include('components.alert')

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label fw-medium">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-secondary"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" placeholder="name@arms.test" required autofocus>
                </div>
                @error('email')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label fw-medium">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-secondary"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="••••••••" required>
                </div>
                @error('password')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-check mb-4">
                <input type="checkbox" name="remember" id="remember_me" class="form-check-input">
                <label for="remember_me" class="form-check-label text-secondary small">Remember me on this device</label>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold rounded-2 mb-3">
                <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
            </button>

            @if (Route::has('register'))
            <div class="text-center small text-muted">
                Don't have an account? <a href="{{ route('register') }}" class="text-primary text-decoration-none fw-semibold">Register here</a>
            </div>
            @endif
        </form>
    </div>
</div>

</body>
</html>
