@extends('landing.layouts.app')

@section('title', 'Услуги - Trimora')
@section('description', 'Подробное описание всех возможностей Trimora для управления салоном красоты')

@section('content')
<!-- Hero Section -->
<section class="bg-light py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold mb-4">Наши услуги</h1>
                <p class="lead text-muted">Полный набор инструментов для эффективного управления салоном красоты</p>
            </div>
        </div>
    </div>
</section>

<!-- Services Grid -->
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <!-- Управление клиентами -->
            <div class="col-lg-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px;">
                                <i class="fas fa-users fa-2x"></i>
                            </div>
                            <h3 class="card-title mb-0">Управление клиентами</h3>
                        </div>
                        <p class="card-text text-muted mb-3">Ведите полную базу клиентов с историей посещений, предпочтениями и контактной информацией.</p>
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>База данных клиентов</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>История посещений</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Предпочтения и заметки</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Сегментация клиентов</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Записи и расписание -->
            <div class="col-lg-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px;">
                                <i class="fas fa-calendar fa-2x"></i>
                            </div>
                            <h3 class="card-title mb-0">Записи и расписание</h3>
                        </div>
                        <p class="card-text text-muted mb-3">Удобное планирование записей с календарем мастера и автоматическими напоминаниями.</p>
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Календарь записей</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Расписание мастеров</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Автоматические напоминания</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Онлайн-запись</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Управление товарами -->
            <div class="col-lg-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px;">
                                <i class="fas fa-box fa-2x"></i>
                            </div>
                            <h3 class="card-title mb-0">Управление товарами</h3>
                        </div>
                        <p class="card-text text-muted mb-3">Контролируйте складские остатки, закупки и продажи товаров с автоматическим учетом.</p>
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Складской учет</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Закупки и поставщики</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Продажи товаров</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Инвентаризация</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Аналитика и отчеты -->
            <div class="col-lg-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px;">
                                <i class="fas fa-chart-line fa-2x"></i>
                            </div>
                            <h3 class="card-title mb-0">Аналитика и отчеты</h3>
                        </div>
                        <p class="card-text text-muted mb-3">Детальная аналитика продаж, популярных услуг и эффективности работы мастеров.</p>
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Финансовая отчетность</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Анализ продаж</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Статистика услуг</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Эффективность мастеров</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Pricing Section -->
<section class="bg-light py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold">Тарифные планы</h2>
            <p class="lead text-muted">Выберите подходящий план для вашего салона</p>
        </div>
        
        <div class="row g-4 justify-content-center">
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 text-center">
                        <h4 class="card-title">Старт</h4>
                        <div class="display-4 fw-bold text-primary mb-3">0₽</div>
                        <p class="text-muted mb-4">Бесплатно навсегда</p>
                        <ul class="list-unstyled mb-4">
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>До 50 клиентов</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Базовые отчеты</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Email поддержка</li>
                        </ul>
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-primary w-100">Начать бесплатно</a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card border-0 shadow border-primary">
                    <div class="card-body p-4 text-center">
                        <div class="badge bg-primary mb-2">Популярный</div>
                        <h4 class="card-title">Про</h4>
                        <div class="display-4 fw-bold text-primary mb-3">2999₽</div>
                        <p class="text-muted mb-4">в месяц</p>
                        <ul class="list-unstyled mb-4">
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Неограниченное количество клиентов</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Все функции</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Приоритетная поддержка</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Интеграции</li>
                        </ul>
                        <a href="{{ route('dashboard') }}" class="btn btn-primary w-100">Выбрать план</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5">
    <div class="container text-center">
        <h2 class="display-6 fw-bold mb-4">Готовы попробовать?</h2>
        <p class="lead text-muted mb-4">Начните использовать CRM Studio уже сегодня</p>
        <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg">Начать бесплатно</a>
    </div>
</section>
@endsection 
