@php
    $user = auth('panel')->user();
    $openTicketsCount = \App\Models\Clients\SupportTicket::where('status', 'open')->count();
    $pendingTicketsCount = \App\Models\Clients\SupportTicket::where('status', 'pending')->count();
    $newTicketsCount = $openTicketsCount + $pendingTicketsCount;
    $lastTickets = \App\Models\Clients\SupportTicket::with('user')->whereIn('status', ['open','pending'])->orderByDesc('created_at')->limit(5)->get();
    use App\Models\Notification;
    $unreadNotifications = ($user && $user->id) ? Notification::where(function($q) use ($user) {
        $q->whereNull('user_id')->orWhere('user_id', $user->id);
    })->where('is_read', false)->orderByDesc('created_at')->limit(5)->get() : collect();
    $unreadCount = ($user && $user->id) ? Notification::where(function($q) use ($user) {
        $q->whereNull('user_id')->orWhere('user_id', $user->id);
    })->where('is_read', false)->count() : 0;
    // Критические ошибки за сутки только со статусом 'new'
    $criticalErrors = \App\Models\SystemLog::where('level', 'error')
        ->where('status', 'new')
        ->where('created_at', '>=', now()->subDay())
        ->orderByDesc('created_at')
        ->limit(5)
        ->get();
    $criticalCount = $criticalErrors->count();
@endphp
@if($user)
<header class="bg-white border-bottom shadow-sm p-3">
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <button class="btn btn-link text-dark d-lg-none me-3" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <h4 class="mb-0">@yield('page-title', 'Админ панель')</h4>
        </div>
        
        <div class="d-flex align-items-center">
            <!-- Критические ошибки -->
            <div class="dropdown me-3">
                <button class="btn btn-link text-dark position-relative" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-exclamation-circle"></i>
                    @if($criticalCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            {{ $criticalCount }}
                        </span>
                    @endif
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><h6 class="dropdown-header">Критические ошибки</h6></li>
                    @forelse($criticalErrors as $log)
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.logs.index') }}">
                                <span class="fw-bold">{{ Str::limit($log->message, 60) }}</span>
                                <br>
                                <small class="text-muted">{{ $log->created_at->format('d.m.Y H:i') }}</small>
                            </a>
                        </li>
                    @empty
                        <li><span class="dropdown-item text-muted">Нет критических ошибок</span></li>
                    @endforelse
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="{{ route('admin.logs.index') }}">Показать все логи</a></li>
                </ul>
            </div>
            <!-- Уведомления -->
            <div class="dropdown me-3">
                <button class="btn btn-link text-dark position-relative" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-bell"></i>
                    @if($unreadCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            {{ $unreadCount }}
                        </span>
                    @endif
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><h6 class="dropdown-header">Уведомления</h6></li>
                    @forelse($unreadNotifications as $notification)
                        <li>
                            <a class="dropdown-item" href="{{ $notification->url ?? '#' }}">
                                <span class="fw-bold">{{ $notification->title }}</span>
                                <br>
                                <small class="text-muted">{{ $notification->created_at->format('d.m.Y H:i') }}</small>
                            </a>
                        </li>
                    @empty
                        <li><span class="dropdown-item text-muted">Нет новых уведомлений</span></li>
                    @endforelse
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="{{ route('admin.tickets.index') }}">Показать все тикеты</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.notifications.index') }}">Показать все уведомления</a></li>
                </ul>
            </div>
            
            <!-- Профиль пользователя -->
            <div class="dropdown">
                <button class="btn btn-link text-dark d-flex align-items-center" type="button" data-bs-toggle="dropdown">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                        <i class="fas fa-user"></i>
                    </div>
                    <span>{{ $user ? $user->name : 'Администратор' }}</span>
                    <i class="fas fa-chevron-down ms-2"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Профиль</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Настройки</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('admin.logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="fas fa-sign-out-alt me-2"></i>Выйти
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>
@endif 
