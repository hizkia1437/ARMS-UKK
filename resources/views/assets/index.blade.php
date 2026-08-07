@extends('layouts.app')

@section('title', 'Assets')

@section('content')
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
    <div>
        <h3 class="fw-bold mb-1">Assets Management</h3>
        <p class="text-muted mb-0">View and manage organization assets.</p>
    </div>
    @if(auth()->user()->isAdmin() || auth()->user()->isStaff())
    <div>
        <a href="{{ route('assets.create') }}" class="btn btn-primary rounded-2 d-inline-flex align-items-center gap-2">
            <i class="bi bi-plus-circle"></i> Add New Asset
        </a>
    </div>
    @endif
</div>

<div class="card card-custom p-4">
    <!-- Search Form -->
    <form method="GET" action="{{ route('assets.index') }}" class="mb-4">
        <div class="row g-2">
            <div class="col-12 col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control border-start-0 ps-0" placeholder="Search by code, name, category, condition, or location...">
                </div>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-secondary rounded-2">Search</button>
                @if(request('search'))
                    <a href="{{ route('assets.index') }}" class="btn btn-outline-secondary rounded-2">Reset</a>
                @endif
            </div>
        </div>
    </form>

    <!-- Table -->
    <div class="table-responsive">
        <table class="table table-custom table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Asset Code</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Condition</th>
                    <th>Location</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($assets as $asset)
                <tr>
                    <td class="fw-semibold text-primary">{{ $asset->asset_code }}</td>
                    <td class="fw-medium text-dark">{{ $asset->name }}</td>
                    <td><span class="badge bg-light text-dark border px-2 py-1">{{ $asset->category }}</span></td>
                    <td>
                        @if($asset->condition === 'Good')
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1">Good</span>
                        @elseif($asset->condition === 'Needs Repair')
                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-3 py-1">Needs Repair</span>
                        @else
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-1">Damaged</span>
                        @endif
                    </td>
                    <td><i class="bi bi-geo-alt text-secondary me-1"></i>{{ $asset->location }}</td>
                    <td class="text-end">
                        @if(auth()->user()->isUser())
                        <a href="{{ route('maintenance.create') }}?asset_id={{ $asset->id }}&from=assets" class="btn btn-sm btn-outline-warning rounded-2" title="Report Maintenance Issue">
                            <i class="bi bi-tools me-1"></i> Report Issue
                        </a>
                        @endif

                        @if(auth()->user()->isAdmin() || auth()->user()->isStaff())
                        <div class="dropdown d-inline">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle me-1 rounded-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Set Condition
                            </button>
                            <ul class="dropdown-menu shadow-sm border-0">
                                <li>
                                    <form method="POST" action="{{ route('assets.update-condition', $asset) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="condition" value="Good">
                                        <button type="submit" class="dropdown-item text-success"><i class="bi bi-check-circle me-1"></i> Good</button>
                                    </form>
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('assets.update-condition', $asset) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="condition" value="Needs Repair">
                                        <button type="submit" class="dropdown-item text-warning"><i class="bi bi-tools me-1"></i> Needs Repair</button>
                                    </form>
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('assets.update-condition', $asset) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="condition" value="Damaged">
                                        <button type="submit" class="dropdown-item text-danger"><i class="bi bi-x-circle me-1"></i> Damaged</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                        <a href="{{ route('assets.edit', $asset) }}" class="btn btn-sm btn-outline-primary me-1 rounded-2">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-2" data-bs-toggle="modal" data-bs-target="#deleteAssetModal{{ $asset->id }}">
                            <i class="bi bi-trash"></i> Delete
                        </button>

                        <!-- Delete Modal -->
                        <div class="modal fade text-start" id="deleteAssetModal{{ $asset->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow">
                                    <div class="modal-header border-0 pb-0">
                                        <h5 class="modal-title fw-bold">Confirm Delete</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body py-3">
                                        Are you sure you want to delete asset <strong>{{ $asset->name }}</strong> ({{ $asset->asset_code }})?
                                    </div>
                                    <div class="modal-footer border-0 pt-0">
                                        <button type="button" class="btn btn-light rounded-2" data-bs-dismiss="modal">Cancel</button>
                                        <form method="POST" action="{{ route('assets.destroy', $asset) }}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger rounded-2">Delete Asset</button>
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
                    <td colspan="6" class="text-center py-4 text-muted">No assets found matching your search.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $assets->links() }}
    </div>
</div>
@endsection
