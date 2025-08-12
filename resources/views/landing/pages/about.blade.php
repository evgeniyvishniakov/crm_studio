@extends('landing.layouts.app')

@section('title', 'О нас - Trimora')
@section('description', 'Узнайте больше о компании Trimora и нашей миссии')

@section('content')
<!-- Hero Section -->
<section class="bg-light py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold mb-4">О нас</h1>
                <p class="lead text-muted">Мы помогаем салонам красоты расти и развиваться</p>
            </div>
        </div>
    </div>
</section>

<!-- About Content -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-5">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Внимание:</strong> Эта страница находится в разработке. Полная информация о компании будет добавлена в ближайшее время.
                        </div>
                        
                        <h2 class="fw-bold mb-4">Наша миссия</h2>
                        <p class="text-muted mb-4">
                            Мы создаем инновационные решения для управления салонами красоты, помогая предпринимателям сосредоточиться на том, что они делают лучше всего.
                        </p>
                        
                        <h3 class="fw-bold mb-3">Что мы предлагаем</h3>
                        <ul class="text-muted mb-4">
                            <li>Система управления записями</li>
                            <li>Управление клиентской базой</li>
                            <li>Аналитика и отчеты</li>
                            <li>Интеграция с популярными сервисами</li>
                        </ul>
                        
                        <h3 class="fw-bold mb-3">Наши ценности</h3>
                        <ul class="text-muted mb-4">
                            <li>Инновации в каждой детали</li>
                            <li>Поддержка клиентов 24/7</li>
                            <li>Безопасность данных</li>
                            <li>Простота использования</li>
                        </ul>
                        
                        <div class="text-center mt-5">
                            <a href="{{ route('beautyflow.contact') }}" class="btn btn-primary">
                                <i class="fas fa-handshake me-2"></i>
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
