<nav class="navbar navbar-expand navbar-custom sticky-top">
    <div class="container-fluid px-0">
        <span class="navbar-brand mb-0 h6 text-secondary fw-semibold">
            Asset Reservation & Maintenance System
        </span>

        <ul class="navbar-nav ms-auto align-items-center gap-2">
            <!-- Notifications Bell Dropdown -->
            @php
                $notifications = auth()->user()->notifications()->take(5)->get();
                $unreadCount = auth()->user()->notifications()->where('is_read', false)->count();
            @endphp
            <li class="nav-item dropdown">
                <a class="nav-link text-secondary position-relative p-2" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" title="Notifications">
                    <i class="bi bi-bell fs-5"></i>
                    @if($unreadCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light" style="font-size: 0.65rem;">
                            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                        </span>
                    @endif
                </a>
                <div class="dropdown-menu dropdown-menu-end shadow border-0 p-0" style="width: 320px; max-height: 420px; overflow-y: auto;" aria-labelledby="notificationDropdown">
                    <div class="p-3 bg-light border-bottom d-flex align-items-center justify-content-between">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-bell-fill me-1 text-primary"></i> Notifications</h6>
                        @if($unreadCount > 0)
                            <form action="{{ route('notifications.read-all') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-link p-0 text-decoration-none small text-primary">Mark all as read</button>
                            </form>
                        @endif
                    </div>
                    <div class="list-group list-group-flush">
                        @forelse($notifications as $noti)
                            <form action="{{ route('notifications.read', $noti->id) }}" method="POST" id="noti-form-{{ $noti->id }}">
                                @csrf
                                <input type="hidden" name="redirect" value="1">
                                <a href="#" onclick="event.preventDefault(); document.getElementById('noti-form-{{ $noti->id }}').submit();" class="list-group-item list-group-item-action p-3 {{ !$noti->is_read ? 'bg-primary-subtle bg-opacity-25' : '' }}">
                                    <div class="d-flex w-100 justify-content-between align-items-center mb-1">
                                        <small class="fw-bold text-dark">{{ $noti->title }}</small>
                                        <small class="text-muted" style="font-size: 0.7rem;">{{ $noti->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-1 text-secondary small">{{ $noti->message }}</p>
                                </a>
                            </form>
                        @empty
                            <div class="p-4 text-center text-muted">
                                <i class="bi bi-bell-slash fs-3 d-block mb-1"></i>
                                <small>No notifications yet</small>
                            </div>
                        @endforelse
                    </div>
                </div>
            </li>

            <!-- User Role Badge -->
            <li class="nav-item me-2">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-2">
                    <i class="bi bi-shield-lock-fill me-1"></i> {{ auth()->user()->role }}
                </span>
            </li>

            <!-- User Dropdown -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle text-dark fw-medium d-flex align-items-center gap-2" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="rounded-circle object-fit-cover border border-secondary-subtle" style="width: 36px; height: 36px;">
                    <span>{{ auth()->user()->name }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" aria-labelledby="userDropdown">
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('profile.edit') }}">
                            <i class="bi bi-person me-1"></i> My Profile
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger d-flex align-items-center gap-2">
                                <i class="bi bi-box-arrow-right me-1"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>
