@php
    $user = auth('panel')->user();
    $openTicketsCount = \App\Models\Clients\SupportTicket::where('status', 'open')->count();
    $pendingTicketsCount = \App\Models\Clients\SupportTicket::where('status', 'pending')->count();
    $unreadNotificationsCount = ($user && $user->id) ? \App\Models\Notification::where(function($q) use ($user) {
        $q->whereNull('user_id')->orWhere('user_id', $user->id);
    })->where('is_read', false)->count() : 0;
@endphp
@if($user)
<nav class="sidebar bg-dark text-white" style="width: 250px; min-height: 100vh;">
    <div class="sidebar-header p-3 border-bottom border-secondary">
        <div class="d-flex align-items-center">
            <i class="fas fa-shield-alt text-primary me-2"></i>
            <span class="fw-bold">CRM Studio Admin</span>
        </div>
    </div>
    <div class="sidebar-content p-3" style="max-height: calc(100vh - 80px); overflow-y: auto;">
        <ul class="nav flex-column">
            <!-- Основные разделы -->
            <li class="nav-item mb-2">
                <a href="{{ route('admin.dashboard') }}" class="nav-link text-white {{ request()->routeIs('admin.dashboard') ? 'active bg-primary' : '' }}">
                    <i class="fas fa-tachometer-alt me-2"></i>
                    Панель управления
                </a>
            </li>
            
            <!-- Управление проектами -->
            <li class="nav-item mb-2">
                <a href="{{ route('admin.projects.index') }}" class="nav-link text-white {{ request()->routeIs('admin.projects.*') ? 'active bg-primary' : '' }}">
                    <i class="fas fa-building me-2"></i>
                    Проекты
                </a>
            </li>
            
            <!-- Управление пользователями -->
            <li class="nav-item mb-2">
                <a href="{{ route('admin.users.index') }}" class="nav-link text-white {{ request()->routeIs('admin.users.index') ? 'active bg-primary' : '' }}">
                    <i class="fas fa-users me-2"></i>
                    Пользователи
                </a>
            </li>
            
            <!-- Настройки -->
            <li class="nav-item mb-2">
                <div class="nav-link text-white d-flex align-items-center justify-content-between" 
                     data-bs-toggle="collapse" 
                     data-bs-target="#settingsSubmenu" 
                     aria-expanded="{{ request()->routeIs('admin.roles.*') || request()->routeIs('admin.plans.*') || request()->routeIs('admin.payment-settings.*') || request()->routeIs('admin.currencies.*') || request()->routeIs('admin.settings.*') || request()->routeIs('admin.languages.*') || request()->routeIs('admin.email-templates.*') || request()->routeIs('admin.security.*') ? 'true' : 'false' }}" 
                     aria-controls="settingsSubmenu"
                     style="cursor: pointer;">
                    <div>
                        <i class="fas fa-cog me-2"></i>
                        Настройки
                    </div>
                    <i class="fas fa-chevron-down" id="settingsChevron"></i>
                </div>
                <div class="collapse {{ request()->routeIs('admin.roles.*') || request()->routeIs('admin.plans.*') || request()->routeIs('admin.payment-settings.*') || request()->routeIs('admin.currencies.*') || request()->routeIs('admin.settings.*') || request()->routeIs('admin.languages.*') || request()->routeIs('admin.email-templates.*') || request()->routeIs('admin.security.*') ? 'show' : '' }}" id="settingsSubmenu">
                    <ul class="nav flex-column ms-3 mt-2">
                        <li class="nav-item mb-1">
                            <a href="{{ route('admin.roles.index') }}" class="nav-link text-white-50 {{ request()->routeIs('admin.roles.*') ? 'active bg-primary' : '' }}">
                                <i class="fas fa-user-shield me-2"></i>
                                Роли и права
                            </a>
                        </li>
                        <li class="nav-item mb-1">
                            <a href="{{ route('admin.plans.index') }}" class="nav-link text-white-50 {{ request()->routeIs('admin.plans.*') ? 'active bg-primary' : '' }}">
                                <i class="fas fa-tag me-2"></i>
                                Тарифы
                            </a>
                        </li>
                        <li class="nav-item mb-1">
                            <a href="{{ route('admin.payment-settings.index') }}" class="nav-link text-white-50 {{ request()->routeIs('admin.payment-settings.*') ? 'active bg-primary' : '' }}">
                                <i class="fas fa-credit-card me-2"></i>
                                Настройки платежей
                            </a>
                        </li>
                        <li class="nav-item mb-1">
                            <a href="{{ route('admin.currencies.index') }}" class="nav-link text-white-50 {{ request()->routeIs('admin.currencies.*') ? 'active bg-primary' : '' }}">
                                <i class="fas fa-dollar-sign me-2"></i>
                                Валюты
                            </a>
                        </li>
                        <li class="nav-item mb-1">
                            <a href="{{ route('admin.settings.index') }}" class="nav-link text-white-50 {{ request()->routeIs('admin.settings.*') ? 'active bg-primary' : '' }}">
                                <i class="fas fa-cog me-2"></i>
                                Основные настройки
                            </a>
                        </li>
                        <li class="nav-item mb-1">
                            <a href="{{ route('admin.languages.index') }}" class="nav-link text-white-50 {{ request()->routeIs('admin.languages.*') ? 'active bg-primary' : '' }}">
                                <i class="fas fa-globe me-2"></i>
                                Языки
                            </a>
                        </li>
                        <li class="nav-item mb-1">
                            <a href="{{ route('admin.email-templates.index') }}" class="nav-link text-white-50 {{ request()->routeIs('admin.email-templates.*') ? 'active bg-primary' : '' }}">
                                <i class="fas fa-envelope me-2"></i>
                                Email шаблоны
                            </a>
                        </li>
                        <li class="nav-item mb-1">
                            <a href="{{ route('admin.security.index') }}" class="nav-link text-white-50 {{ request()->routeIs('admin.security.*') ? 'active bg-primary' : '' }}">
                                <i class="fas fa-lock me-2"></i>
                                Безопасность
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            
            <!-- Мониторинг и поддержка -->
            <li class="nav-item mb-2">
                <a href="{{ route('admin.tickets.index') }}" class="nav-link text-white {{ request()->routeIs('admin.tickets.index') ? 'active bg-primary' : '' }}">
                    <i class="fa fa-comments me-2"></i>
                    Сообщения
                    @if($openTicketsCount + $pendingTicketsCount > 0)
                        <span class="badge bg-warning text-dark ms-1">{{ $openTicketsCount + $pendingTicketsCount }}</span>
                    @endif
                </a>
            </li>
            {{-- Уведомления перемещены вниз --}}
            <li class="nav-item mb-2">
                <a href="{{ route('admin.logs.index') }}" class="nav-link text-white {{ request()->routeIs('admin.logs.index') ? 'active bg-primary' : '' }}">
                    <i class="fas fa-file-alt me-2"></i>
                    Логи системы
                </a>
            </li>
            
            <!-- Резервное копирование -->
            <li class="nav-item mb-2">
                <a href="{{ route('admin.backups.index') }}" class="nav-link text-white {{ request()->routeIs('admin.backups.*') ? 'active bg-primary' : '' }}">
                    <i class="fas fa-database me-2"></i>
                    Резервные копии
                </a>
            </li>
            
            <!-- База знаний -->
            <li class="nav-item mb-2">
                <a href="{{ route('admin.knowledge.index') }}" class="nav-link text-white {{ request()->routeIs('admin.knowledge.*') ? 'active bg-primary' : '' }}">
                    <i class="fas fa-book me-2"></i>
                    База знаний (Админ)
                </a>
            </li>
            
            <!-- Блог -->
            <li class="nav-item mb-2">
                <div class="nav-link text-white d-flex align-items-center justify-content-between" 
                     data-bs-toggle="collapse" 
                     data-bs-target="#blogSubmenu" 
                     aria-expanded="{{ request()->routeIs('admin.blog.*') || request()->routeIs('admin.blog-categories.*') || request()->routeIs('admin.blog-tags.*') ? 'true' : 'false' }}" 
                     aria-controls="blogSubmenu"
                     style="cursor: pointer;">
                    <div>
                        <i class="fas fa-blog me-2"></i>
                        Блог
                    </div>
                    <i class="fas fa-chevron-down" id="blogChevron"></i>
                </div>
                <div class="collapse {{ request()->routeIs('admin.blog.*') || request()->routeIs('admin.blog-categories.*') || request()->routeIs('admin.blog-tags.*') ? 'show' : '' }}" id="blogSubmenu">
                    <ul class="nav flex-column ms-3 mt-2">
                        <li class="nav-item mb-1">
                            <a href="{{ route('admin.blog.index') }}" class="nav-link text-white-50 {{ request()->routeIs('admin.blog.*') ? 'active bg-primary' : '' }}">
                                <i class="fas fa-newspaper me-2"></i>
                                Статьи
                            </a>
                        </li>
                        <li class="nav-item mb-1">
                            <a href="{{ route('admin.blog-categories.index') }}" class="nav-link text-white-50 {{ request()->routeIs('admin.blog-categories.*') ? 'active bg-primary' : '' }}">
                                <i class="fas fa-tags me-2"></i>
                                Категории
                            </a>
                        </li>
                        <li class="nav-item mb-1">
                            <a href="{{ route('admin.blog-tags.index') }}" class="nav-link text-white-50 {{ request()->routeIs('admin.blog-tags.*') ? 'active bg-primary' : '' }}">
                                <i class="fas fa-hashtag me-2"></i>
                                Теги
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            
            <!-- Подписки и уведомления -->
            <li class="nav-item mb-2">
                <a href="{{ route('admin.subscriptions.index') }}" class="nav-link text-white {{ request()->routeIs('admin.subscriptions.*') ? 'active bg-primary' : '' }}">
                    <i class="fas fa-credit-card me-2"></i>
                    Подписки
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

