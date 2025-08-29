@extends('landing.layouts.app')

@php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\App;
@endphp

@section('title', 'Тарифы - Trimora')
@section('description', 'Выберите подходящий тариф для вашего салона красоты. Начните с бесплатного пробного периода на 7 дней.')

@section('content')
<!-- Отладочная информация о мультивалютности -->
<div class="container mt-3">
    <div class="alert alert-info">
        <h6>🧪 Тест мультивалютности:</h6>
        <p><strong>Текущий язык:</strong> {{ App::getLocale() }}</p>
        <p><strong>Доступные валюты:</strong> 
            @foreach($currencies as $currency)
                {{ $currency->code }} ({{ $currency->symbol }})@if(!$loop->last), @endif
            @endforeach
        </p>
        <p><strong>Валюты по языкам:</strong> 
            @foreach($defaultCurrencies as $lang => $curr)
                {{ $lang }} → {{ $curr }}@if(!$loop->last), @endif
            @endforeach
        </p>
        
        @if($plans->count() > 0)
            @php $plan = $plans->first(); @endphp
            <p><strong>Тест плана "{{ $plan->name }}":</strong></p>
            <ul>
                <li>Месяц: {{ $plan->getPriceForLanguage('ua', 'monthly') }}₴ / {{ $plan->getPriceForLanguage('en', 'monthly') }}$</li>
                <li>3 месяца: {{ $plan->getPriceForLanguage('ua', 'quarterly') }}₴ / {{ $plan->getPriceForLanguage('en', 'quarterly') }}$</li>
            </ul>
        @endif
    </div>
</div>

<!-- Hero Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold mb-4 text-dark">Тарифы</h1>
                <p class="lead mb-4 text-muted">Выберите план в зависимости от количества сотрудников в вашем салоне</p>
                <p class="text-muted mb-4">Все тарифы включают полный набор функций Trimora</p>
                @if(Auth::guard('client')->check())
                    <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg animate-pulse">
                        <i class="fas fa-sign-in-alt me-2"></i>Войти в систему
                    </a>
                @else
                    <a href="#" class="btn btn-primary btn-lg animate-pulse" data-bs-toggle="modal" data-bs-target="#registerModal">
                        <i class="fas fa-rocket me-2"></i>Попробовать бесплатно 7 дней
                    </a>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Features Overview Section -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-lg-8 mx-auto">
                <h2 class="h3 fw-bold mb-4 text-dark">Все тарифы включают полный набор функций</h2>
                <p class="text-muted">Цена зависит только от количества сотрудников в вашем салоне</p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="text-center">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-users text-primary fa-lg"></i>
                    </div>
                    <h5 class="fw-bold">Управление клиентами</h5>
                    <p class="text-muted small">База данных клиентов, история посещений, предпочтения</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="text-center">
                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-calendar-alt text-success fa-lg"></i>
                    </div>
                    <h5 class="fw-bold">Записи и календарь</h5>
                    <p class="text-muted small">Онлайн-бронирование, расписание мастеров, уведомления</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="text-center">
                    <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-chart-line text-bright-yellow fa-lg"></i>
                    </div>
                    <h5 class="fw-bold">Аналитика и отчеты</h5>
                    <p class="text-muted small">Детальная статистика, финансовые отчеты, аналитика</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="text-center">
                    <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-headset text-info fa-lg"></i>
                    </div>
                    <h5 class="fw-bold">Поддержка 24/7</h5>
                    <p class="text-muted small">Техническая поддержка, обучение, консультации</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Pricing Section -->
