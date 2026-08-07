@extends('layouts.app')

@section('title', 'Edit Reservation')

@section('content')
<div class="mb-4">
    <a href="{{ route('reservations.index') }}" class="text-decoration-none text-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Reservations
    </a>
    <h3 class="fw-bold mt-2 mb-1">Edit Reservation</h3>
    <p class="text-muted mb-0">Update reservation {{ $reservation->reservation_code }}.</p>
</div>

<div class="row">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="card card-custom p-4">
            <form method="POST" action="{{ route('reservations.update', $reservation) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="reservation_code" class="form-label fw-medium">Reservation Code</label>
                    <input type="text" name="reservation_code" id="reservation_code" value="{{ $reservation->reservation_code }}" class="form-control bg-light" readonly>
                </div>

                <div class="mb-3">
                    <label for="room_id" class="form-label fw-medium">Select Room <span class="text-danger">*</span></label>
                    <select name="room_id" id="room_id" class="form-select @error('room_id') is-invalid @enderror" required>
                        @foreach($rooms as $room)
                            <option value="{{ $room->id }}" {{ old('room_id', $reservation->room_id) == $room->id ? 'selected' : '' }}>
                                {{ $room->name }} (Capacity: {{ $room->capacity }} Persons)
                            </option>
                        @endforeach
                    </select>
                    @error('room_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="reservation_date" class="form-label fw-medium">Reservation Date <span class="text-danger">*</span></label>
                    <input type="date" name="reservation_date" id="reservation_date" value="{{ old('reservation_date', $reservation->reservation_date) }}" class="form-control @error('reservation_date') is-invalid @enderror" required>
                    @error('reservation_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label for="start_time" class="form-label fw-medium">Start Time <span class="text-danger">*</span></label>
                        <input type="time" name="start_time" id="start_time" value="{{ old('start_time', substr($reservation->start_time, 0, 5)) }}" class="form-control @error('start_time') is-invalid @enderror" required>
                        @error('start_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-6">
                        <label for="end_time" class="form-label fw-medium">End Time <span class="text-danger">*</span></label>
                        <input type="time" name="end_time" id="end_time" value="{{ old('end_time', substr($reservation->end_time, 0, 5)) }}" class="form-control @error('end_time') is-invalid @enderror" required>
                        @error('end_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="purpose" class="form-label fw-medium">Purpose <span class="text-danger">*</span></label>
                    <textarea name="purpose" id="purpose" rows="3" class="form-control @error('purpose') is-invalid @enderror" required>{{ old('purpose', $reservation->purpose) }}</textarea>
                    @error('purpose')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                @if(auth()->user()->isAdmin())
                <div class="mb-4">
                    <label for="status" class="form-label fw-medium">Status <span class="text-danger">*</span></label>
                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="Pending" {{ old('status', $reservation->status) === 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Approved" {{ old('status', $reservation->status) === 'Approved' ? 'selected' : '' }}>Approved</option>
                        <option value="Rejected" {{ old('status', $reservation->status) === 'Rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                @endif

                <div class="d-flex align-items-center gap-2">
                    <button type="submit" class="btn btn-primary rounded-2 px-4">
                        <i class="bi bi-save me-1"></i> Update Reservation
                    </button>
                    <a href="{{ route('reservations.index') }}" class="btn btn-light rounded-2 px-3">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