/* Стили для группировки меню */
.nav-item {
    position: relative;
}

.nav-item:not(:last-child) {
    margin-bottom: 0.5rem;
}


/* Стили для выпадающих меню */
#blogChevron, #settingsChevron {
    transition: transform 0.3s ease;
}

#blogChevron.rotated, #settingsChevron.rotated {
    transform: rotate(180deg);
}

/* Стили для подпунктов */
.nav-item .nav-link.text-white-50 {
    padding: 0.5rem 1rem;
    font-size: 0.9rem;
    border-radius: 0.25rem;
    margin-bottom: 0.25rem;
}

.nav-item .nav-link.text-white-50:hover {
    background-color: rgba(255, 255, 255, 0.1);
    color: white !important;
}

.nav-item .nav-link.text-white-50.active {
    background-color: #0d6efd !important;
    color: white !important;
}

</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Анимация стрелки для выпадающего меню блога
    const blogToggle = document.querySelector('[data-bs-target="#blogSubmenu"]');
    const blogChevron = document.getElementById('blogChevron');
    
    if (blogToggle && blogChevron) {
        blogToggle.addEventListener('click', function() {
            blogChevron.classList.toggle('rotated');
        });
        
        const blogSubmenu = document.getElementById('blogSubmenu');
        if (blogSubmenu && blogSubmenu.classList.contains('show')) {
            blogChevron.classList.add('rotated');
        }
    }
    
    // Анимация стрелки для выпадающего меню настроек
    const settingsToggle = document.querySelector('[data-bs-target="#settingsSubmenu"]');
    const settingsChevron = document.getElementById('settingsChevron');
    
    if (settingsToggle && settingsChevron) {
        settingsToggle.addEventListener('click', function() {
            settingsChevron.classList.toggle('rotated');
        });
        
        const settingsSubmenu = document.getElementById('settingsSubmenu');
        if (settingsSubmenu && settingsSubmenu.classList.contains('show')) {
            settingsChevron.classList.add('rotated');
        }
    }
});
</script>

