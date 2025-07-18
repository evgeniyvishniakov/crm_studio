@php
    $user = auth()->user();
@endphp
@if($user && !empty($user->is_panel_admin))
<header class="bg-white border-bottom shadow-sm p-3">
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <button class="btn btn-link text-dark d-lg-none me-3" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <h4 class="mb-0">@yield('page-title', 'Админ панель')</h4>
        </div>
        
        <div class="d-flex align-items-center">
            <!-- Уведомления -->
            <div class="dropdown me-3">
                <button class="btn btn-link text-dark position-relative" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-bell"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        3
                    </span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><h6 class="dropdown-header">Уведомления</h6></li>
                    <li><a class="dropdown-item" href="#">Новый пользователь зарегистрирован</a></li>
                    <li><a class="dropdown-item" href="#">Обновление системы доступно</a></li>
                    <li><a class="dropdown-item" href="#">Резервная копия создана</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#">Показать все</a></li>
                </ul>
            </div>
            
            <!-- Профиль пользователя -->
            <div class="dropdown">
                <button class="btn btn-link text-dark d-flex align-items-center" type="button" data-bs-toggle="dropdown">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                        <i class="fas fa-user"></i>
                    </div>
                    <span>{{ auth()->user()->name ?? 'Администратор' }}</span>
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
