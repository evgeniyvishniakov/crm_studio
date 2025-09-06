@php
    use Illuminate\Support\Facades\Auth;
@endphp

<header class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="{{ \App\Helpers\LanguageHelper::createSeoUrl('beautyflow.index') }}">
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
                    <a class="nav-link {{ request()->routeIs('beautyflow.pricing') ? 'active' : '' }}" href="{{ \App\Helpers\LanguageHelper::createSeoUrl('beautyflow.pricing') }}" role="menuitem">{{ __('landing.pricing') }}</a>
                </li>
                <li class="nav-item" role="none">
                    <a class="nav-link {{ request()->routeIs('beautyflow.knowledge') ? 'active' : '' }}" href="{{ \App\Helpers\LanguageHelper::createSeoUrl('beautyflow.knowledge') }}" role="menuitem">{{ __('landing.knowledge_base') }}</a>
                </li>
                <li class="nav-item" role="none">
                    <a class="nav-link {{ request()->routeIs('beautyflow.blog*') ? 'active' : '' }}" href="{{ \App\Helpers\LanguageHelper::createSeoUrl('beautyflow.blog') }}" role="menuitem">{{ __('landing.blog') }}</a>
                </li>
                <li class="nav-item" role="none">
                    <a class="nav-link {{ request()->routeIs('beautyflow.contact') ? 'active' : '' }}" href="{{ \App\Helpers\LanguageHelper::createSeoUrl('beautyflow.contact') }}" role="menuitem">{{ __('landing.contacts') }}</a>
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
                            <a href="#" data-lang="en" class="alteg-lang-item {{ app()->getLocale() === 'en' ? 'active' : '' }}">English</a>
                            <span class="alteg-divider">|</span>
                            <a href="#" data-lang="ru" class="alteg-lang-item {{ app()->getLocale() === 'ru' ? 'active' : '' }}">Русский</a>
                            <span class="alteg-divider">|</span>
                            <a href="#" data-lang="ua" class="alteg-lang-item {{ app()->getLocale() === 'ua' ? 'active' : '' }}">Українська</a>
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
        
        document.addEventListener('click', function(e) {
            if (!altegLangDropdown.contains(e.target)) {
                altegLangDropdown.classList.remove('open');
            }
        });
        
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                altegLangDropdown.classList.remove('open');
            }
        });
    }
    
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

    // Обработка смены языка
    document.querySelectorAll('[data-lang]').forEach(langLink => {
        langLink.addEventListener('click', function(e) {
            e.preventDefault();
            const langCode = this.getAttribute('data-lang');
            
            // Получаем текущий путь
            const currentPath = window.location.pathname;
            
            // Определяем новый путь на основе текущего пути и выбранного языка
            let newPath = '';
            
            if (currentPath === '/beautyflow' || currentPath === '/beautyflow/') {
                // Главная страница
                newPath = langCode === 'ua' ? '/beautyflow' : `/beautyflow/${langCode}`;
            } else if (currentPath.match(/^\/beautyflow\/[a-z]{2}$/)) {
                // Главная страница с языковым префиксом
                newPath = langCode === 'ua' ? '/beautyflow' : `/beautyflow/${langCode}`;
            } else if (currentPath.match(/^\/beautyflow\/[a-z]{2}\/blog$/)) {
                // Блог с языковым префиксом
                newPath = langCode === 'ua' ? '/beautyflow/blog' : `/beautyflow/${langCode}/blog`;
            } else if (currentPath.match(/^\/beautyflow\/blog$/)) {
                // Блог без префикса
                newPath = langCode === 'ua' ? '/beautyflow/blog' : `/beautyflow/${langCode}/blog`;
            } else if (currentPath.match(/^\/beautyflow\/[a-z]{2}\/blog\/(.+)$/)) {
                // Статья блога с языковым префиксом
                const slug = currentPath.match(/^\/beautyflow\/[a-z]{2}\/blog\/(.+)$/)[1];
                newPath = langCode === 'ua' ? `/beautyflow/blog/${slug}` : `/beautyflow/${langCode}/blog/${slug}`;
            } else if (currentPath.match(/^\/beautyflow\/blog\/(.+)$/)) {
                // Статья блога без префикса
                const slug = currentPath.match(/^\/beautyflow\/blog\/(.+)$/)[1];
                newPath = langCode === 'ua' ? `/beautyflow/blog/${slug}` : `/beautyflow/${langCode}/blog/${slug}`;
            } else if (currentPath.match(/^\/beautyflow\/[a-z]{2}\/contact$/)) {
                // Контакты с языковым префиксом
                newPath = langCode === 'ua' ? '/beautyflow/contact' : `/beautyflow/${langCode}/contact`;
            } else if (currentPath.match(/^\/beautyflow\/contact$/)) {
                // Контакты без префикса
                newPath = langCode === 'ua' ? '/beautyflow/contact' : `/beautyflow/${langCode}/contact`;
            } else if (currentPath.match(/^\/beautyflow\/[a-z]{2}\/pricing$/)) {
                // Цены с языковым префиксом
                newPath = langCode === 'ua' ? '/beautyflow/pricing' : `/beautyflow/${langCode}/pricing`;
            } else if (currentPath.match(/^\/beautyflow\/pricing$/)) {
                // Цены без префикса
                newPath = langCode === 'ua' ? '/beautyflow/pricing' : `/beautyflow/${langCode}/pricing`;
            } else if (currentPath.match(/^\/beautyflow\/[a-z]{2}\/knowledge$/)) {
                // База знаний с языковым префиксом
                newPath = langCode === 'ua' ? '/beautyflow/knowledge' : `/beautyflow/${langCode}/knowledge`;
            } else if (currentPath.match(/^\/beautyflow\/knowledge$/)) {
                // База знаний без префикса
                newPath = langCode === 'ua' ? '/beautyflow/knowledge' : `/beautyflow/${langCode}/knowledge`;
            } else if (currentPath.match(/^\/beautyflow\/[a-z]{2}\/knowledge\/(.+)$/)) {
                // Статья базы знаний с языковым префиксом
                const slug = currentPath.match(/^\/beautyflow\/[a-z]{2}\/knowledge\/(.+)$/)[1];
                newPath = langCode === 'ua' ? `/beautyflow/knowledge/${slug}` : `/beautyflow/${langCode}/knowledge/${slug}`;
            } else if (currentPath.match(/^\/beautyflow\/knowledge\/(.+)$/)) {
                // Статья базы знаний без префикса
                const slug = currentPath.match(/^\/beautyflow\/knowledge\/(.+)$/)[1];
                newPath = langCode === 'ua' ? `/beautyflow/knowledge/${slug}` : `/beautyflow/${langCode}/knowledge/${slug}`;
            } else if (currentPath.match(/^\/beautyflow\/[a-z]{2}\/privacy$/)) {
                // Политика конфиденциальности с языковым префиксом
                newPath = langCode === 'ua' ? '/beautyflow/privacy' : `/beautyflow/${langCode}/privacy`;
            } else if (currentPath.match(/^\/beautyflow\/privacy$/)) {
                // Политика конфиденциальности без префикса
                newPath = langCode === 'ua' ? '/beautyflow/privacy' : `/beautyflow/${langCode}/privacy`;
            } else if (currentPath.match(/^\/beautyflow\/[a-z]{2}\/terms$/)) {
                // Условия использования с языковым префиксом
                newPath = langCode === 'ua' ? '/beautyflow/terms' : `/beautyflow/${langCode}/terms`;
            } else if (currentPath.match(/^\/beautyflow\/terms$/)) {
                // Условия использования без префикса
                newPath = langCode === 'ua' ? '/beautyflow/terms' : `/beautyflow/${langCode}/terms`;
            } else {
                // Fallback - для маршрутов без языковых версий используем параметр lang
                const currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('lang', langCode);
                
                // Устанавливаем язык в сессии перед переходом
                fetch('/beautyflow/set-language', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        language: langCode
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Переходим на новый URL
                        window.location.href = currentUrl.toString();
                    } else {
                        console.error('Failed to set language:', data.error);
                        // Переходим на новый URL в любом случае
                        window.location.href = currentUrl.toString();
                    }
                })
                .catch(error => {
                    console.error('Error setting language:', error);
                    // Переходим на новый URL в любом случае
                    window.location.href = currentUrl.toString();
                });
                return;
            }
            
            // Устанавливаем язык в сессии перед переходом
            fetch('/beautyflow/set-language', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    language: langCode
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Переходим на новый URL
                    window.location.href = newPath;
                } else {
                    console.error('Failed to set language:', data.error);
                    // Переходим на новый URL в любом случае
                    window.location.href = newPath;
                }
            })
            .catch(error => {
                console.error('Error setting language:', error);
                // Переходим на новый URL в любом случае
                window.location.href = newPath;
            });
        });
    });
    
    // Функция показа уведомления о смене языка
    function showLanguageChangeNotification(langCode) {
        const languageNames = {
            'en': 'English',
            'ru': 'Русский',
            'ua': 'Українська'
        };
        
        const notification = document.createElement('div');
        notification.className = 'alert alert-success alert-dismissible fade show';
        notification.style.position = 'fixed';
        notification.style.top = '20px';
        notification.style.right = '20px';
        notification.style.zIndex = '9999';
        notification.style.maxWidth = '400px';
        
        notification.innerHTML = `
            <i class="fas fa-check-circle"></i>
            <strong>${languageNames[langCode] || langCode}</strong><br>
            <small>${languageMessages[langCode]?.language_synced || languageMessages['ua'].language_synced}</small>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        `;
        
        document.body.appendChild(notification);
        
        // Автоматически скрываем через 3 секунды
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 3000);
    }
});
</script>
