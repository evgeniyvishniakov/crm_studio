<header class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="{{ route('beautyflow.index') }}">
            <i class="fas fa-spa text-primary"></i>
            <span class="ms-2 fw-bold">CRM Studio</span>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('beautyflow.index') }}">Главная</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        Продукт
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('beautyflow.features') }}">Возможности</a></li>
                        <li><a class="dropdown-item" href="{{ route('beautyflow.demo') }}">Демо</a></li>
                        <li><a class="dropdown-item" href="{{ route('beautyflow.integrations') }}">Интеграции</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('beautyflow.pricing') }}">Тарифы</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('beautyflow.services') }}">Услуги</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('beautyflow.about') }}">О нас</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('beautyflow.contact') }}">Контакты</a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="btn btn-outline-primary me-2" href="{{ route('dashboard') }}">Войти</a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-primary" href="#" data-bs-toggle="modal" data-bs-target="#registerModal">Попробовать бесплатно</a>
                </li>
            </ul>
        </div>
    </div>
</header> 
