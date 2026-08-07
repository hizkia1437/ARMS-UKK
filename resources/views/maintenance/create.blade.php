@extends('layouts.app')

@section('title', 'Create Maintenance Report')

@section('content')
<div class="mb-4">
    @if(request('from') === 'assets')
        <a href="{{ route('assets.index') }}" class="text-decoration-none text-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Assets
        </a>
    @elseif(request('from') === 'rooms')
        <a href="{{ route('rooms.index') }}" class="text-decoration-none text-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Rooms
        </a>
    @else
        <a href="{{ route('maintenance.index') }}" class="text-decoration-none text-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Maintenance Reports
        </a>
    @endif
    <h3 class="fw-bold mt-2 mb-1">Create Maintenance Report</h3>
    <p class="text-muted mb-0">Report an issue for an asset hardware or a room facility.</p>
</div>

<div class="row">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="card card-custom p-4">
            <form method="POST" action="{{ route('maintenance.store') }}">
                @csrf

                <div class="mb-3">
                    <label for="report_code" class="form-label fw-medium">Report Code</label>
                    <input type="text" name="report_code" id="report_code" value="{{ old('report_code', $nextCode) }}" class="form-control bg-light" readonly>
                </div>

                @if(request('from') === 'rooms' || request('room_id'))
                <!-- Room Selector -->
                <div class="mb-3">
                    <label for="room_id" class="form-label fw-medium">Target Room Facility <span class="text-danger">*</span></label>
                    <select name="room_id" id="room_id" class="form-select @error('room_id') is-invalid @enderror" required>
                        <option value="">Choose a room facility...</option>
                        @foreach($rooms as $room)
                            <option value="{{ $room->id }}" {{ old('room_id', request('room_id')) == $room->id ? 'selected' : '' }}>
                                {{ $room->room_code }} - {{ $room->name }} (Capacity: {{ $room->capacity }} Persons)
                            </option>
                        @endforeach
                    </select>
                    @error('room_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                @else
                <!-- Asset Selector -->
                <div class="mb-3">
                    <label for="asset_id" class="form-label fw-medium">Target Asset Equipment <span class="text-danger">*</span></label>
                    <select name="asset_id" id="asset_id" class="form-select @error('asset_id') is-invalid @enderror">
                        <option value="">Choose an asset equipment...</option>
                        @foreach($assets as $asset)
                            <option value="{{ $asset->id }}" {{ old('asset_id', request('asset_id')) == $asset->id ? 'selected' : '' }}>
                                {{ $asset->asset_code }} - {{ $asset->name }} ({{ $asset->location }})
                            </option>
                        @endforeach
                    </select>
                    @error('asset_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Optional Room selector if creating from general maintenance page -->
                <div class="mb-3">
                    <label for="room_id" class="form-label fw-medium">Or Select Room Facility <span class="text-muted">(Optional if reporting room)</span></label>
                    <select name="room_id" id="room_id" class="form-select @error('room_id') is-invalid @enderror">
                        <option value="">None (Asset only)</option>
                        @foreach($rooms as $room)
                            <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                {{ $room->room_code }} - {{ $room->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('room_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                @endif

                <div class="mb-4">
                    <label for="description" class="form-label fw-medium">Problem Description <span class="text-danger">*</span></label>
                    <textarea name="description" id="description" rows="4" class="form-control @error('description') is-invalid @enderror" placeholder="Provide detailed description of the issue, damage, or facility defect..." required>{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex align-items-center gap-2">
                    <button type="submit" class="btn btn-primary rounded-2 px-4">
                        <i class="bi bi-send me-1"></i> Submit Report
                    </button>
                    <a href="{{ request('from') === 'assets' ? route('assets.index') : (request('from') === 'rooms' ? route('rooms.index') : route('maintenance.index')) }}" class="btn btn-light rounded-2 px-3">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
