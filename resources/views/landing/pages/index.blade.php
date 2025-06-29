@extends('landing.layouts.app')

@section('title', 'CRM Studio - Система управления салоном красоты')
@section('description', 'Профессиональная CRM система для управления салоном красоты, записями клиентов и аналитикой')

@section('content')
<!-- Hero Section -->
<section class="hero-section bg-gradient-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">Управляйте салоном красоты эффективно</h1>
                <p class="lead mb-4">CRM Studio - это современная система управления, которая поможет вам организовать работу салона, вести клиентскую базу и увеличить прибыль.</p>
                <div class="d-flex gap-3">
                    <a href="{{ route('dashboard') }}" class="btn btn-light btn-lg">Попробовать бесплатно</a>
                    <a href="{{ route('landing.services') }}" class="btn btn-outline-light btn-lg">Узнать больше</a>
                </div>
            </div>
            <div class="col-lg-6">
                <img src="{{ asset('images/hero-dashboard.png') }}" alt="CRM Studio Dashboard" class="img-fluid rounded shadow">
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold">Возможности системы</h2>
            <p class="lead text-muted">Все необходимые инструменты для успешного управления салоном красоты</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                        <h5 class="card-title">Управление клиентами</h5>
                        <p class="card-text text-muted">Ведите базу клиентов, отслеживайте историю посещений и предпочтения каждого клиента.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="fas fa-calendar fa-2x"></i>
                        </div>
                        <h5 class="card-title">Записи и расписание</h5>
                        <p class="card-text text-muted">Удобное планирование записей, календарь мастера и автоматические напоминания.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon bg-info text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                        <h5 class="card-title">Аналитика и отчеты</h5>
                        <p class="card-text text-muted">Детальная аналитика продаж, популярных услуг и эффективности работы мастеров.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="bg-light py-5">
    <div class="container text-center">
        <h2 class="display-6 fw-bold mb-4">Готовы начать?</h2>
        <p class="lead text-muted mb-4">Присоединяйтесь к тысячам салонов красоты, которые уже используют CRM Studio</p>
        <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg">Начать бесплатно</a>
    </div>
</section>
@endsection

@push('styles')
<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.feature-icon {
    transition: transform 0.3s ease;
}

.card:hover .feature-icon {
    transform: scale(1.1);
}
</style>
@endpush 
