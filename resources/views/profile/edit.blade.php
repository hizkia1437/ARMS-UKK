@extends('layouts.app')

@section('title', 'Profile')

@section('content')
<div class="mb-4">
    <h3 class="fw-bold mb-1">User Profile</h3>
    <p class="text-muted mb-0">Manage your personal account settings and password.</p>
</div>

<div class="row g-4">
    <div class="col-12 col-lg-6">
        <div class="card card-custom p-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-person-badge text-primary me-2"></i>Profile Information</h5>
            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <div class="mb-4 text-center">
                    <img src="{{ auth()->user()->avatar_url }}" alt="Profile Avatar" class="rounded-circle object-fit-cover shadow-sm mb-2 border border-3 border-primary-subtle" style="width: 100px; height: 100px;">
                    <div>
                        <label for="avatar" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                            <i class="bi bi-camera me-1"></i> Change Photo
                        </label>
                        <input type="file" name="avatar" id="avatar" class="d-none @error('avatar') is-invalid @enderror" accept="image/png, image/jpeg, image/jpg, image/webp" onchange="this.form.submit()">
                    </div>
                    @error('avatar')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                    <div class="text-muted small mt-1">PNG, JPG, WEBP up to 2MB</div>
                </div>

                <div class="mb-3">
                    <label for="name" class="form-label fw-medium">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', auth()->user()->name) }}" class="form-control @error('name') is-invalid @enderror" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label fw-medium">Email Address <span class="text-danger">*</span></label>
                    <input type="email" name="email" id="email" value="{{ old('email', auth()->user()->email) }}" class="form-control @error('email') is-invalid @enderror" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-medium">Role</label>
                    <input type="text" value="{{ auth()->user()->role }}" class="form-control bg-light" readonly>
                </div>

                <button type="submit" class="btn btn-primary rounded-2 px-4">
                    <i class="bi bi-save me-1"></i> Save Changes
                </button>
            </form>
        </div>
    </div>

    <div class="col-12 col-lg-6">
        <div class="card card-custom p-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-key-fill text-warning me-2"></i>Update Password</h5>
            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="current_password" class="form-label fw-medium">Current Password</label>
                    <input type="password" name="current_password" id="current_password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror">
                    @error('current_password', 'updatePassword')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label fw-medium">New Password</label>
                    <input type="password" name="password" id="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror">
                    @error('password', 'updatePassword')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password_confirmation" class="form-label fw-medium">Confirm New Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                </div>

                <button type="submit" class="btn btn-warning text-dark rounded-2 px-4">
                    <i class="bi bi-shield-lock me-1"></i> Update Password
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