<section id="pricing" class="py-5 bg-light">
    <div class="container">
        <!-- Tabs Navigation -->
        <div class="row justify-content-center mb-5">
            <div class="col-lg-8">
                <ul class="nav nav-pills nav-fill" id="pricingTabs" role="tablist">
                    @foreach($plans as $index => $plan)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $index === 0 ? 'active' : '' }}" 
                                    id="{{ $plan->slug }}-tab" 
                                    data-bs-toggle="pill" 
                                    data-bs-target="#{{ $plan->slug }}" 
                                    type="button" 
                                    role="tab"
                                    aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                                {{ $plan->name }}
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <!-- Tabs Content -->
        <div class="tab-content" id="pricingTabsContent">
            @foreach($plans as $index => $plan)
                <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="{{ $plan->slug }}" role="tabpanel">
                    <div class="row justify-content-center">
                        <!-- Месяц -->
                                                <div class="col-lg-3 col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm {{ $planColors[$plan->slug]['border'] ?? 'border-success' }} border-2" data-plan-id="{{ $plan->id }}">
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge {{ $planColors[$plan->slug]['badge'] ?? 'bg-success' }}" style="font-size: 0.7rem;">Базовый</span>
                            </div>
                                <div class="card-body p-4" style="padding-top: 3rem !important;">
                                    <div class="text-center mb-4">
                                        <h3 class="h5 fw-bold text-dark mb-3">Месяц</h3>
                                        <div class="mb-3">
                                            <span class="h2 fw-bold text-dark">{{ number_format($plan->price_monthly, 0, ',', ' ') }}₴</span>
                                            <span class="text-muted">/ месяц</span>
                                        </div>
                                    </div>
                                    
                                    <div class="text-center mb-4">
                                        <div class="{{ $planColors[$plan->slug]['badge'] }} bg-opacity-10 rounded p-3 mb-3">
                                            <i class="{{ $planColors[$plan->slug]['icon_class'] }} {{ $planColors[$plan->slug]['icon'] }} fa-2x mb-2"></i>
                                            <h6 class="fw-bold {{ $planColors[$plan->slug]['icon'] }} mb-0">{{ $plan->name }}</h6>
                                        </div>
                                        <p class="text-muted small">{{ $plan->description ?: 'Идеально для вашего бизнеса' }}</p>
                                    </div>
                                    
                                    <div class="text-center mb-4">
                                        <p class="{{ $planColors[$plan->slug]['icon'] }} fw-bold mb-2"><i class="fas fa-check-circle me-2"></i>Все функции включены</p>
                                        <p class="text-muted small">Полный доступ ко всем возможностям CRM Studio</p>
                                    </div>
                                    
                                    @if(Auth::guard('client')->check())
                                        <a href="#" class="btn {{ $planColors[$plan->slug]['button'] }} w-100 pricing-btn">
                                            Выбрать план
                                        </a>
                                    @else
                                        <a href="#" class="btn {{ $planColors[$plan->slug]['button'] }} w-100 pricing-btn" data-bs-toggle="modal" data-bs-target="#registerModal">
                                            Попробовать бесплатно 7 дней
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- 3 месяца -->
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="card h-100 position-relative {{ $planColors[$plan->slug]['border'] }} border-3 shadow-lg" style="transform: scale(1.05); z-index: 10;" data-plan-id="{{ $plan->id }}">
                                <div class="position-absolute top-0 start-0 m-3">
                                    <span class="badge {{ $planColors[$plan->slug]['badge'] }} px-2 py-1" style="font-size: 0.7rem; font-weight: 700;">
                                        <i class="fas fa-star me-1"></i>ТОП
                                    </span>
                                </div>
                                <div class="position-absolute top-0 end-0 m-3">
                                    <span class="badge {{ $planColors[$plan->slug]['badge'] }}" style="font-size: 0.7rem;">Экономия 10%</span>
                                </div>
                                <div class="card-body p-4" style="padding-top: 3.5rem !important;">
                                    <div class="text-center mb-4">
                                        <h3 class="h5 fw-bold text-dark mb-3">3 месяца</h3>
                                        <div class="mb-3">
                                            <span class="h2 fw-bold text-dark">{{ number_format($plan->getPriceForPeriod('quarterly'), 0, ',', ' ') }}₴</span>
                                            <span class="text-muted">/ 3 месяца</span>
                                        </div>
                                        <p class="text-muted small">{{ number_format($plan->getPriceForPeriod('quarterly') / 3, 0, ',', ' ') }}₴ / месяц</p>
                                    </div>
                                    
                                    <div class="text-center mb-4">
                                        <div class="{{ $planColors[$plan->slug]['badge'] }} bg-opacity-10 rounded p-3 mb-3">
                                            <i class="fas fa-users {{ $planColors[$plan->slug]['icon'] }} fa-2x mb-2"></i>
                                            <h6 class="fw-bold {{ $planColors[$plan->slug]['icon'] }} mb-0">{{ $plan->name }}</h6>
                                        </div>
                                        <p class="text-muted small">{{ $plan->description ?: 'Идеально для вашего бизнеса' }}</p>
                                    </div>
                                    
                                    <div class="text-center mb-4">
                                        <p class="{{ $planColors[$plan->slug]['icon'] }} fw-bold mb-2"><i class="fas fa-check-circle me-2"></i>Все функции включены</p>
                                        <p class="text-muted small">Полный доступ ко всем возможностям CRM Studio</p>
                                    </div>
                                    
                                    @if(Auth::guard('client')->check())
                                        <a href="#" class="btn {{ $planColors[$plan->slug]['button'] }} w-100 pricing-btn">
                                            Выбрать план
                                        </a>
                                    @else
                                        <a href="#" class="btn {{ $planColors[$plan->slug]['button'] }} w-100 pricing-btn" data-bs-toggle="modal" data-bs-target="#registerModal">
                                            Попробовать бесплатно 7 дней
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- 6 месяцев -->
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm {{ $planColors[$plan->slug]['border'] }} border-2" data-plan-id="{{ $plan->id }}">
                                <div class="position-absolute top-0 end-0 m-3">
                                    <span class="badge {{ $planColors[$plan->slug]['badge'] }}" style="font-size: 0.7rem;">Экономия 15%</span>
                                </div>
                                <div class="card-body p-4" style="padding-top: 3rem !important;">
                                    <div class="text-center mb-4">
                                        <h3 class="h5 fw-bold text-dark mb-3">6 месяцев</h3>
                                        <div class="mb-3">
                                            <span class="h2 fw-bold text-dark">{{ number_format($plan->getPriceForPeriod('semiannual'), 0, ',', ' ') }}₴</span>
                                            <span class="text-muted">/ 6 месяцев</span>
                                        </div>
                                        <p class="text-muted small">{{ number_format($plan->getPriceForPeriod('semiannual') / 6, 0, ',', ' ') }}₴ / месяц</p>
                                    </div>
                                    
                                    <div class="text-center mb-4">
                                        <div class="{{ $planColors[$plan->slug]['badge'] }} bg-opacity-10 rounded p-3 mb-3">
                                            <i class="{{ $planColors[$plan->slug]['icon_class'] }} {{ $planColors[$plan->slug]['icon'] }} fa-2x mb-2"></i>
                                            <h6 class="fw-bold {{ $planColors[$plan->slug]['icon'] }} mb-0">{{ $plan->name }}</h6>
                                        </div>
                                        <p class="text-muted small">{{ $plan->description ?: 'Идеально для вашего бизнеса' }}</p>
                                    </div>
                                    
                                    <div class="text-center mb-4">
                                        <p class="{{ $planColors[$plan->slug]['icon'] }} fw-bold mb-2"><i class="fas fa-check-circle me-2"></i>Все функции включены</p>
                                        <p class="text-muted small">Полный доступ ко всем возможностям CRM Studio</p>
                                    </div>
                                    
                                    @if(Auth::guard('client')->check())
                                        <a href="#" class="btn {{ $planColors[$plan->slug]['button'] }} w-100 pricing-btn">
                                            Выбрать план
                                        </a>
                                    @else
                                        <a href="#" class="btn {{ $planColors[$plan->slug]['button'] }} w-100 pricing-btn" data-bs-toggle="modal" data-bs-target="#registerModal">
                                            Попробовать бесплатно 7 дней
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Год -->
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm {{ $planColors[$plan->slug]['border'] }} border-2" data-plan-id="{{ $plan->id }}">
                                <div class="position-absolute top-0 end-0 m-3">
                                    <span class="badge {{ $planColors[$plan->slug]['badge'] }}" style="font-size: 0.7rem;">Экономия 25%</span>
                                </div>
                                <div class="card-body p-4" style="padding-top: 3rem !important;">
                                    <div class="text-center mb-4">
                                        <h3 class="h5 fw-bold text-dark mb-3">Год</h3>
                                        <div class="mb-3">
                                            <span class="h2 fw-bold text-dark">{{ number_format($plan->getPriceForPeriod('yearly'), 0, ',', ' ') }}₴</span>
                                            <span class="text-muted">/ год</span>
                                        </div>
                                        <p class="text-muted small">{{ number_format($plan->getPriceForPeriod('yearly') / 12, 0, ',', ' ') }}₴ / месяц</p>
                                    </div>
                                    
                                    <div class="text-center mb-4">
                                        <div class="{{ $planColors[$plan->slug]['badge'] }} bg-opacity-10 rounded p-3 mb-3">
                                            <i class="{{ $planColors[$plan->slug]['icon_class'] }} {{ $planColors[$plan->slug]['icon'] }} fa-2x mb-2"></i>
                                            <h6 class="fw-bold {{ $planColors[$plan->slug]['icon'] }} mb-0">{{ $plan->name }}</h6>
                                        </div>
                                        <p class="text-muted small">{{ $plan->description ?: 'Идеально для вашего бизнеса' }}</p>
                                    </div>
                                    
                                    <div class="text-center mb-4">
                                        <p class="{{ $planColors[$plan->slug]['icon'] }} fw-bold mb-2"><i class="fas fa-check-circle me-2"></i>Все функции включены</p>
                                        <p class="text-muted small">Полный доступ ко всем возможностям CRM Studio</p>
                                    </div>
                                    
                                    @if(Auth::guard('client')->check())
                                        <a href="#" class="btn {{ $planColors[$plan->slug]['button'] }} w-100 pricing-btn">
                                            Выбрать план
                                        </a>
                                    @else
                                        <a href="#" class="btn {{ $planColors[$plan->slug]['button'] }} w-100 pricing-btn" data-bs-toggle="modal" data-bs-target="#registerModal">
                                            Попробовать бесплатно 7 дней
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach


        </div>
    </div>
