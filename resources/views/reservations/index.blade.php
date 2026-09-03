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

                        <!-- Discussion Button -->
                        <button type="button" class="btn btn-sm btn-outline-info rounded-2 me-1" data-bs-toggle="modal" data-bs-target="#commentsResModal{{ $reservation->id }}" title="Discussion / Comments">
                            <i class="bi bi-chat-dots-fill"></i> Discuss
                            @if($reservation->comments->count() > 0)
                                <span class="badge rounded-pill bg-info text-dark ms-1">{{ $reservation->comments->count() }}</span>
                            @endif
                        </button>

                        @if(auth()->user()->isAdmin() || auth()->id() === $reservation->user_id)
                        <a href="{{ route('reservations.edit', $reservation) }}" class="btn btn-sm btn-outline-primary me-1 rounded-2">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-2" data-bs-toggle="modal" data-bs-target="#deleteResModal{{ $reservation->id }}">
                            <i class="bi bi-trash"></i> Delete
                        </button>

                        <!-- Discussion Modal -->
                        <div class="modal fade text-start" id="commentsResModal{{ $reservation->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content border-0 shadow">
                                    <div class="modal-header border-bottom bg-light">
                                        <h5 class="modal-title fw-bold">
                                            <i class="bi bi-chat-left-text-fill text-primary me-2"></i>Discussion — {{ $reservation->reservation_code }}
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-4" style="max-height: 450px; overflow-y: auto;">
                                        <div class="mb-4 pb-3 border-bottom">
                                            <small class="text-muted text-uppercase fw-semibold">Reservation Details</small>
                                            <div class="d-flex align-items-center justify-content-between mt-1">
                                                <div>
                                                    <span class="fw-bold fs-6">{{ $reservation->room->name ?? 'Room' }}</span>
                                                    <span class="text-secondary small ms-2">({{ \Carbon\Carbon::parse($reservation->reservation_date)->format('d M Y') }} | {{ substr($reservation->start_time, 0, 5) }} - {{ substr($reservation->end_time, 0, 5) }})</span>
                                                </div>
                                                <span class="badge bg-secondary-subtle text-secondary border rounded-pill">{{ $reservation->user->name ?? 'User' }}</span>
                                            </div>
                                            <p class="text-muted small mb-0 mt-1">Purpose: {{ $reservation->purpose }}</p>
                                        </div>

                                        <!-- Comments Timeline -->
                                        <div class="vstack gap-3 mb-4">
                                            @forelse($reservation->comments as $cmt)
                                                <div class="d-flex gap-3 align-items-start p-3 rounded-3 {{ $cmt->user_id === auth()->id() ? 'bg-primary-subtle bg-opacity-25 ms-4' : 'bg-light me-4' }}">
                                                    <img src="{{ $cmt->user->avatar_url }}" alt="{{ $cmt->user->name }}" class="rounded-circle object-fit-cover border" style="width: 38px; height: 38px;">
                                                    <div class="flex-grow-1">
                                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                                            <div class="d-flex align-items-center gap-2">
                                                                <span class="fw-bold small text-dark">{{ $cmt->user->name }}</span>
                                                                <span class="badge bg-secondary-subtle text-secondary small" style="font-size: 0.65rem;">{{ $cmt->user->role }}</span>
                                                            </div>
                                                            <div class="d-flex align-items-center gap-2">
                                                                <small class="text-muted" style="font-size: 0.75rem;">{{ $cmt->created_at->diffForHumans() }}</small>
                                                                @if(auth()->user()->isAdmin() || auth()->id() === $cmt->user_id)
                                                                    <form method="POST" action="{{ route('comments.destroy', $cmt) }}" class="d-inline">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="btn btn-link text-danger p-0 border-0 ms-1" style="font-size: 0.75rem;" title="Delete comment"><i class="bi bi-trash"></i></button>
                                                                    </form>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <p class="mb-0 text-secondary small">{{ $cmt->body }}</p>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="text-center text-muted py-4">
                                                    <i class="bi bi-chat-square-dots fs-2 d-block mb-2"></i>
                                                    <small>No comments yet. Be the first to start the discussion!</small>
                                                </div>
                                            @endforelse
                                        </div>

                                        <!-- Add Comment Form -->
                                        <form method="POST" action="{{ route('comments.store') }}">
                                            @csrf
                                            <input type="hidden" name="reservation_id" value="{{ $reservation->id }}">
                                            <div class="input-group">
                                                <input type="text" name="body" class="form-control" placeholder="Write a comment or update..." required>
                                                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-send-fill me-1"></i> Post</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

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
