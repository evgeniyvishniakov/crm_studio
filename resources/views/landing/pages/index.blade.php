@extends('landing.layouts.app')

@section('title', 'CRM Studio - Система управления салоном красоты')
@section('description', 'Профессиональная CRM система для управления салоном красоты, записями клиентов и аналитикой')

@section('content')

<!-- Hero Section -->
<section class="hero-section py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 animate-fade-in-left">
                <h1 class="display-4 fw-bold mb-4 text-dark gradient-text">Управляйте салоном красоты эффективно</h1>
                <p class="lead mb-4 text-muted typing-effect" data-text="CRM Studio - это современная система управления, которая поможет вам организовать работу салона, вести клиентскую базу и увеличить прибыль.">CRM Studio - это современная система управления, которая поможет вам организовать работу салона, вести клиентскую базу и увеличить прибыль.</p>
                <div class="d-flex gap-3">
                    <a href="#" class="btn btn-primary btn-lg animate-pulse" data-bs-toggle="modal" data-bs-target="#registerModal">
                        <i class="fas fa-rocket me-2"></i>Попробовать бесплатно
                    </a>
                    <a href="{{ route('beautyflow.demo') }}" class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-play me-2"></i>Посмотреть демо
                    </a>
                </div>
            </div>
            <div class="col-lg-6 animate-fade-in-right">
                <div class="card border-0 shadow-lg card-3d">
                    <div class="card-body p-0">
                        <div class="bg-gradient-primary text-white p-4 rounded-top">
                            <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Панель управления</h5>
                        </div>
                        <div class="p-4">
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="stat-card bg-success-light interactive-element">
                                        <div class="stat-icon bg-flat-color-1">
                                            <i class="fas fa-coins"></i>
                                        </div>
                                        <div class="stat-content">
                                            <h3 class="stat-title">Прибыль</h3>
                                            <p class="stat-value counter" data-target="125400">₽125,400</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stat-card bg-info-light interactive-element">
                                        <div class="stat-icon bg-flat-color-3">
                                            <i class="fas fa-users"></i>
                                        </div>
                                        <div class="stat-content">
                                            <h3 class="stat-title">Клиенты</h3>
                                            <p class="stat-value counter" data-target="1247">1,247</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stat-card bg-warning-light interactive-element">
                                        <div class="stat-icon bg-flat-color-4">
                                            <i class="fas fa-calendar-check"></i>
                                        </div>
                                        <div class="stat-content">
                                            <h3 class="stat-title">Записи</h3>
                                            <p class="stat-value counter" data-target="89">89</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stat-card bg-primary-light interactive-element">
                                        <div class="stat-icon bg-flat-color-2">
                                            <i class="fas fa-shopping-cart"></i>
                                        </div>
                                        <div class="stat-content">
                                            <h3 class="stat-title">Продажи</h3>
                                            <p class="stat-value counter" data-target="45200">₽45,200</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Плавающие элементы -->
    <div class="floating-elements">
        <div class="floating-element" style="top: 20%; left: 10%; animation-delay: 0s;"></div>
        <div class="floating-element" style="top: 60%; right: 15%; animation-delay: 2s;"></div>
        <div class="floating-element" style="top: 80%; left: 20%; animation-delay: 4s;"></div>
    </div>
</section>