</section>





@include('landing.components.register-modal')

<!-- Модальное окно выбора плана -->
<div class="modal fade" id="planSelectionModal" tabindex="-1" aria-labelledby="planSelectionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="planSelectionModalLabel">Подтверждение выбора плана</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3">Выбранный план:</h6>
                        <div class="card border-0 bg-light">
                            <div class="card-body">
                                <h5 class="card-title" id="selectedPlanName"></h5>
                                <p class="card-text" id="selectedPlanDescription"></p>
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <span class="badge bg-primary fs-6 px-3 py-2" id="selectedPlanPeriod"></span>
                                    <span class="fs-5 fw-bold text-dark" id="selectedPlanPrice"></span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-2" id="selectedPlanSavings" style="display: none;">
                                    <span class="badge bg-success" id="savingsBadge"></span>
                                    <span class="text-muted small" id="selectedPlanMonthlyPrice"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3">Детали подписки:</h6>
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Все функции включены</li>
                            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Полный доступ к CRM Studio</li>
                            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Техническая поддержка</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-primary" id="proceedToPayment">
                    <i class="fas fa-credit-card me-2"></i>Перейти к оплате
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('landing/main.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Script loaded');
    
    // Получаем все вкладки
    var tabs = document.querySelectorAll('#pricingTabs .nav-link');
    console.log('Found tabs:', tabs.length);
    
    // Добавляем обработчик клика на каждую вкладку
    tabs.forEach(function(tab) {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Tab clicked:', this.textContent.trim());
            
            // Убираем активный класс со всех вкладок
            tabs.forEach(function(t) {
                t.classList.remove('active');
                t.setAttribute('aria-selected', 'false');
            });
            
            // Убираем активный класс со всех панелей
            document.querySelectorAll('#pricingTabsContent .tab-pane').forEach(function(pane) {
                pane.classList.remove('show', 'active');
            });
            
            // Активируем текущую вкладку
            this.classList.add('active');
            this.setAttribute('aria-selected', 'true');
            console.log('Tab activated:', this.textContent.trim());
            
            // Показываем соответствующую панель
            var targetId = this.getAttribute('data-bs-target');
            var targetPane = document.querySelector(targetId);
            if (targetPane) {
                targetPane.classList.add('show', 'active');
                console.log('Panel shown:', targetId);
            } else {
                console.log('Panel not found:', targetId);
            }
        });
    });
    
    // Обработчики для кнопок "Выбрать план"
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('pricing-btn') && !e.target.hasAttribute('data-bs-toggle')) {
            e.preventDefault();
            
            // Получаем данные о выбранном плане
            var card = e.target.closest('.card');
            var planName = card.querySelector('h6').textContent;
            var planPeriod = card.querySelector('h3').textContent;
            var planPrice = card.querySelector('.h2').textContent;
            
            // Получаем экономию и стоимость в месяц
            var savings = '';
            var monthlyPrice = '';
            
            // Ищем бейдж с экономией
            var badges = card.querySelectorAll('.badge');
            badges.forEach(function(badge) {
                if (badge.textContent.includes('Экономия')) {
                    savings = badge.textContent;
                }
            });
            
            // Ищем стоимость в месяц
            var smallTexts = card.querySelectorAll('.text-muted.small');
            smallTexts.forEach(function(text) {
                if (text.textContent.includes('₴ / месяц')) {
                    monthlyPrice = text.textContent;
                }
            });
            
            // Получаем цвет плана из кнопки
            var button = e.target;
            var buttonClasses = button.className;
            var planColor = '';
            
            // Определяем цвет плана по классу кнопки
            if (buttonClasses.includes('btn-tab-green')) {
                planColor = 'bg-success';
            } else if (buttonClasses.includes('btn-tab-blue')) {
                planColor = 'bg-primary';
            } else if (buttonClasses.includes('btn-tab-yellow')) {
                planColor = 'bg-warning';
            } else {
                planColor = 'bg-primary'; // по умолчанию
            }
            
            // Заполняем модальное окно
            document.getElementById('selectedPlanName').textContent = planName;
            document.getElementById('selectedPlanDescription').textContent = 'Идеально для вашего бизнеса';
            document.getElementById('selectedPlanPeriod').textContent = planPeriod;
            document.getElementById('selectedPlanPrice').textContent = planPrice;
            
            // Показываем экономию если есть
            var savingsContainer = document.getElementById('selectedPlanSavings');
            var savingsBadge = document.getElementById('savingsBadge');
            if (savings && savings.includes('Экономия')) {
                savingsBadge.textContent = savings;
                savingsContainer.style.display = 'block';
                
                // Применяем цвет плана к бейджу экономии
                savingsBadge.className = 'badge ' + planColor;
            } else {
                savingsContainer.style.display = 'none';
            }
            
            // Показываем стоимость в месяц если есть
            var monthlyPriceContainer = document.getElementById('selectedPlanMonthlyPrice');
            if (monthlyPrice) {
                monthlyPriceContainer.textContent = monthlyPrice;
            } else {
                monthlyPriceContainer.textContent = '';
            }
            
            // Применяем цвет плана к бейджу периода
            var periodBadge = document.getElementById('selectedPlanPeriod');
            periodBadge.className = 'badge me-3 fs-6 px-3 py-2 ' + planColor;
            
            // Устанавливаем атрибуты для кнопки "Перейти к оплате"
            var proceedButton = document.getElementById('proceedToPayment');
            var cardPlanId = card.getAttribute('data-plan-id');
            proceedButton.setAttribute('data-plan-id', cardPlanId);
            
            // Определяем период по тексту на карточке
            var periodText = planPeriod.trim();
            var period = 'monthly'; // по умолчанию
            
            if (periodText.includes('3 месяца') || periodText.includes('3 мес')) {
                period = 'quarterly';
            } else if (periodText.includes('6 месяцев') || periodText.includes('6 мес')) {
                period = 'semiannual';
            } else if (periodText.includes('год') || periodText.includes('12 месяцев')) {
                period = 'yearly';
            }
            
            proceedButton.setAttribute('data-period', period);
            
            console.log('Modal data set:', { 
                planId: cardPlanId, 
                period: period, 
                periodText: periodText 
            });
            
            // Показываем модальное окно
            var modal = new bootstrap.Modal(document.getElementById('planSelectionModal'));
            modal.show();
        }
    });
    
    // Обработчик для кнопки "Перейти к оплате"
    document.getElementById('proceedToPayment').addEventListener('click', function() {
        var planId = this.getAttribute('data-plan-id');
        var period = this.getAttribute('data-period');
        
        console.log('Payment button clicked:', { planId: planId, period: period });
        
        if (planId && period) {
            // Создаем форму для отправки
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("landing.payment.create") }}';
            
            // Добавляем CSRF токен
            var csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            // Добавляем данные плана
            var planIdInput = document.createElement('input');
            planIdInput.type = 'hidden';
            planIdInput.name = 'plan_id';
            planIdInput.value = planId;
            form.appendChild(planIdInput);
            
            // Добавляем период
            var periodInput = document.createElement('input');
            periodInput.type = 'hidden';
            periodInput.name = 'period';
            periodInput.value = period;
            form.appendChild(periodInput);
            
            // Отправляем форму
            document.body.appendChild(form);
            form.submit();
        } else {
            alert('Ошибка: не удалось определить план или период');
        }
    });
});
</script>

