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
                <p class="lead mb-4 text-muted">CRM Studio - это современная система управления, которая поможет вам организовать работу салона, вести клиентскую базу и увеличить прибыль.</p>
                <div class="d-flex gap-3">
                    <a href="#" class="btn btn-primary btn-lg animate-pulse" data-bs-toggle="modal" data-bs-target="#registerModal" aria-label="Открыть форму регистрации">
                        <i class="fas fa-rocket me-2" aria-hidden="true"></i>Попробовать бесплатно
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
                                    <div class="stat-card bg-success-light interactive-element" role="region" aria-label="Статистика прибыли">
                                        <div class="stat-icon bg-flat-color-1">
                                            <i class="fas fa-coins" aria-hidden="true"></i>
                                        </div>
                                        <div class="stat-content">
                                            <h3 class="stat-title">Прибыль</h3>
                                            <p class="stat-value counter" data-target="125400" aria-live="polite">₽125,400</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stat-card bg-info-light interactive-element" role="region" aria-label="Статистика клиентов">
                                        <div class="stat-icon bg-flat-color-3">
                                            <i class="fas fa-users" aria-hidden="true"></i>
                                        </div>
                                        <div class="stat-content">
                                            <h3 class="stat-title">Клиенты</h3>
                                            <p class="stat-value counter" data-target="1247" aria-live="polite">1,247</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stat-card bg-warning-light interactive-element" role="region" aria-label="Статистика записей">
                                        <div class="stat-icon bg-flat-color-4">
                                            <i class="fas fa-calendar-check" aria-hidden="true"></i>
                                        </div>
                                        <div class="stat-content">
                                            <h3 class="stat-title">Записи</h3>
                                            <p class="stat-value counter" data-target="89" aria-live="polite">89</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stat-card bg-primary-light interactive-element" role="region" aria-label="Статистика продаж">
                                        <div class="stat-icon bg-flat-color-2">
                                            <i class="fas fa-shopping-cart" aria-hidden="true"></i>
                                        </div>
                                        <div class="stat-content">
                                            <h3 class="stat-title">Продажи</h3>
                                            <p class="stat-value counter" data-target="45200" aria-live="polite">₽45,200</p>
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