<!-- Features Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-lg-8 mx-auto">
                <h2 class="display-5 fw-bold mb-4 section-title">Все возможности в одной системе</h2>
                <p class="lead text-muted">Полный набор инструментов для эффективного управления салоном красоты</p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-4 col-md-6 feature-item">
                <div class="card h-100 border-0 shadow-sm card-3d">
                    <div class="card-body text-center p-4">
                        <div class="stat-icon bg-flat-color-1 mx-auto mb-3 feature-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h4 class="card-title">Управление клиентами</h4>
                        <p class="card-text text-muted">Ведите базу клиентов, отслеживайте историю посещений, управляйте типами клиентов и анализируйте их поведение.</p>
                        <ul class="list-unstyled text-start">
                            <li><i class="fas fa-check text-success me-2"></i>База клиентов</li>
                            <li><i class="fas fa-check text-success me-2"></i>История посещений</li>
                            <li><i class="fas fa-check text-success me-2"></i>Типы клиентов</li>
                            <li><i class="fas fa-check text-success me-2"></i>Аналитика по клиентам</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 feature-item">
                <div class="card h-100 border-0 shadow-sm card-3d">
                    <div class="card-body text-center p-4">
                        <div class="stat-icon bg-flat-color-3 mx-auto mb-3 feature-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <h4 class="card-title">Записи и расписание</h4>
                        <p class="card-text text-muted">Управляйте записями клиентов, создавайте расписание мастеров, отслеживайте статусы и отправляйте уведомления.</p>
                        <ul class="list-unstyled text-start">
                            <li><i class="fas fa-check text-success me-2"></i>Календарь записей</li>
                            <li><i class="fas fa-check text-success me-2"></i>Расписание мастеров</li>
                            <li><i class="fas fa-check text-success me-2"></i>Статусы записей</li>
                            <li><i class="fas fa-check text-success me-2"></i>Уведомления</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 feature-item">
                <div class="card h-100 border-0 shadow-sm card-3d">
                    <div class="card-body text-center p-4">
                        <div class="stat-icon bg-flat-color-2 mx-auto mb-3 feature-icon">
                            <i class="fas fa-boxes"></i>
                        </div>
                        <h4 class="card-title">Управление товарами</h4>
                        <p class="card-text text-muted">Ведите складской учет, управляйте закупками и продажами, проводите инвентаризацию и анализируйте товарооборот.</p>
                        <ul class="list-unstyled text-start">
                            <li><i class="fas fa-check text-success me-2"></i>Складской учет</li>
                            <li><i class="fas fa-check text-success me-2"></i>Закупки и продажи</li>
                            <li><i class="fas fa-check text-success me-2"></i>Инвентаризация</li>
                            <li><i class="fas fa-check text-success me-2"></i>Аналитика товаров</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 feature-item">
                <div class="card h-100 border-0 shadow-sm card-3d">
                    <div class="card-body text-center p-4">
                        <div class="stat-icon bg-flat-color-5 mx-auto mb-3 feature-icon">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <h4 class="card-title">Аналитика и отчеты</h4>
                        <p class="card-text text-muted">Получайте детальную аналитику по всем аспектам работы салона, создавайте отчеты и отслеживайте ключевые показатели.</p>
                        <ul class="list-unstyled text-start">
                            <li><i class="fas fa-check text-success me-2"></i>Финансовая аналитика</li>
                            <li><i class="fas fa-check text-success me-2"></i>Аналитика клиентов</li>
                            <li><i class="fas fa-check text-success me-2"></i>Аналитика сотрудников</li>
                            <li><i class="fas fa-check text-success me-2"></i>Детальные отчеты</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 feature-item">
                <div class="card h-100 border-0 shadow-sm card-3d">
                    <div class="card-body text-center p-4">
                        <div class="stat-icon bg-flat-color-4 mx-auto mb-3 feature-icon">
                            <i class="fas fa-plug"></i>
                        </div>
                        <h4 class="card-title">Интеграции</h4>
                        <p class="card-text text-muted">Подключайте дополнительные сервисы для автоматизации работы: Telegram уведомления, email рассылки, виджеты для сайта.</p>
                        <ul class="list-unstyled text-start">
                            <li><i class="fas fa-check text-success me-2"></i>Telegram уведомления</li>
                            <li><i class="fas fa-check text-success me-2"></i>Email рассылки</li>
                            <li><i class="fas fa-check text-success me-2"></i>Виджет для сайта</li>
                            <li><i class="fas fa-check text-success me-2"></i>Веб-запись</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 feature-item">
                <div class="card h-100 border-0 shadow-sm card-3d">
                    <div class="card-body text-center p-4">
                        <div class="stat-icon bg-flat-color-6 mx-auto mb-3 feature-icon">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <h4 class="card-title">Настройки и роли</h4>
                        <p class="card-text text-muted">Настраивайте систему под свои нужды, создавайте роли и права доступа, управляйте пользователями и настройками.</p>
                        <ul class="list-unstyled text-start">
                            <li><i class="fas fa-check text-success me-2"></i>Роли и права доступа</li>
                            <li><i class="fas fa-check text-success me-2"></i>Управление пользователями</li>
                            <li><i class="fas fa-check text-success me-2"></i>Настройки системы</li>
                            <li><i class="fas fa-check text-success me-2"></i>Безопасность</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Statistics Section -->
