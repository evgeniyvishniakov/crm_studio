@extends('landing.layouts.app')

@section('title', 'Функции - CRM Studio')
@section('description', 'Подробный обзор всех возможностей CRM Studio для управления салоном красоты')

@section('content')
<!-- Hero Section -->
<section class="bg-light py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold mb-4 gradient-text section-title">Все возможности CRM Studio</h1>
                <p class="lead text-muted typing-effect" data-text="Полный набор инструментов для эффективного управления салоном красоты">Полный набор инструментов для эффективного управления салоном красоты</p>
            </div>
        </div>
    </div>
</section>

<!-- Dashboard Overview -->
<section class="py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 animate-fade-in-left">
                <h2 class="display-6 fw-bold mb-4 section-title">Панель управления</h2>
                <p class="lead text-muted mb-4">Полный контроль над вашим бизнесом в одном месте</p>
                <ul class="list-unstyled">
                    <li class="mb-3 feature-item"><i class="fas fa-chart-pie text-primary me-2 feature-icon"></i>Карточки с ключевыми показателями</li>
                    <li class="mb-3 feature-item"><i class="fas fa-chart-line text-primary me-2 feature-icon"></i>Графики динамики показателей</li>
                    <li class="mb-3 feature-item"><i class="fas fa-calendar text-primary me-2 feature-icon"></i>Интерактивный календарь</li>
                    <li class="mb-3 feature-item"><i class="fas fa-list text-primary me-2 feature-icon"></i>Ближайшие записи</li>
                    <li class="mb-3 feature-item"><i class="fas fa-chart-bar text-primary me-2 feature-icon"></i>Краткий отчет за сегодня</li>
                    <li class="mb-3 feature-item"><i class="fas fa-tasks text-primary me-2 feature-icon"></i>Todo-лист для задач</li>
                </ul>
            </div>
            <div class="col-lg-6 animate-fade-in-right">
                <div class="card border-0 shadow-lg card-3d">
                    <div class="card-body p-4">
                        <div class="row text-center">
                            <div class="col-6 mb-3">
                                <div class="bg-gradient-primary text-white rounded p-3 interactive-element">
                                    <h4 class="mb-0 counter" data-target="125000">₽125,000</h4>
                                    <small>Прибыль</small>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="bg-gradient-secondary text-white rounded p-3 interactive-element">
                                    <h4 class="mb-0 counter" data-target="47">47</h4>
                                    <small>Записей</small>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="bg-gradient-accent text-white rounded p-3 interactive-element">
                                    <h4 class="mb-0 counter" data-target="156">156</h4>
                                    <small>Клиентов</small>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="bg-warning text-white rounded p-3 interactive-element">
                                    <h4 class="mb-0 counter" data-target="12">12</h4>
                                    <small>Услуг</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Client Management -->
<section class="bg-light py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 order-lg-2 animate-fade-in-right">
                <h2 class="display-6 fw-bold mb-4 section-title">Работа с клиентами</h2>
                <p class="lead text-muted mb-4">Полная база клиентов с историей и аналитикой</p>
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="fw-bold mb-3">Клиенты</h5>
                        <ul class="list-unstyled">
                            <li class="mb-2 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>База данных клиентов</li>
                            <li class="mb-2 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>История посещений</li>
                            <li class="mb-2 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Предпочтения и заметки</li>
                            <li class="mb-2 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Сегментация по типам</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5 class="fw-bold mb-3">Записи</h5>
                        <ul class="list-unstyled">
                            <li class="mb-2 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Календарь записей</li>
                            <li class="mb-2 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Расписание мастеров</li>
                            <li class="mb-2 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Статусы записей</li>
                            <li class="mb-2 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Продажи товаров</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 order-lg-1 animate-fade-in-left">
                <div class="card border-0 shadow-lg card-3d">
                    <div class="card-header bg-gradient-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-users me-2"></i>База клиентов</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Клиент</th>
                                        <th>Тип</th>
                                        <th>Последний визит</th>
                                        <th>Сумма</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="interactive-element">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar bg-flat-color-1 text-white rounded-circle me-2 magnetic" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; font-size: 0.8rem;">
                                                    АК
                                                </div>
                                                <div>
                                                    <strong>Анна Ковалева</strong>
                                                    <br><small class="text-muted">anna@email.com</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-success animate-pulse">Постоянный</span></td>
                                        <td>15.01.2024</td>
                                        <td>₽12,500</td>
                                    </tr>
                                    <tr class="interactive-element">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar bg-flat-color-2 text-white rounded-circle me-2 magnetic" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; font-size: 0.8rem;">
                                                    МП
                                                </div>
                                                <div>
                                                    <strong>Мария Петрова</strong>
                                                    <br><small class="text-muted">maria@email.com</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-warning animate-pulse">Новый</span></td>
                                        <td>10.01.2024</td>
                                        <td>₽3,200</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Inventory Management -->
