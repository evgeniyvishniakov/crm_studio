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
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('beautyflow.index') }}">Главная</a>
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
                <li class="nav-item">
                    <a class="btn btn-primary" href="{{ route('dashboard') }}">Войти в систему</a>
                </li>
            </ul>
        </div>
    </div>
</header> 
