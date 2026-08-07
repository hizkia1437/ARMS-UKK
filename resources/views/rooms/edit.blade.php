@extends('layouts.app')

@section('title', 'Edit Room')

@section('content')
<div class="mb-4">
    <a href="{{ route('rooms.index') }}" class="text-decoration-none text-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Rooms
    </a>
    <h3 class="fw-bold mt-2 mb-1">Edit Room</h3>
    <p class="text-muted mb-0">Update details for {{ $room->room_code }} - {{ $room->name }}.</p>
</div>

<div class="row">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="card card-custom p-4">
            <form method="POST" action="{{ route('rooms.update', $room) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="room_code" class="form-label fw-medium">Room Code <span class="text-danger">*</span></label>
                    <input type="text" name="room_code" id="room_code" value="{{ old('room_code', $room->room_code) }}" class="form-control @error('room_code') is-invalid @enderror" required>
                    @error('room_code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="name" class="form-label fw-medium">Room Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $room->name) }}" class="form-control @error('name') is-invalid @enderror" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="capacity" class="form-label fw-medium">Capacity (Persons) <span class="text-danger">*</span></label>
                    <input type="number" name="capacity" id="capacity" value="{{ old('capacity', $room->capacity) }}" min="1" class="form-control @error('capacity') is-invalid @enderror" required>
                    @error('capacity')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="status" class="form-label fw-medium">Status <span class="text-danger">*</span></label>
                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="Available" {{ old('status', $room->status) === 'Available' ? 'selected' : '' }}>Available</option>
                        <option value="Occupied" {{ old('status', $room->status) === 'Occupied' ? 'selected' : '' }}>Occupied</option>
                        <option value="Under Maintenance" {{ old('status', $room->status) === 'Under Maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex align-items-center gap-2">
                    <button type="submit" class="btn btn-primary rounded-2 px-4">
                        <i class="bi bi-save me-1"></i> Update Room
                    </button>
                    <a href="{{ route('rooms.index') }}" class="btn btn-light rounded-2 px-3">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
