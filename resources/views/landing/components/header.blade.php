<header class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="{{ route('beautyflow.index') }}">
            <i class="fas fa-spa text-primary"></i>
            <span class="ms-2 fw-bold">CRM Studio</span>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Переключить навигацию">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav" role="navigation" aria-label="Главная навигация">
            <ul class="navbar-nav me-auto" role="menubar">
                <li class="nav-item" role="none">
                    <a class="nav-link" href="{{ route('beautyflow.index') }}" role="menuitem">Главная</a>
                </li>
                <li class="nav-item" role="none">
                    <a class="nav-link" href="{{ route('beautyflow.features') }}" role="menuitem">Возможности</a>
                </li>
                <li class="nav-item" role="none">
                    <a class="nav-link" href="{{ route('beautyflow.services') }}" role="menuitem">Услуги</a>
                </li>
                <li class="nav-item" role="none">
                    <a class="nav-link" href="{{ route('beautyflow.pricing') }}" role="menuitem">Тарифы</a>
                </li>
                <li class="nav-item" role="none">
                    <a class="nav-link" href="#" role="menuitem">База знаний</a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="btn btn-outline-primary me-2" href="{{ route('login') }}" aria-label="Войти в систему">Войти</a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-primary" href="#" data-bs-toggle="modal" data-bs-target="#registerModal" aria-label="Открыть форму регистрации">Попробовать бесплатно</a>
                </li>
            </ul>
        </div>
    </div>
</header> 