<section class="py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 animate-fade-in-left">
                <h2 class="display-6 fw-bold mb-4 section-title">Управление товарами</h2>
                <p class="lead text-muted mb-4">Полный контроль над складом, закупками и продажами</p>
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="fw-bold mb-3">Склад</h5>
                        <ul class="list-unstyled">
                            <li class="mb-2 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Учет товаров</li>
                            <li class="mb-2 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Остатки на складе</li>
                            <li class="mb-2 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Категории и бренды</li>
                            <li class="mb-2 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Фотографии товаров</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5 class="fw-bold mb-3">Закупки и продажи</h5>
                        <ul class="list-unstyled">
                            <li class="mb-2 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Управление закупками</li>
                            <li class="mb-2 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Продажи товаров</li>
                            <li class="mb-2 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Инвентаризация</li>
                            <li class="mb-2 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Аналитика товарооборота</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 animate-fade-in-right">
                <div class="card border-0 shadow-lg card-3d">
                    <div class="card-header bg-gradient-secondary text-white">
                        <h5 class="mb-0"><i class="fas fa-boxes me-2"></i>Склад товаров</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="stat-card bg-success-light interactive-element">
                                    <div class="stat-icon bg-flat-color-1" style="width: 50px; height: 50px; font-size: 1.2rem;">
                                        <i class="fas fa-box"></i>
                                    </div>
                                    <div class="stat-content">
                                        <h6 class="stat-title">Товаров на складе</h6>
                                        <p class="stat-value counter" data-target="245">245</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-card bg-info-light interactive-element">
                                    <div class="stat-icon bg-flat-color-3" style="width: 50px; height: 50px; font-size: 1.2rem;">
                                        <i class="fas fa-shopping-cart"></i>
                                    </div>
                                    <div class="stat-content">
                                        <h6 class="stat-title">Продаж за месяц</h6>
                                        <p class="stat-value counter" data-target="89">89</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-card bg-warning-light interactive-element">
                                    <div class="stat-icon bg-flat-color-4" style="width: 50px; height: 50px; font-size: 1.2rem;">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </div>
                                    <div class="stat-content">
                                        <h6 class="stat-title">Заканчивается</h6>
                                        <p class="stat-value counter" data-target="12">12</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-card bg-danger-light interactive-element">
                                    <div class="stat-icon bg-flat-color-4" style="width: 50px; height: 50px; font-size: 1.2rem;">
                                        <i class="fas fa-times-circle"></i>
                                    </div>
                                    <div class="stat-content">
                                        <h6 class="stat-title">Нет в наличии</h6>
                                        <p class="stat-value counter" data-target="3">3</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Analytics -->
