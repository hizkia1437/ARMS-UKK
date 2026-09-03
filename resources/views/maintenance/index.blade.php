@extends('layouts.app')

@section('title', 'Maintenance Reports')

@section('content')
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
    <div>
        <h3 class="fw-bold mb-1">Maintenance Reports</h3>
        <p class="text-muted mb-0">Track asset issue reports and resolution progress.</p>
    </div>
    <div>
        <a href="{{ route('maintenance.create') }}" class="btn btn-primary rounded-2 d-inline-flex align-items-center gap-2">
            <i class="bi bi-plus-circle"></i> Create Maintenance Report
        </a>
    </div>
</div>

<div class="card card-custom p-4">
    <!-- Search, Filter & Sort Form -->
    <form method="GET" action="{{ route('maintenance.index') }}" class="mb-4">
        <div class="row g-2 align-items-center">
            <div class="col-12 col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control border-start-0 ps-0" placeholder="Search maintenance reports...">
                </div>
            </div>
            <div class="col-6 col-md-2">
                <select name="status" class="form-select text-secondary" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="Pending" {{ request('status') === 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Completed" {{ request('status') === 'Completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <select name="target_type" class="form-select text-secondary" onchange="this.form.submit()">
                    <option value="">All Targets</option>
                    <option value="asset" {{ request('target_type') === 'asset' ? 'selected' : '' }}>Asset Equipment</option>
                    <option value="room" {{ request('target_type') === 'room' ? 'selected' : '' }}>Room Facility</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <select name="sort_by" class="form-select text-secondary" onchange="this.form.submit()">
                    <option value="created_at" {{ request('sort_by', 'created_at') === 'created_at' ? 'selected' : '' }}>Sort by: Date</option>
                    <option value="report_code" {{ request('sort_by') === 'report_code' ? 'selected' : '' }}>Sort by: Code</option>
                    <option value="status" {{ request('sort_by') === 'status' ? 'selected' : '' }}>Sort by: Status</option>
                </select>
            </div>
            <div class="col-6 col-md-2 d-flex gap-1">
                <select name="sort_dir" class="form-select text-secondary" onchange="this.form.submit()">
                    <option value="desc" {{ request('sort_dir', 'desc') === 'desc' ? 'selected' : '' }}>DESC ⬇️</option>
                    <option value="asc" {{ request('sort_dir') === 'asc' ? 'selected' : '' }}>ASC ⬆️</option>
                </select>
                @if(request('search') || request('status') || request('target_type') || request('sort_by') || request('sort_dir'))
                    <a href="{{ route('maintenance.index') }}" class="btn btn-outline-secondary rounded-2" title="Reset Filters"><i class="bi bi-x-circle"></i></a>
                @endif
            </div>
        </div>
    </form>

    <!-- Table -->
    <div class="table-responsive">
        <table class="table table-custom table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Report Code</th>
                    <th>Reported By</th>
                    <th>Target (Asset / Room)</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Report Date</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $report)
                <tr>
                    <td class="fw-semibold text-primary">{{ $report->report_code }}</td>
                    <td>{{ $report->user->name ?? '-' }}</td>
                    <td>
                        @if($report->room_id && $report->room)
                            <span class="fw-medium text-dark"><i class="bi bi-door-open-fill text-info me-1"></i> {{ $report->room->name }}</span>
                            <div class="text-muted small">Room Facility ({{ $report->room->room_code }})</div>
                        @elseif($report->asset_id && $report->asset)
                            <span class="fw-medium text-dark"><i class="bi bi-laptop-fill text-primary me-1"></i> {{ $report->asset->name }}</span>
                            <div class="text-muted small">{{ $report->asset->asset_code }} &bull; {{ $report->asset->location }}</div>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td><span class="text-truncate d-inline-block" style="max-width: 250px;">{{ $report->description }}</span></td>
                    <td>
                        @if($report->status === 'Completed')
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1">Completed</span>
                        @else
                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-3 py-1">Pending</span>
                        @endif
                    </td>
                    <td>{{ $report->created_at->format('d M Y') }}</td>
                    <td class="text-end">
                        <!-- Staff & Admin Mark as Completed quick action -->
                        @if((auth()->user()->isAdmin() || auth()->user()->isStaff()) && $report->status === 'Pending')
                        <form method="POST" action="{{ route('maintenance.update-status', $report) }}" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="Completed">
                            <button type="submit" class="btn btn-sm btn-success rounded-2 me-1" title="Mark Completed">
                                <i class="bi bi-check2-circle"></i> Mark Completed
                            </button>
                        </form>
                        @endif

                        @if(auth()->user()->isAdmin() || auth()->id() === $report->user_id)
                        <a href="{{ route('maintenance.edit', $report) }}" class="btn btn-sm btn-outline-primary me-1 rounded-2">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>
                        @endif

                        @if(auth()->user()->isAdmin())
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-2" data-bs-toggle="modal" data-bs-target="#deleteMntModal{{ $report->id }}">
                            <i class="bi bi-trash"></i> Delete
                        </button>

                        <!-- Delete Modal -->
                        <div class="modal fade text-start" id="deleteMntModal{{ $report->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow">
                                    <div class="modal-header border-0 pb-0">
                                        <h5 class="modal-title fw-bold">Confirm Delete</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body py-3">
                                        Are you sure you want to delete report <strong>{{ $report->report_code }}</strong> for {{ $report->asset->name ?? 'Asset' }}?
                                    </div>
                                    <div class="modal-footer border-0 pt-0">
                                        <button type="button" class="btn btn-light rounded-2" data-bs-dismiss="modal">Cancel</button>
                                        <form method="POST" action="{{ route('maintenance.destroy', $report) }}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger rounded-2">Delete Report</button>
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
                    <td colspan="7" class="text-center py-4 text-muted">No maintenance reports found matching your search.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4 d-flex justify-content-end">
        {{ $reports->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
