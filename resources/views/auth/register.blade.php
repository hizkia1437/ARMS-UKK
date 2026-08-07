<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - ARMS System</title>
    
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
            max-width: 480px;
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
            <i class="bi bi-person-plus-fill fs-4 text-white"></i>
        </div>
        <h4 class="fw-bold mb-1">Create Account</h4>
        <p class="text-slate-300 small mb-0 opacity-75">Register as a user in ARMS System</p>
    </div>

    <div class="p-4">
        @include('components.alert')

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label fw-medium">Full Name</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" placeholder="Enter full name" required autofocus>
                @error('name')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label fw-medium">Email Address</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" placeholder="name@example.com" required>
                @error('email')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label fw-medium">Password</label>
                <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="At least 8 characters" required>
                @error('password')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="form-label fw-medium">Confirm Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Re-enter password" required>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold rounded-2 mb-3">
                <i class="bi bi-check-circle me-1"></i> Register Account
            </button>

            <div class="text-center small text-muted">
                Already registered? <a href="{{ route('login') }}" class="text-primary text-decoration-none fw-semibold">Sign in here</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>