<section class="bg-light py-5">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-lg-8 mx-auto">
                <h2 class="display-5 fw-bold mb-4 section-title">Аналитика и отчеты</h2>
                <p class="lead text-muted">Детальная аналитика по всем аспектам работы салона</p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-4 col-md-6 feature-item">
                <div class="card h-100 border-0 shadow-sm card-3d">
                    <div class="card-body text-center p-4">
                        <div class="stat-icon bg-flat-color-1 mx-auto mb-3 feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h4 class="card-title">Финансовая аналитика</h4>
                        <p class="card-text text-muted">Отслеживайте прибыль, расходы, динамику продаж и ключевые финансовые показатели.</p>
                        <ul class="list-unstyled text-start">
                            <li><i class="fas fa-check text-success me-2"></i>Динамика прибыли</li>
                            <li><i class="fas fa-check text-success me-2"></i>Анализ расходов</li>
                            <li><i class="fas fa-check text-success me-2"></i>Средний чек</li>
                            <li><i class="fas fa-check text-success me-2"></i>Прогнозы</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 feature-item">
                <div class="card h-100 border-0 shadow-sm card-3d">
                    <div class="card-body text-center p-4">
                        <div class="stat-icon bg-flat-color-3 mx-auto mb-3 feature-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h4 class="card-title">Аналитика клиентов</h4>
                        <p class="card-text text-muted">Анализируйте поведение клиентов, их предпочтения и лояльность.</p>
                        <ul class="list-unstyled text-start">
                            <li><i class="fas fa-check text-success me-2"></i>Топ клиентов</li>
                            <li><i class="fas fa-check text-success me-2"></i>Частота посещений</li>
                            <li><i class="fas fa-check text-success me-2"></i>Предпочтения услуг</li>
                            <li><i class="fas fa-check text-success me-2"></i>Сегментация</li>
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
                        <h4 class="card-title">Аналитика услуг</h4>
                        <p class="card-text text-muted">Отслеживайте популярность услуг, загруженность мастеров и эффективность.</p>
                        <ul class="list-unstyled text-start">
                            <li><i class="fas fa-check text-success me-2"></i>Популярные услуги</li>
                            <li><i class="fas fa-check text-success me-2"></i>Загруженность мастеров</li>
                            <li><i class="fas fa-check text-success me-2"></i>Эффективность</li>
                            <li><i class="fas fa-check text-success me-2"></i>Статистика записей</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Integrations -->
<section class="py-5">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-lg-8 mx-auto">
                <h2 class="display-5 fw-bold mb-4 section-title">Интеграции</h2>
                <p class="lead text-muted">Подключайте дополнительные сервисы для автоматизации работы</p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-3 col-md-6 feature-item">
                <div class="card border-0 shadow-sm card-3d text-center">
                    <div class="card-body p-4">
                        <div class="stat-icon bg-flat-color-1 mx-auto mb-3 feature-icon">
                            <i class="fab fa-telegram"></i>
                        </div>
                        <h5 class="card-title">Telegram</h5>
                        <p class="card-text text-muted">Уведомления о новых записях и изменениях</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 feature-item">
                <div class="card border-0 shadow-sm card-3d text-center">
                    <div class="card-body p-4">
                        <div class="stat-icon bg-flat-color-3 mx-auto mb-3 feature-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <h5 class="card-title">Email</h5>
                        <p class="card-text text-muted">Автоматические письма клиентам</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 feature-item">
                <div class="card border-0 shadow-sm card-3d text-center">
                    <div class="card-body p-4">
                        <div class="stat-icon bg-flat-color-2 mx-auto mb-3 feature-icon">
                            <i class="fas fa-globe"></i>
                        </div>
                        <h5 class="card-title">Виджет</h5>
                        <p class="card-text text-muted">Встраиваемый виджет для сайта</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 feature-item">
                <div class="card border-0 shadow-sm card-3d text-center">
                    <div class="card-body p-4">
                        <div class="stat-icon bg-flat-color-5 mx-auto mb-3 feature-icon">
                            <i class="fas fa-link"></i>
                        </div>
                        <h5 class="card-title">Веб-запись</h5>
                        <p class="card-text text-muted">Публичная форма записи</p>
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
/* Специальные стили для страницы функций */
.feature-item {
    opacity: 0;
    transform: translateY(30px);
    transition: all 0.8s ease;
}

.feature-item.revealed {
    opacity: 1;
    transform: translateY(0);
}

.feature-icon {
    transition: all 0.3s ease;
    color: var(--primary-color);
}

.feature-item:hover .feature-icon {
    transform: scale(1.2) rotate(10deg);
    color: var(--secondary-color);
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

/* Адаптивность */
@media (max-width: 768px) {
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