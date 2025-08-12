@extends('landing.layouts.app')

@section('title', 'Условия использования - Trimora')
@section('description', 'Условия использования сервиса Trimora')

@section('content')
<!-- Hero Section -->
<section class="bg-light py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold mb-4">Условия использования</h1>
                <p class="lead text-muted">Правила и условия использования нашего сервиса</p>
            </div>
        </div>
    </div>
</section>

<!-- Terms Content -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-5">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Внимание:</strong> Эта страница находится в разработке. Полные условия использования будут добавлены в ближайшее время.
                        </div>
                        
                        <h2 class="fw-bold mb-4">Общие положения</h2>
                        <p class="text-muted mb-4">
                            Используя наш сервис, вы соглашаетесь с настоящими условиями использования.
                        </p>
                        
                        <h3 class="fw-bold mb-3">Принятие условий</h3>
                        <p class="text-muted mb-4">
                            Регистрируясь в системе, вы автоматически принимаете все условия использования сервиса.
                        </p>
                        
                        <h3 class="fw-bold mb-3">Права и обязанности</h3>
                        <ul class="text-muted mb-4">
                            <li>Использовать сервис только в законных целях</li>
                            <li>Не нарушать права других пользователей</li>
                            <li>Соблюдать технические требования системы</li>
                        </ul>
                        
                        <h3 class="fw-bold mb-3">Ограничения ответственности</h3>
                        <p class="text-muted mb-4">
                            Мы стремимся обеспечить стабильную работу сервиса, но не гарантируем его бесперебойную работу.
                        </p>
                        
                        <div class="text-center mt-5">
                            <a href="{{ route('beautyflow.contact') }}" class="btn btn-primary">
                                <i class="fas fa-question-circle me-2"></i>
                                Задать вопрос
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
