@extends('layouts.app')

@section('title', 'Rooms')

@section('content')
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
    <div>
        <h3 class="fw-bold mb-1">Rooms Management</h3>
        <p class="text-muted mb-0">View and manage organization rooms and availability.</p>
    </div>
    @if(auth()->user()->isAdmin())
    <div>
        <a href="{{ route('rooms.create') }}" class="btn btn-primary rounded-2 d-inline-flex align-items-center gap-2">
            <i class="bi bi-plus-circle"></i> Add New Room
        </a>
    </div>
    @endif
</div>

<div class="card card-custom p-4">
    <!-- Search Form -->
    <form method="GET" action="{{ route('rooms.index') }}" class="mb-4">
        <div class="row g-2">
            <div class="col-12 col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control border-start-0 ps-0" placeholder="Search by code, name, capacity, or status...">
                </div>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-secondary rounded-2">Search</button>
                @if(request('search'))
                    <a href="{{ route('rooms.index') }}" class="btn btn-outline-secondary rounded-2">Reset</a>
                @endif
            </div>
        </div>
    </form>

    <!-- Table -->
    <div class="table-responsive">
        <table class="table table-custom table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Room Code</th>
                    <th>Name</th>
                    <th>Capacity</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rooms as $room)
                <tr>
                    <td class="fw-semibold text-primary">{{ $room->room_code }}</td>
                    <td class="fw-medium text-dark">{{ $room->name }}</td>
                    <td><i class="bi bi-people me-1 text-secondary"></i> {{ $room->capacity }} Persons</td>
                    <td>
                        @if($room->status === 'Available')
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1">Available</span>
                        @elseif($room->status === 'Occupied')
                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-3 py-1">Occupied</span>
                        @else
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-1">Under Maintenance</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('maintenance.create') }}?room_id={{ $room->id }}&from=rooms" class="btn btn-sm btn-outline-warning me-1 rounded-2" title="Report Issue for Room">
                            <i class="bi bi-tools me-1"></i> Report Issue
                        </a>

                        @if(auth()->user()->isAdmin())
                        <a href="{{ route('rooms.edit', $room) }}" class="btn btn-sm btn-outline-primary me-1 rounded-2">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-2" data-bs-toggle="modal" data-bs-target="#deleteRoomModal{{ $room->id }}">
                            <i class="bi bi-trash"></i> Delete
                        </button>

                        <!-- Delete Modal -->
                        <div class="modal fade text-start" id="deleteRoomModal{{ $room->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow">
                                    <div class="modal-header border-0 pb-0">
                                        <h5 class="modal-title fw-bold">Confirm Delete</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body py-3">
                                        Are you sure you want to delete room <strong>{{ $room->name }}</strong> ({{ $room->room_code }})?
                                    </div>
                                    <div class="modal-footer border-0 pt-0">
                                        <button type="button" class="btn btn-light rounded-2" data-bs-dismiss="modal">Cancel</button>
                                        <form method="POST" action="{{ route('rooms.destroy', $room) }}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger rounded-2">Delete Room</button>
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
                    <td colspan="5" class="text-center py-4 text-muted">No rooms found matching your search.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $rooms->links() }}
    </div>
</div>
@endsection
