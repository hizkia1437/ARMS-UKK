@extends('layouts.app')

@section('title', 'Edit Maintenance Report')

@section('content')
<div class="mb-4">
    <a href="{{ route('maintenance.index') }}" class="text-decoration-none text-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Maintenance Reports
    </a>
    <h3 class="fw-bold mt-2 mb-1">Edit Maintenance Report</h3>
    <p class="text-muted mb-0">Update report {{ $maintenance->report_code }}.</p>
</div>

<div class="row">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="card card-custom p-4">
            <form method="POST" action="{{ route('maintenance.update', $maintenance) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="report_code" class="form-label fw-medium">Report Code</label>
                    <input type="text" name="report_code" id="report_code" value="{{ $maintenance->report_code }}" class="form-control bg-light" readonly>
                </div>

                <div class="mb-3">
                    <label for="asset_id" class="form-label fw-medium">Select Asset <span class="text-danger">*</span></label>
                    <select name="asset_id" id="asset_id" class="form-select @error('asset_id') is-invalid @enderror" required>
                        @foreach($assets as $asset)
                            <option value="{{ $asset->id }}" {{ old('asset_id', $maintenance->asset_id) == $asset->id ? 'selected' : '' }}>
                                {{ $asset->asset_code }} - {{ $asset->name }} ({{ $asset->location }})
                            </option>
                        @endforeach
                    </select>
                    @error('asset_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label fw-medium">Problem Description <span class="text-danger">*</span></label>
                    <textarea name="description" id="description" rows="4" class="form-control @error('description') is-invalid @enderror" required>{{ old('description', $maintenance->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                @if(auth()->user()->isAdmin() || auth()->user()->isStaff())
                <div class="mb-4">
                    <label for="status" class="form-label fw-medium">Status <span class="text-danger">*</span></label>
                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="Pending" {{ old('status', $maintenance->status) === 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Completed" {{ old('status', $maintenance->status) === 'Completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                @endif

                <div class="d-flex align-items-center gap-2">
                    <button type="submit" class="btn btn-primary rounded-2 px-4">
                        <i class="bi bi-save me-1"></i> Update Report
                    </button>
                    <a href="{{ route('maintenance.index') }}" class="btn btn-light rounded-2 px-3">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
