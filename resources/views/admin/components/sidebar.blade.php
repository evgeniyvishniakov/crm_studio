@php
    $menu = [
        [
            'route' => 'admin.dashboard',
            'icon' => 'fas fa-tachometer-alt',
            'label' => 'Панель управления',
            'permission' => 'dashboard',
        ],
        [
            'route' => 'admin.projects.index',
            'icon' => 'fas fa-building',
            'label' => 'Проекты',
            'permission' => 'projects',
        ],
        [
            'route' => 'admin.users.index',
            'icon' => 'fas fa-users',
            'label' => 'Пользователи',
            'permission' => 'users',
        ],
        [
            'route' => 'admin.roles.index',
            'icon' => 'fas fa-user-shield',
            'label' => 'Роли и права',
            'permission' => 'roles',
        ],
        [
            'route' => 'admin.settings.index',
            'icon' => 'fas fa-cog',
            'label' => 'Настройки',
            'permission' => 'settings',
        ],
        [
            'route' => 'admin.email-templates.index',
            'icon' => 'fas fa-envelope',
            'label' => 'Email шаблоны',
            'permission' => 'email-templates',
        ],
        [
            'route' => 'admin.security.index',
            'icon' => 'fas fa-lock',
            'label' => 'Безопасность',
            'permission' => 'security',
        ],
        [
            'route' => 'admin.logs.index',
            'icon' => 'fas fa-file-alt',
            'label' => 'Логи системы',
            'permission' => 'logs',
        ],
    ];
    $userPermissions = auth()->user()->permissions()->pluck('name')->toArray();
@endphp
<nav class="sidebar bg-dark text-white" style="width: 250px; min-height: 100vh;">
    <div class="sidebar-header p-3 border-bottom border-secondary">
        <div class="d-flex align-items-center">
            <i class="fas fa-shield-alt text-primary me-2"></i>
            <span class="fw-bold">CRM Studio Admin</span>
        </div>
    </div>
    <div class="sidebar-content p-3">
        <ul class="nav flex-column">
            @foreach ($menu as $item)
                @php
                    $hasAccess = in_array($item['permission'], $userPermissions);
                @endphp
                <li class="nav-item mb-2">
                    <a href="{{ $hasAccess ? route($item['route']) : '#' }}"
                       class="nav-link text-white {{ request()->routeIs($item['route']) && $hasAccess ? 'active bg-primary' : '' }} {{ !$hasAccess ? 'disabled-link' : '' }}"
                       @if(!$hasAccess) tabindex="-1" aria-disabled="true" @endif>
                        <i class="{{ $item['icon'] }} me-2"></i>
                        {{ $item['label'] }}
                        @if(!$hasAccess)
                            <i class="fas fa-lock ms-2"></i>
                        @endif
                    </a>
                </li>
            @endforeach
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
<style>
.disabled-link {
    pointer-events: none;
    color: #aaa !important;
    opacity: 0.7;
}
</style> 
