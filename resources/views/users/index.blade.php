@extends('layouts.app')

@section('title', 'Users')

@section('content')
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
    <div>
        <h3 class="fw-bold mb-1">Users Management</h3>
        <p class="text-muted mb-0">Manage system users and access roles.</p>
    </div>
    <div>
        <a href="{{ route('users.create') }}" class="btn btn-primary rounded-2 d-inline-flex align-items-center gap-2">
            <i class="bi bi-plus-circle"></i> Add New User
        </a>
    </div>
</div>

<div class="card card-custom p-4">
    <!-- Search Form -->
    <form method="GET" action="{{ route('users.index') }}" class="mb-4">
        <div class="row g-2">
            <div class="col-12 col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control border-start-0 ps-0" placeholder="Search by name, email, or role...">
                </div>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-secondary rounded-2">Search</button>
                @if(request('search'))
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary rounded-2">Reset</a>
                @endif
            </div>
        </div>
    </form>

    <!-- Table -->
    <div class="table-responsive">
        <table class="table table-custom table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Created At</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                    <td class="fw-semibold text-dark">{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @if($user->role === 'Admin')
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-1">Admin</span>
                        @elseif($user->role === 'Staff')
                            <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-3 py-1">Staff</span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-3 py-1">User</span>
                        @endif
                    </td>
                    <td>{{ $user->created_at->format('d M Y') }}</td>
                    <td class="text-end">
                        @if(auth()->id() === $user->id || $user->email === 'admin@arms.test')
                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-3 py-1">
                            <i class="bi bi-shield-check me-1"></i> Protected Admin
                        </span>
                        @else
                        <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary me-1 rounded-2">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-2" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $user->id }}">
                            <i class="bi bi-trash"></i> Delete
                        </button>

                        <!-- Delete Confirmation Modal -->
                        <div class="modal fade text-start" id="deleteModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow">
                                    <div class="modal-header border-0 pb-0">
                                        <h5 class="modal-title fw-bold">Confirm Delete</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body py-3">
                                        Are you sure you want to delete user <strong>{{ $user->name }}</strong> ({{ $user->email }})?
                                    </div>
                                    <div class="modal-footer border-0 pt-0">
                                        <button type="button" class="btn btn-light rounded-2" data-bs-dismiss="modal">Cancel</button>
                                        <form method="POST" action="{{ route('users.destroy', $user) }}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger rounded-2">Delete User</button>
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
                    <td colspan="6" class="text-center py-4 text-muted">No users found matching your query.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4 d-flex justify-content-end">
        {{ $users->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
