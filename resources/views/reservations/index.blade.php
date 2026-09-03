@extends('layouts.app')

@section('title', 'Reservations')

@section('content')
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
    <div>
        <h3 class="fw-bold mb-1">Room Reservations</h3>
        <p class="text-muted mb-0">View and manage room booking requests.</p>
    </div>
    <div>
        <a href="{{ route('reservations.create') }}" class="btn btn-primary rounded-2 d-inline-flex align-items-center gap-2">
            <i class="bi bi-plus-circle"></i> Create Reservation
        </a>
    </div>
</div>

<div class="card card-custom p-4">
    <!-- Search, Filter & Sort Form -->
    <form method="GET" action="{{ route('reservations.index') }}" class="mb-4">
        <div class="row g-2 align-items-center">
            <div class="col-12 col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control border-start-0 ps-0" placeholder="Search reservations...">
                </div>
            </div>
            <div class="col-6 col-md-2">
                <select name="status" class="form-select text-secondary" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="Pending" {{ request('status') === 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Approved" {{ request('status') === 'Approved' ? 'selected' : '' }}>Approved</option>
                    <option value="Rejected" {{ request('status') === 'Rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <select name="room_id" class="form-select text-secondary" onchange="this.form.submit()">
                    <option value="">All Rooms</option>
                    @foreach($rooms as $rm)
                        <option value="{{ $rm->id }}" {{ request('room_id') == $rm->id ? 'selected' : '' }}>{{ $rm->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <select name="sort_by" class="form-select text-secondary" onchange="this.form.submit()">
                    <option value="reservation_date" {{ request('sort_by', 'reservation_date') === 'reservation_date' ? 'selected' : '' }}>Sort by: Date</option>
                    <option value="reservation_code" {{ request('sort_by') === 'reservation_code' ? 'selected' : '' }}>Sort by: Code</option>
                    <option value="start_time" {{ request('sort_by') === 'start_time' ? 'selected' : '' }}>Sort by: Time</option>
                    <option value="status" {{ request('sort_by') === 'status' ? 'selected' : '' }}>Sort by: Status</option>
                </select>
            </div>
            <div class="col-6 col-md-2 d-flex gap-1">
                <select name="sort_dir" class="form-select text-secondary" onchange="this.form.submit()">
                    <option value="desc" {{ request('sort_dir', 'desc') === 'desc' ? 'selected' : '' }}>DESC ⬇️</option>
                    <option value="asc" {{ request('sort_dir') === 'asc' ? 'selected' : '' }}>ASC ⬆️</option>
                </select>
                @if(request('search') || request('status') || request('room_id') || request('sort_by') || request('sort_dir'))
                    <a href="{{ route('reservations.index') }}" class="btn btn-outline-secondary rounded-2" title="Reset Filters"><i class="bi bi-x-circle"></i></a>
                @endif
            </div>
        </div>
    </form>

    <!-- Table -->
    <div class="table-responsive">
        <table class="table table-custom table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>User</th>
                    <th>Room</th>
                    <th>Date</th>
                    <th>Time Slot</th>
                    <th>Purpose</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reservations as $reservation)
                <tr>
                    <td class="fw-semibold text-primary">{{ $reservation->reservation_code }}</td>
                    <td>{{ $reservation->user->name ?? '-' }}</td>
                    <td class="fw-medium text-dark">{{ $reservation->room->name ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($reservation->reservation_date)->format('d M Y') }}</td>
                    <td>{{ substr($reservation->start_time, 0, 5) }} - {{ substr($reservation->end_time, 0, 5) }}</td>
                    <td><span class="text-truncate d-inline-block" style="max-width: 200px;">{{ $reservation->purpose }}</span></td>
                    <td>
                        @if($reservation->status === 'Approved')
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1">Approved</span>
                        @elseif($reservation->status === 'Pending')
                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-3 py-1">Pending</span>
                        @else
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-1">Rejected</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <!-- Admin Approve / Reject quick actions -->
                        @if(auth()->user()->isAdmin() && $reservation->status === 'Pending')
                        <form method="POST" action="{{ route('reservations.update-status', $reservation) }}" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="Approved">
                            <button type="submit" class="btn btn-sm btn-success rounded-2 me-1" title="Approve Reservation">
                                <i class="bi bi-check-circle"></i> Approve
                            </button>
                        </form>
                        <form method="POST" action="{{ route('reservations.update-status', $reservation) }}" class="d-inline me-1">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="Rejected">
                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-2 me-1" title="Reject Reservation">
                                <i class="bi bi-x-circle"></i> Reject
                            </button>
                        </form>
                        @endif

                        @if(auth()->user()->isAdmin() || auth()->id() === $reservation->user_id)
                        <a href="{{ route('reservations.edit', $reservation) }}" class="btn btn-sm btn-outline-primary me-1 rounded-2">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-2" data-bs-toggle="modal" data-bs-target="#deleteResModal{{ $reservation->id }}">
                            <i class="bi bi-trash"></i> Delete
                        </button>

                        <!-- Delete Modal -->
                        <div class="modal fade text-start" id="deleteResModal{{ $reservation->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow">
                                    <div class="modal-header border-0 pb-0">
                                        <h5 class="modal-title fw-bold">Confirm Delete</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body py-3">
                                        Are you sure you want to delete reservation <strong>{{ $reservation->reservation_code }}</strong> for {{ $reservation->room->name ?? 'Room' }}?
                                    </div>
                                    <div class="modal-footer border-0 pt-0">
                                        <button type="button" class="btn btn-light rounded-2" data-bs-dismiss="modal">Cancel</button>
                                        <form method="POST" action="{{ route('reservations.destroy', $reservation) }}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger rounded-2">Delete Reservation</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">No reservations found matching your search.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4 d-flex justify-content-end">
        {{ $reservations->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
