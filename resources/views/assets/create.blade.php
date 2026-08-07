@extends('layouts.app')

@section('title', 'Create Asset')

@section('content')
<div class="mb-4">
    <a href="{{ route('assets.index') }}" class="text-decoration-none text-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Assets
    </a>
    <h3 class="fw-bold mt-2 mb-1">Add New Asset</h3>
    <p class="text-muted mb-0">Register a new asset into the inventory.</p>
</div>

<div class="row">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="card card-custom p-4">
            <form method="POST" action="{{ route('assets.store') }}">
                @csrf

                <div class="mb-3">
                    <label for="asset_code" class="form-label fw-medium">Asset Code <span class="text-danger">*</span></label>
                    <input type="text" name="asset_code" id="asset_code" value="{{ old('asset_code', $nextCode) }}" class="form-control @error('asset_code') is-invalid @enderror" required>
                    @error('asset_code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="name" class="form-label fw-medium">Asset Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" placeholder="e.g. Epson EB-X400 Projector" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="category" class="form-label fw-medium">Category <span class="text-danger">*</span></label>
                    <input type="text" name="category" id="category" value="{{ old('category') }}" class="form-control @error('category') is-invalid @enderror" placeholder="e.g. Electronics, Audio, Computer" required>
                    @error('category')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="condition" class="form-label fw-medium">Condition <span class="text-danger">*</span></label>
                    <select name="condition" id="condition" class="form-select @error('condition') is-invalid @enderror" required>
                        <option value="Good" {{ old('condition') === 'Good' ? 'selected' : '' }}>Good</option>
                        <option value="Needs Repair" {{ old('condition') === 'Needs Repair' ? 'selected' : '' }}>Needs Repair</option>
                        <option value="Damaged" {{ old('condition') === 'Damaged' ? 'selected' : '' }}>Damaged</option>
                    </select>
                    @error('condition')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="location" class="form-label fw-medium">Location <span class="text-danger">*</span></label>
                    <input type="text" name="location" id="location" value="{{ old('location') }}" class="form-control @error('location') is-invalid @enderror" placeholder="e.g. Audio Visual Room, Server Room" required>
                    @error('location')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex align-items-center gap-2">
                    <button type="submit" class="btn btn-primary rounded-2 px-4">
                        <i class="bi bi-save me-1"></i> Save Asset
                    </button>
                    <a href="{{ route('assets.index') }}" class="btn btn-light rounded-2 px-3">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
