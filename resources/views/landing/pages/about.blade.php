@extends('landing.layouts.app')

@section('title', 'О нас - CRM Studio')
@section('description', 'Узнайте больше о CRM Studio и нашей миссии помочь салонам красоты развиваться')

@section('content')
<!-- Hero Section -->
<section class="bg-light py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold mb-4">О CRM Studio</h1>
                <p class="lead text-muted">Мы помогаем салонам красоты расти и развиваться с помощью современных технологий</p>
            </div>
        </div>
    </div>
</section>

<!-- About Content -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <h2 class="fw-bold mb-4">Наша миссия</h2>
                <p class="text-muted mb-4">Мы создаем простые и эффективные решения для управления салонами красоты. Наша цель - помочь владельцам салонов сосредоточиться на том, что они делают лучше всего - создавать красоту и дарить радость клиентам.</p>
                
                <h3 class="fw-bold mb-3">Почему CRM Studio?</h3>
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Простой и интуитивно понятный интерфейс</li>
                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Все необходимые функции в одном месте</li>
                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Надежная и безопасная система</li>
                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Поддержка 24/7</li>
                </ul>
            </div>
            <div class="col-lg-6">
                <img src="{{ asset('images/about-us.jpg') }}" alt="О нас" class="img-fluid rounded shadow">
            </div>
        </div>
    </div>
</section>

<!-- Team Section -->
<section class="bg-light py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Наша команда</h2>
            <p class="text-muted">Профессионалы, которые создают лучшие решения для вашего бизнеса</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm text-center">
                    <div class="card-body p-4">
                        <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-user fa-2x"></i>
                        </div>
                        <h5 class="card-title">Разработчики</h5>
                        <p class="card-text text-muted">Создают надежные и современные решения</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card border-0 shadow-sm text-center">
                    <div class="card-body p-4">
                        <div class="rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-headset fa-2x"></i>
                        </div>
                        <h5 class="card-title">Поддержка</h5>
                        <p class="card-text text-muted">Всегда готовы помочь и ответить на вопросы</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card border-0 shadow-sm text-center">
                    <div class="card-body p-4">
                        <div class="rounded-circle bg-info text-white d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                        <h5 class="card-title">Аналитики</h5>
                        <p class="card-text text-muted">Изучают потребности и улучшают продукт</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection 