<style>
/* Стили для активных вкладок по тарифам */
#pricingTabs .nav-link.active[data-bs-target="#small"] {
    background-color: #28a745 !important; /* Зеленый для первого тарифа */
    color: white !important;
    border-color: #28a745 !important;
}

#pricingTabs .nav-link.active[data-bs-target="#medium"] {
    background-color: #007bff !important; /* Синий для второго тарифа */
    color: white !important;
    border-color: #007bff !important;
}

#pricingTabs .nav-link.active[data-bs-target="#unlimited"] {
    background-color: #ffc107 !important; /* Желтый для третьего тарифа */
    color: white !important; /* Белый текст как у всех */
    border-color: #ffc107 !important;
}

/* Стили для неактивных вкладок */
#pricingTabs .nav-link:not(.active) {
    background-color: transparent !important;
    color: #6c757d !important;
    border-color: #dee2e6 !important;
}

#pricingTabs .nav-link:not(.active):hover {
    background-color: #e9ecef !important;
    color: #495057 !important;
}

/* Стили для компактных кнопок тарифов */
.pricing-btn {
    padding: 8px 16px !important;
    font-size: 14px !important;
    font-weight: 600 !important;
    border-radius: 6px !important;
    transition: all 0.3s ease !important;
}

.pricing-btn:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
}
</style>
@endpush