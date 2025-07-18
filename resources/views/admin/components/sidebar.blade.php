@php
    $user = auth()->user();
    $openTicketsCount = \App\Models\Clients\SupportTicket::where('status', 'open')->count();
    $pendingTicketsCount = \App\Models\Clients\SupportTicket::where('status', 'pending')->count();
    $unreadNotificationsCount = \App\Models\Notification::where(function($q) use ($user) {
        $q->whereNull('user_id')->orWhere('user_id', $user->id);
    })->where('is_read', false)->count();
@endphp
@if($user && !empty($user->is_panel_admin))
<nav class="sidebar bg-dark text-white" style="width: 250px; min-height: 100vh;">
    <div class="sidebar-header p-3 border-bottom border-secondary">
        <div class="d-flex align-items-center">
            <i class="fas fa-shield-alt text-primary me-2"></i>
            <span class="fw-bold">CRM Studio Admin</span>
        </div>
    </div>
    <div class="sidebar-content p-3">
        <ul class="nav flex-column">
            <li class="nav-item mb-2">
                <a href="{{ route('admin.dashboard') }}" class="nav-link text-white {{ request()->routeIs('admin.dashboard') ? 'active bg-primary' : '' }}">
                    <i class="fas fa-tachometer-alt me-2"></i>
                    Панель управления
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="{{ route('admin.projects.index') }}" class="nav-link text-white {{ request()->routeIs('admin.projects.index') ? 'active bg-primary' : '' }}">
                    <i class="fas fa-building me-2"></i>
                    Проекты
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="{{ route('admin.users.index') }}" class="nav-link text-white {{ request()->routeIs('admin.users.index') ? 'active bg-primary' : '' }}">
                    <i class="fas fa-users me-2"></i>
                    Пользователи
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="{{ route('admin.roles.index') }}" class="nav-link text-white {{ request()->routeIs('admin.roles.index') ? 'active bg-primary' : '' }}">
                    <i class="fas fa-user-shield me-2"></i>
                    Роли и права
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="{{ route('admin.settings.index') }}" class="nav-link text-white {{ request()->routeIs('admin.settings.index') ? 'active bg-primary' : '' }}">
                    <i class="fas fa-cog me-2"></i>
                    Настройки
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="{{ route('admin.email-templates.index') }}" class="nav-link text-white {{ request()->routeIs('admin.email-templates.index') ? 'active bg-primary' : '' }}">
                    <i class="fas fa-envelope me-2"></i>
                    Email шаблоны
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="{{ route('admin.security.index') }}" class="nav-link text-white {{ request()->routeIs('admin.security.index') ? 'active bg-primary' : '' }}">
                    <i class="fas fa-lock me-2"></i>
                    Безопасность
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="{{ route('admin.logs.index') }}" class="nav-link text-white {{ request()->routeIs('admin.logs.index') ? 'active bg-primary' : '' }}">
                    <i class="fas fa-file-alt me-2"></i>
                    Логи системы
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="{{ route('admin.tickets.index') }}" class="nav-link text-white {{ request()->routeIs('admin.tickets.index') ? 'active bg-primary' : '' }}">
                    <i class="fa fa-comments me-2"></i>
                    Сообщения
                    @if($openTicketsCount + $pendingTicketsCount > 0)
                        <span class="badge bg-warning text-dark ms-1">{{ $openTicketsCount + $pendingTicketsCount }}</span>
                    @endif
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="{{ route('admin.notifications.index') }}" class="nav-link text-white {{ request()->routeIs('admin.notifications.index') ? 'active bg-primary' : '' }}">
                    <i class="fas fa-bell me-2"></i>
                    Уведомления
                    @if($unreadNotificationsCount > 0)
                        <span class="badge bg-danger ms-1">{{ $unreadNotificationsCount }}</span>
                    @endif
                </a>
            </li>
        </ul>
        <hr class="border-secondary my-4">
        <ul class="nav flex-column">
            <li class="nav-item mb-2">
                <a href="{{ route('dashboard') }}" class="nav-link text-muted">
                    <i class="fas fa-arrow-left me-2"></i>
                    Вернуться в CRM
                </a>
            </li>
            <li class="nav-item">
                <form method="POST" action="{{ route('admin.logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="nav-link text-muted border-0 bg-transparent">
                        <i class="fas fa-sign-out-alt me-2"></i>
                        Выйти
                    </button>
                </form>
            </li>
        </ul>
    </div>
</nav>
@endif
<style>
.disabled-link {
    pointer-events: none;
    color: #aaa !important;
    opacity: 0.7;
}
</style> 
