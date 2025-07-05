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
                <a href="{{ route('admin.users.index') }}" class="nav-link text-white {{ request()->routeIs('admin.users.*') ? 'active bg-primary' : '' }}">
                    <i class="fas fa-users me-2"></i>
                    Пользователи
                </a>
            </li>
            
            <li class="nav-item mb-2">
                <a href="{{ route('admin.roles.index') }}" class="nav-link text-white {{ request()->routeIs('admin.roles.*') ? 'active bg-primary' : '' }}">
                    <i class="fas fa-user-shield me-2"></i>
                    Роли и права
                </a>
            </li>
            
            <li class="nav-item mb-2">
                <a href="{{ route('admin.settings.index') }}" class="nav-link text-white {{ request()->routeIs('admin.settings.*') ? 'active bg-primary' : '' }}">
                    <i class="fas fa-cog me-2"></i>
                    Настройки
                </a>
            </li>
            
            <li class="nav-item mb-2">
                <a href="{{ route('admin.email-templates.index') }}" class="nav-link text-white {{ request()->routeIs('admin.email-templates.*') ? 'active bg-primary' : '' }}">
                    <i class="fas fa-envelope me-2"></i>
                    Email шаблоны
                </a>
            </li>
            
            <li class="nav-item mb-2">
                <a href="{{ route('admin.security.index') }}" class="nav-link text-white {{ request()->routeIs('admin.security.*') ? 'active bg-primary' : '' }}">
                    <i class="fas fa-lock me-2"></i>
                    Безопасность
                </a>
            </li>
            
            <li class="nav-item mb-2">
                <a href="{{ route('admin.logs.index') }}" class="nav-link text-white {{ request()->routeIs('admin.logs.*') ? 'active bg-primary' : '' }}">
                    <i class="fas fa-file-alt me-2"></i>
                    Логи системы
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