<!-- Web Booking Section -->
<section class="py-5">
    <div class="container">
        <div class="row align-items-center">
            <!-- Phone Mockup with Form -->
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="position-relative">
                    <!-- iPhone 15 Pro Max Mockup -->
                    <div class="phone-mockup mx-auto" style="max-width: 320px;">
                        <div class="iphone-15-pro-max">
                            <!-- Left Edge -->
                            <div class="iphone-edge iphone-edge-left"></div>
                            
                            <!-- Right Edge -->
                            <div class="iphone-edge iphone-edge-right"></div>
                            
                            <!-- Top Edge -->
                            <div class="iphone-edge iphone-edge-top"></div>
                            
                            <!-- Bottom Edge -->
                            <div class="iphone-edge iphone-edge-bottom"></div>
                            
                            <!-- Main Frame -->
                            <div class="iphone-frame-15">
                                <!-- Dynamic Island -->
                                <div class="dynamic-island">
                                    <div class="dynamic-island-camera"></div>
                                    <div class="dynamic-island-sensor"></div>
                                </div>
                                
                                <!-- iPhone Screen -->
                                <div class="iphone-screen-15">
                                    <!-- Status Bar -->
                                    <div class="status-bar-15">
                                        <div class="status-left">
                                            <span class="time">9:41</span>
                                        </div>
                                        <div class="status-right">
                                            <i class="fas fa-signal text-dark"></i>
                                            <i class="fas fa-wifi text-dark ms-1"></i>
                                            <i class="fas fa-battery-three-quarters text-dark ms-1"></i>
                                        </div>
                                    </div>
                                    
                                    <!-- Form Header -->
                                    <div class="text-center mb-3">
                                        <div class="stat-icon bg-flat-color-1 mx-auto mb-2" style="width: 50px; height: 50px; font-size: 1.5rem;">
                                            <i class="fas fa-calendar-plus feature-icon"></i>
                                        </div>
                                        <h5 class="mb-1 fw-bold">Записаться онлайн</h5>
                                        <small class="text-muted">CRM Studio</small>
                                    </div>
                                    
                                    <!-- Real Booking Form Demo -->
                                    <div class="booking-demo">
                                        <!-- Step Indicator -->
                                        <div class="step-indicator-demo mb-3">
                                            <div class="step active">1</div>
                                            <div class="step">2</div>
                                            <div class="step">3</div>
                                            <div class="step">4</div>
                                        </div>
                                        
                                        <!-- Step 1: Service Selection -->
                                        <div class="step-content-demo active" id="demo-step1">
                                            <h6 class="mb-3">Выберите услугу</h6>
                                            <div class="service-card-demo selected">
                                                <h6>Стрижка</h6>
                                                <small class="text-muted">от 1 500 ₽ • 60 мин</small>
                                            </div>
                                            <div class="service-card-demo">
                                                <h6>Окрашивание</h6>
                                                <small class="text-muted">от 3 000 ₽ • 120 мин</small>
                                            </div>
                                            <div class="service-card-demo">
                                                <h6>Маникюр</h6>
                                                <small class="text-muted">от 800 ₽ • 45 мин</small>
                                            </div>
                                            <div class="demo-buttons">
                                                <button type="button" class="btn btn-next-demo">
                                                    Далее <i class="fas fa-arrow-right"></i>
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <!-- Step 2: Master Selection -->
                                        <div class="step-content-demo" id="demo-step2">
                                            <h6 class="mb-3">Выберите мастера</h6>
                                            <div class="master-card-demo selected">
                                                <div class="master-avatar-demo">А</div>
                                                <div>
                                                    <h6>Анна</h6>
                                                    <small class="text-muted">Стилист</small>
                                                </div>
                                            </div>
                                            <div class="master-card-demo">
                                                <div class="master-avatar-demo">М</div>
                                                <div>
                                                    <h6>Мария</h6>
                                                    <small class="text-muted">Мастер</small>
                                                </div>
                                            </div>
                                            <div class="demo-buttons">
                                                <button type="button" class="btn btn-secondary-demo">
                                                    <i class="fas fa-arrow-left"></i> Назад
                                                </button>
                                                <button type="button" class="btn btn-next-demo">
                                                    Далее <i class="fas fa-arrow-right"></i>
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <!-- Step 3: Date & Time -->
                                        <div class="step-content-demo" id="demo-step3">
                                            <h6 class="mb-3">Выберите дату и время</h6>
                                            <div class="calendar-demo mb-3">
                                                <div class="calendar-header-demo">
                                                    <i class="fas fa-chevron-left"></i>
                                                    <span>Июль 2025</span>
                                                    <i class="fas fa-chevron-right"></i>
                                                </div>
                                                <div class="calendar-grid-demo">
                                                    <div class="calendar-day-demo disabled">15</div>
                                                    <div class="calendar-day-demo disabled">16</div>
                                                    <div class="calendar-day-demo">17</div>
                                                    <div class="calendar-day-demo">18</div>
                                                    <div class="calendar-day-demo selected">19</div>
                                                    <div class="calendar-day-demo">20</div>
                                                    <div class="calendar-day-demo">21</div>
                                                </div>
                                            </div>
                                            <div class="time-slots-demo">
                                                <div class="time-slot-demo">09:00</div>
                                                <div class="time-slot-demo">10:00</div>
                                                <div class="time-slot-demo selected">11:00</div>
                                                <div class="time-slot-demo">12:00</div>
                                                <div class="time-slot-demo">13:00</div>
                                            </div>
                                            <div class="demo-buttons">
                                                <button type="button" class="btn btn-secondary-demo">
                                                    <i class="fas fa-arrow-left"></i> Назад
                                                </button>
                                                <button type="button" class="btn btn-next-demo">
                                                    Далее <i class="fas fa-arrow-right"></i>
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <!-- Step 4: Client Data -->
                                        <div class="step-content-demo" id="demo-step4">
                                            <h6 class="mb-3">Ваши данные</h6>
                                            <div class="mb-2">
                                                <input type="text" class="form-control form-control-sm" placeholder="Ваше имя" readonly>
                                            </div>
                                            <div class="mb-2">
                                                <input type="tel" class="form-control form-control-sm" placeholder="+380" readonly>
                                            </div>
                                            <div class="mb-2">
                                                <input type="email" class="form-control form-control-sm" placeholder="Email (необязательно)" readonly>
                                            </div>
                                            <div class="mb-3">
                                                <textarea class="form-control form-control-sm" placeholder="Комментарий" rows="2" readonly></textarea>
                                            </div>
                                            <button type="button" class="btn btn-primary btn-sm w-100" disabled>
                                                <i class="fas fa-check me-1"></i>Записаться
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Home Indicator -->
                                    <div class="home-indicator-15"></div>
                                </div>
                                
                                <!-- Action Button -->
                                <div class="action-button"></div>
                                
                                <!-- Volume Buttons -->
                                <div class="volume-buttons">
                                    <div class="volume-up"></div>
                                    <div class="volume-down"></div>
                                </div>
                                
                                <!-- Power Button -->
                                <div class="power-button"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Floating Elements -->
                    <div class="position-absolute" style="top: -20px; right: 20px;">
                        <div class="bg-success text-white rounded-circle p-2 shadow" style="width: 40px; height: 40px;">
                            <i class="fas fa-check"></i>
                        </div>
                    </div>
                    <div class="position-absolute" style="bottom: 20px; left: -10px;">
                        <div class="bg-warning text-white rounded-circle p-2 shadow" style="width: 35px; height: 35px;">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Content -->
            <div class="col-lg-6">
                <div class="ps-lg-4">
                    <h2 class="display-5 fw-bold mb-4">Веб-запись для вашего сайта</h2>
                    <p class="lead text-muted mb-4">Встроите форму записи прямо на ваш сайт или используйте отдельную ссылку для социальных сетей</p>
                    
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="stat-icon bg-flat-color-1 me-3" style="width: 40px; height: 40px; font-size: 1rem;">
                                    <i class="fas fa-link feature-icon"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1">Отдельная ссылка</h5>
                                    <small class="text-muted">Уникальный URL для каждой соцсети</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="stat-icon bg-flat-color-3 me-3" style="width: 40px; height: 40px; font-size: 1rem;">
                                    <i class="fas fa-mobile-alt feature-icon"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1">Адаптивный дизайн</h5>
                                    <small class="text-muted">Отлично работает на всех устройствах</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="stat-icon bg-flat-color-5 me-3" style="width: 40px; height: 40px; font-size: 1rem;">
                                    <i class="fas fa-instagram feature-icon"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1">Instagram Bio Link</h5>
                                    <small class="text-muted">Идеально для Instagram профиля</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="stat-icon bg-flat-color-2 me-3" style="width: 40px; height: 40px; font-size: 1rem;">
                                    <i class="fas fa-chart-line feature-icon"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1">Аналитика записей</h5>
                                    <small class="text-muted">Отслеживайте источники клиентов</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="instagram-highlight p-1 mb-4">
                        <div class="bg-light rounded-3 p-4">
                            <h5 class="fw-bold mb-3">
                                <i class="fab fa-instagram text-danger me-2"></i>
                                Особенно для Instagram
                            </h5>
                            <p class="mb-3">Создайте отдельную ссылку для Instagram Bio, которая будет вести прямо к форме записи. Клиенты смогут записаться в один клик!</p>
                            <div class="d-flex align-items-center flex-wrap gap-2">
                                <div class="bg-dark text-white rounded-2 px-3 py-2">
                                    <small class="font-monospace">crmstudio.com/booking/insta</small>
                                </div>
                                <button class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-copy me-1"></i>Копировать
                                </button>
                                <button class="btn btn-outline-success btn-sm">
                                    <i class="fab fa-instagram me-1"></i>Добавить в Bio
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-3">
                        <a href="#" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#registerModal">
                            <i class="fas fa-rocket me-2"></i>Попробовать бесплатно
                        </a>
                        <a href="#" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-play me-2"></i>Демо виджета
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-lg-8 mx-auto">
                <h2 class="display-5 fw-bold mb-4">Отзывы наших клиентов</h2>
                <p class="lead text-muted">Что говорят владельцы салонов красоты о CRM Studio</p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-4 col-md-6 animate-fade-in-up">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar bg-flat-color-1 text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px; font-size: 1.2rem; font-weight: 700;">
                                АК
                            </div>
                            <div>
                                <h5 class="mb-0">Анна Коваленко</h5>
                                <small class="text-muted">Салон "Красота"</small>
                            </div>
                        </div>
                        <div class="mb-3">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                        <p class="card-text text-muted">"CRM Studio полностью изменила работу нашего салона. Теперь все записи в одном месте, клиенты получают уведомления, а мы видим полную аналитику. Очень удобно!"</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 animate-fade-in-up">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar bg-flat-color-3 text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px; font-size: 1.2rem; font-weight: 700;">
                                МП
                            </div>
                            <div>
                                <h5 class="mb-0">Мария Петренко</h5>
                                <small class="text-muted">Студия "Элегант"</small>
                            </div>
                        </div>
                        <div class="mb-3">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                        <p class="card-text text-muted">"Отличная система! Особенно нравится управление товарами и складом. Всегда знаем, что есть в наличии, а что нужно заказать. Экономит много времени."</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 animate-fade-in-up">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar bg-flat-color-5 text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px; font-size: 1.2rem; font-weight: 700;">
                                ОС
                            </div>
                            <div>
                                <h5 class="mb-0">Ольга Сидоренко</h5>
                                <small class="text-muted">Салон "Престиж"</small>
                            </div>
                        </div>
                        <div class="mb-3">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                        <p class="card-text text-muted">"Простое и понятное управление клиентами. Аналитика помогает принимать правильные решения. Поддержка отвечает быстро и по делу. Рекомендую всем!"</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-5">
            <div class="col-12 text-center">
                <div class="d-flex justify-content-center align-items-center flex-wrap gap-4">
                    <div class="text-center">
                        <h3 class="fw-bold text-primary mb-0">4.9</h3>
                        <div class="mb-2">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                        <small class="text-muted">Средний рейтинг</small>
                    </div>
                    <div class="text-center">
                        <h3 class="fw-bold text-primary mb-0">500+</h3>
                        <small class="text-muted">Довольных клиентов</small>
                    </div>
                    <div class="text-center">
                        <h3 class="fw-bold text-primary mb-0">98%</h3>
                        <small class="text-muted">Рекомендуют друзьям</small>
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
                <h2 class="display-5 fw-bold mb-4">Почему выбирают CRM Studio</h2>
                <p class="lead text-muted">Тысячи салонов красоты уже используют нашу систему</p>
            </div>
        </div>
        
        <div class="row g-4 text-center">
            <div class="col-lg-3 col-md-6 animate-fade-in-up">
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
            
            <div class="col-lg-3 col-md-6 animate-fade-in-up">
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
            
            <div class="col-lg-3 col-md-6 animate-fade-in-up">
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
            
            <div class="col-lg-3 col-md-6 animate-fade-in-up">
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
/* Дополнительные стили для главной страницы */
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
</style>
@endpush

@push('scripts')
<script src="{{ asset('landing/main.js') }}"></script>
@endpush 