<section class="py-5">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-lg-8 mx-auto">
                <h2 class="display-5 fw-bold mb-4 section-title">Почему выбирают CRM Studio</h2>
                <p class="lead text-muted">Тысячи салонов красоты уже используют нашу систему</p>
            </div>
        </div>
        
        <div class="row g-4 text-center">
            <div class="col-lg-3 col-md-6 scroll-reveal">
                <div class="card border-0 shadow-sm magnetic">
                    <div class="card-body p-4">
                        <div class="stat-icon bg-flat-color-1 mx-auto mb-3">
                            <i class="fas fa-store"></i>
                        </div>
                        <h3 class="stat-value counter" data-target="500">500+</h3>
                        <p class="stat-title">Салонов красоты</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 scroll-reveal">
                <div class="card border-0 shadow-sm magnetic">
                    <div class="card-body p-4">
                        <div class="stat-icon bg-flat-color-3 mx-auto mb-3">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3 class="stat-value counter" data-target="50000">50,000+</h3>
                        <p class="stat-title">Клиентов</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 scroll-reveal">
                <div class="card border-0 shadow-sm magnetic">
                    <div class="card-body p-4">
                        <div class="stat-icon bg-flat-color-5 mx-auto mb-3">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <h3 class="stat-value counter" data-target="1000000">1M+</h3>
                        <p class="stat-title">Записей</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 scroll-reveal">
                <div class="card border-0 shadow-sm magnetic">
                    <div class="card-body p-4">
                        <div class="stat-icon bg-flat-color-2 mx-auto mb-3">
                            <i class="fas fa-star"></i>
                        </div>
                        <h3 class="stat-value">4.9</h3>
                        <p class="stat-title">Рейтинг</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="bg-gradient-primary text-white py-5">
    <div class="container text-center">
        <h2 class="display-6 fw-bold mb-4">Готовы начать?</h2>
        <p class="lead mb-4">Присоединяйтесь к тысячам салонов красоты, которые уже используют CRM Studio</p>
        <a href="#" class="btn btn-light btn-lg animate-glow" data-bs-toggle="modal" data-bs-target="#registerModal">
            <i class="fas fa-rocket me-2"></i>Начать бесплатно
        </a>
    </div>
</section>

@include('landing.components.register-modal')
@endsection

@push('styles')
<style>
.bg-primary-light {
    background: rgba(171, 140, 228, 0.1);
    color: var(--secondary-color);
}

.stat-card .stat-icon {
    width: 50px;
    height: 50px;
    font-size: 1.2rem;
}

.stat-card .stat-value {
    font-size: 1.8rem;
    font-weight: 700;
    margin: 0.5rem 0;
}

.stat-card .stat-title {
    font-size: 0.9rem;
    margin: 0;
}

.card ul li {
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

/* Плавающие элементы */
.floating-elements {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    pointer-events: none;
    z-index: 1;
}

.floating-element {
    position: absolute;
    width: 20px;
    height: 20px;
    background: var(--gradient-primary);
    border-radius: 50%;
    opacity: 0.1;
    animation: float 6s ease-in-out infinite;
}

.floating-element:nth-child(2) {
    width: 30px;
    height: 30px;
    animation-delay: 2s;
}

.floating-element:nth-child(3) {
    width: 15px;
    height: 15px;
    animation-delay: 4s;
}

/* Магнитные элементы */
.magnetic {
    transition: transform 0.1s ease;
}

/* 3D карточки */
.card-3d {
    transition: transform 0.1s ease;
}

/* Градиентный текст */
.gradient-text {
    background: var(--gradient-primary);
    background-clip: text;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    transition: background 0.3s ease;
}

/* Эффект печатающегося текста */
.typing-effect {
    overflow: hidden;
    white-space: nowrap;
    border-right: 2px solid var(--primary-color);
    animation: typing 3.5s steps(40, end), blink-caret 0.75s step-end infinite;
}

@keyframes typing {
    from { width: 0; }
    to { width: 100%; }
}

@keyframes blink-caret {
    from, to { border-color: transparent; }
    50% { border-color: var(--primary-color); }
}

/* Анимации появления */
.section-title {
    opacity: 0;
    transform: translateY(30px);
    transition: all 0.8s ease;
}

.section-title.revealed {
    opacity: 1;
    transform: translateY(0);
}

.feature-item {
    opacity: 0;
    transform: translateY(30px);
    transition: all 0.8s ease;
}

.feature-item.revealed {
    opacity: 1;
    transform: translateY(0);
}

/* Дополнительные эффекты */
.btn.animate-pulse {
    animation: pulse 2s infinite;
}

.btn.animate-glow {
    animation: glow 2s ease-in-out infinite alternate;
}

/* Адаптивность */
@media (max-width: 768px) {
    .floating-elements {
        display: none;
    }
    
    .typing-effect {
        white-space: normal;
        border-right: none;
        animation: none;
    }
}
</style>
@endpush

@push('scripts')
<script src="{{ asset('landing/main.js') }}"></script>
@endpush 
