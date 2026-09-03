@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h3 class="fw-bold mb-1">Dashboard</h3>
        <p class="text-muted mb-0">Welcome back, {{ auth()->user()->name }}! Here is your system overview.</p>
    </div>
    <div>
        <span class="text-secondary small">{{ now()->format('l, d F Y') }}</span>
    </div>
</div>

<!-- Stat Cards Row -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-start border-4 border-primary">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Total Assets</span>
                    <h2 class="fw-bold mb-0 mt-1">{{ number_format($totalAssets) }}</h2>
                </div>
                <div class="bg-primary-subtle text-primary p-3 rounded-circle">
                    <i class="bi bi-laptop fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-start border-4 border-info">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Total Rooms</span>
                    <h2 class="fw-bold mb-0 mt-1">{{ number_format($totalRooms) }}</h2>
                </div>
                <div class="bg-info-subtle text-info p-3 rounded-circle">
                    <i class="bi bi-door-open-fill fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-start border-4 border-warning">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">
                        {{ auth()->user()->isUser() ? 'My Pending Reservations' : 'Pending Reservations' }}
                    </span>
                    <h2 class="fw-bold mb-0 mt-1">{{ number_format($pendingReservations) }}</h2>
                </div>
                <div class="bg-warning-subtle text-warning p-3 rounded-circle">
                    <i class="bi bi-clock-history fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-start border-4 border-danger">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">
                        {{ auth()->user()->isUser() ? 'My Pending Reports' : 'Pending Maintenances' }}
                    </span>
                    <h2 class="fw-bold mb-0 mt-1">{{ number_format($pendingMaintenances) }}</h2>
                </div>
                <div class="bg-danger-subtle text-danger p-3 rounded-circle">
                    <i class="bi bi-tools fs-3"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Reservations Table -->
<div class="card card-custom p-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h5 class="fw-bold mb-0">Recent Reservations</h5>
        <a href="{{ route('reservations.index') }}" class="btn btn-sm btn-outline-primary rounded-2">
            View All Reservations <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-custom table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>User</th>
                    <th>Room</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentReservations as $reservation)
                <tr>
                    <td class="fw-semibold text-primary">{{ $reservation->reservation_code }}</td>
                    <td>{{ $reservation->user->name ?? '-' }}</td>
                    <td>{{ $reservation->room->name ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($reservation->reservation_date)->format('d M Y') }}</td>
                    <td>{{ substr($reservation->start_time, 0, 5) }} - {{ substr($reservation->end_time, 0, 5) }}</td>
                    <td>
                        @if($reservation->status === 'Approved')
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1">Approved</span>
                        @elseif($reservation->status === 'Pending')
                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-3 py-1">Pending</span>
                        @else
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-1">Rejected</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">No recent reservations found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
