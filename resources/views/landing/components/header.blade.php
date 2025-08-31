@php
    use Illuminate\Support\Facades\Auth;
@endphp

<header class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="{{ route('beautyflow.index') }}">
            <span class="fw-bold logo-text">Trimora</span>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="{{ __('landing.toggle_navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav" role="navigation" aria-label="{{ __('landing.main_navigation') }}">
                        <ul class="navbar-nav me-auto" role="menubar">
                <li class="nav-item" role="none">
                    <a class="nav-link" href="#features-grid" role="menuitem">{{ __('landing.features') }}</a>
                </li>
                <li class="nav-item" role="none">
                    <a class="nav-link" href="#niches-section" role="menuitem">{{ __('landing.niches') }}</a>
                </li>
                <li class="nav-item" role="none">
                    <a class="nav-link {{ request()->routeIs('beautyflow.pricing') ? 'active' : '' }}" href="{{ \App\Helpers\LanguageHelper::addLanguageToUrl(route('beautyflow.pricing')) }}" role="menuitem">{{ __('landing.pricing') }}</a>
                </li>
                <li class="nav-item" role="none">
                    <a class="nav-link {{ request()->routeIs('beautyflow.knowledge') ? 'active' : '' }}" href="{{ \App\Helpers\LanguageHelper::addLanguageToUrl(route('beautyflow.knowledge')) }}" role="menuitem">{{ __('landing.knowledge_base') }}</a>
                </li>
                <li class="nav-item" role="none">
                    <a class="nav-link {{ request()->routeIs('beautyflow.contact') ? 'active' : '' }}" href="{{ \App\Helpers\LanguageHelper::addLanguageToUrl(route('beautyflow.contact')) }}" role="menuitem">{{ __('landing.contacts') }}</a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <!-- Language Selector - Простая смена через перезагрузку -->
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
                
                @if(Auth::guard('client')->check())
                    @if(Auth::guard('client')->user()->role === 'admin')
                        <li class="nav-item">
                            <a class="btn btn-outline-primary me-2" href="{{ route('landing.account.dashboard') }}" aria-label="{{ __('landing.personal_account') }}">
                                <i class="fas fa-user me-1"></i>{{ __('landing.personal_account') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('landing.account.logout') }}" class="btn btn-outline-danger" aria-label="{{ __('landing.logout') }}">
                                <i class="fas fa-sign-out-alt me-1"></i>{{ __('landing.logout') }}
                            </a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="btn btn-primary" href="{{ route('dashboard') }}" aria-label="{{ __('landing.enter_system') }}">
                                <i class="fas fa-sign-in-alt me-1"></i>{{ __('landing.enter_system') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('landing.account.logout') }}" class="btn btn-outline-danger" aria-label="{{ __('landing.logout') }}">
                                <i class="fas fa-sign-out-alt me-1"></i>{{ __('landing.logout') }}
                            </a>
                        </li>
                    @endif
                @else
                    <li class="nav-item">
                        <a class="btn btn-outline-primary me-2" href="#" data-bs-toggle="modal" data-bs-target="#loginModal" aria-label="{{ __('landing.login') }}">{{ __('landing.login') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary" href="#" data-bs-toggle="modal" data-bs-target="#registerModal" aria-label="{{ __('landing.try_free') }}">{{ __('landing.try_free') }}</a>
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
    
    // Плавный скролл к секциям
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});
</script>
