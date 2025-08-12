@extends('landing.layouts.app')

@section('title', 'Возможности - Trimora')
@section('description', 'Узнайте о всех возможностях системы управления салоном красоты Trimora')

@section('content')
<!-- Hero Section -->
<section class="bg-light py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold mb-4">Возможности системы</h1>
                <p class="lead text-muted">Все инструменты для эффективного управления вашим салоном красоты</p>
            </div>
        </div>
    </div>
</section>

<!-- Features Content -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-5">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Внимание:</strong> Эта страница находится в разработке. Полное описание всех возможностей будет добавлено в ближайшее время.
                        </div>
                        
                        <h2 class="fw-bold mb-4">Основные функции</h2>
                        
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-start">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-2">Управление записями</h5>
                                        <p class="text-muted small">Онлайн-бронирование, календарь записей, управление расписанием</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-start">
                                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-2">База клиентов</h5>
                                        <p class="text-muted small">Хранение данных клиентов, история посещений, предпочтения</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-start">
                                    <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        <i class="fas fa-chart-line"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-2">Аналитика и отчеты</h5>
                                        <p class="text-muted small">Статистика посещений, финансовые отчеты, анализ эффективности</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-start">
                                    <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        <i class="fas fa-cog"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-2">Настройки и интеграции</h5>
                                        <p class="text-muted small">Гибкая настройка под ваш бизнес, интеграция с популярными сервисами</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <h3 class="fw-bold mb-3">Дополнительные возможности</h3>
                        <ul class="text-muted mb-4">
                            <li>Мобильное приложение для клиентов</li>
                            <li>Система уведомлений и напоминаний</li>
                            <li>Управление персоналом и расписанием</li>
                            <li>Интеграция с платежными системами</li>
                            <li>API для разработчиков</li>
                        </ul>
                        
                        <div class="text-center mt-5">
                            <a href="{{ route('beautyflow.pricing') }}" class="btn btn-primary me-3">
                                <i class="fas fa-tags me-2"></i>
                                Посмотреть тарифы
                            </a>
                            <a href="{{ route('beautyflow.contact') }}" class="btn btn-outline-primary">
                                <i class="fas fa-envelope me-2"></i>
                                Узнать больше
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
