@php
    use Illuminate\Support\Facades\Auth;
@endphp

<header class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="{{ route('beautyflow.index') }}">
            <i class="fas fa-spa text-primary"></i>
            <span class="ms-2 fw-bold">Trimora</span>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Переключить навигацию">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav" role="navigation" aria-label="Главная навигация">
            <ul class="navbar-nav me-auto" role="menubar">
                <li class="nav-item" role="none">
                    <a class="nav-link {{ request()->routeIs('beautyflow.index') ? 'active' : '' }}" href="{{ route('beautyflow.index') }}" role="menuitem">Продукт</a>
                </li>


                <li class="nav-item" role="none">
                    <a class="nav-link {{ request()->routeIs('beautyflow.pricing') ? 'active' : '' }}" href="{{ route('beautyflow.pricing') }}" role="menuitem">Тарифы</a>
                </li>
                <li class="nav-item" role="none">
                    <a class="nav-link {{ request()->routeIs('beautyflow.knowledge') ? 'active' : '' }}" href="{{ route('beautyflow.knowledge') }}" role="menuitem">База знаний</a>
                </li>
                <li class="nav-item" role="none">
                    <a class="nav-link {{ request()->routeIs('beautyflow.contact') ? 'active' : '' }}" href="{{ route('beautyflow.contact') }}" role="menuitem">Контакты</a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <!-- Language Selector - Alteg Style -->
                <li class="nav-item me-3">
                    <div class="alteg-lang-dropdown">
                        <button class="alteg-lang-btn" id="altegLangBtn">
                            <i class="fas fa-globe"></i>
                            <i class="fas fa-chevron-up ms-1"></i>
                        </button>
                        <div class="alteg-lang-menu" id="altegLangMenu">
                            <a href="?lang=en" class="alteg-lang-item {{ app()->getLocale() === 'en' ? 'active' : '' }}">English</a>
                            <span class="alteg-divider">|</span>
                            <a href="?lang=ru" class="alteg-lang-item {{ app()->getLocale() === 'ru' ? 'active' : '' }}">Русский</a>
                            <span class="alteg-divider">|</span>
                            <a href="?lang=ua" class="alteg-lang-item {{ app()->getLocale() === 'ua' ? 'active' : '' }}">Українська</a>
                        </div>
                    </div>
                </li>
                
                @if(Auth::guard('client')->check() && Auth::guard('client')->user()->role === 'admin')
                    <li class="nav-item">
                        <a class="btn btn-outline-primary me-2" href="{{ route('landing.account.dashboard') }}" aria-label="Личный кабинет">
                            <i class="fas fa-user me-1"></i>Личный кабинет
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('landing.account.logout') }}" class="btn btn-outline-danger" aria-label="Выйти">
                            <i class="fas fa-sign-out-alt me-1"></i>Выйти
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="btn btn-outline-primary me-2" href="#" data-bs-toggle="modal" data-bs-target="#loginModal" aria-label="Открыть форму входа">Войти</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary" href="#" data-bs-toggle="modal" data-bs-target="#registerModal" aria-label="Открыть форму регистрации">Попробовать бесплатно</a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const altegLangBtn = document.getElementById('altegLangBtn');
    const altegLangMenu = document.getElementById('altegLangMenu');
    const altegLangDropdown = document.querySelector('.alteg-lang-dropdown');
    
    if (altegLangBtn && altegLangMenu) {
        altegLangBtn.addEventListener('click', function(e) {
            e.preventDefault();
            altegLangDropdown.classList.toggle('open');
        });
        
        // Close on click outside
        document.addEventListener('click', function(e) {
            if (!altegLangDropdown.contains(e.target)) {
                altegLangDropdown.classList.remove('open');
            }
        });
        
        // Close on escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                altegLangDropdown.classList.remove('open');
            }
        });
    }
});
</script>
