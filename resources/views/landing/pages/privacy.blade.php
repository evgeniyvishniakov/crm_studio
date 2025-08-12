@extends('landing.layouts.app')

@section('title', 'Политика конфиденциальности - Trimora')
@section('description', 'Политика конфиденциальности и обработки персональных данных Trimora')

@section('content')
<!-- Hero Section -->
<section class="bg-light py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold mb-4">Политика конфиденциальности</h1>
                <p class="lead text-muted">Информация о том, как мы защищаем ваши персональные данные</p>
            </div>
        </div>
    </div>
</section>

<!-- Privacy Content -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-5">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Внимание:</strong> Эта страница находится в разработке. Полная политика конфиденциальности будет добавлена в ближайшее время.
                        </div>
                        
                        <h2 class="fw-bold mb-4">Основные принципы</h2>
                        <p class="text-muted mb-4">
                            Мы серьезно относимся к защите ваших персональных данных и соблюдаем все требования законодательства о персональных данных.
                        </p>
                        
                        <h3 class="fw-bold mb-3">Что мы собираем</h3>
                        <ul class="text-muted mb-4">
                            <li>Контактная информация (имя, email, телефон)</li>
                            <li>Информация об использовании сервиса</li>
                            <li>Технические данные (IP-адрес, тип браузера)</li>
                        </ul>
                        
                        <h3 class="fw-bold mb-3">Как мы используем данные</h3>
                        <ul class="text-muted mb-4">
                            <li>Для предоставления услуг</li>
                            <li>Для улучшения качества сервиса</li>
                            <li>Для связи с вами</li>
                        </ul>
                        
                        <h3 class="fw-bold mb-3">Безопасность</h3>
                        <p class="text-muted mb-4">
                            Мы используем современные технологии шифрования и защиты данных для обеспечения безопасности вашей информации.
                        </p>
                        
                        <div class="text-center mt-5">
                            <a href="{{ route('beautyflow.contact') }}" class="btn btn-primary">
                                <i class="fas fa-envelope me-2"></i>
                                Связаться с нами
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
