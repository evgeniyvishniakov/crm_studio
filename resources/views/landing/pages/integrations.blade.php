@extends('landing.layouts.app')

@section('title', 'Интеграции - CRM Studio')
@section('description', 'Подключите дополнительные сервисы для автоматизации работы вашего салона красоты')

@section('content')
<!-- Hero Section -->
<section class="bg-light py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold mb-4 gradient-text section-title">Интеграции</h1>
                <p class="lead text-muted typing-effect" data-text="Автоматизируйте работу салона с помощью мощных интеграций">Автоматизируйте работу салона с помощью мощных интеграций</p>
            </div>
        </div>
    </div>
</section>

<!-- Telegram Integration -->
<section class="py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 animate-fade-in-left">
                <div class="d-flex align-items-center mb-4">
                    <div class="stat-icon bg-flat-color-1 me-3 feature-icon">
                        <i class="fab fa-telegram"></i>
                    </div>
                    <h2 class="mb-0 section-title">Telegram уведомления</h2>
                </div>
                <p class="lead text-muted mb-4">Получайте мгновенные уведомления о всех важных событиях в вашем салоне прямо в Telegram.</p>
                
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="fw-bold mb-3">Что уведомляет:</h5>
                        <ul class="list-unstyled">
                            <li class="mb-2 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Новые записи клиентов</li>
                            <li class="mb-2 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Изменения в записях</li>
                            <li class="mb-2 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Отмены записей</li>
                            <li class="mb-2 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Продажи товаров</li>
                            <li class="mb-2 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Низкие остатки на складе</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5 class="fw-bold mb-3">Преимущества:</h5>
                        <ul class="list-unstyled">
                            <li class="mb-2 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Мгновенные уведомления</li>
                            <li class="mb-2 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Простая настройка</li>
                            <li class="mb-2 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Бесплатно</li>
                            <li class="mb-2 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Работает на всех устройствах</li>
                        </ul>
                    </div>
                </div>
                
                <div class="mt-4">
                    <a href="#" class="btn btn-primary btn-lg animate-glow" data-bs-toggle="modal" data-bs-target="#registerModal">
                        <i class="fab fa-telegram me-2"></i>Настроить Telegram
                    </a>
                </div>
            </div>
            <div class="col-lg-6 animate-fade-in-right">
                <div class="card border-0 shadow-lg card-3d">
                    <div class="card-header bg-gradient-primary text-white">
                        <h5 class="mb-0"><i class="fab fa-telegram me-2"></i>Пример уведомления</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="bg-light rounded p-3 mb-3 interactive-element">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fab fa-telegram text-primary me-2"></i>
                                <strong>CRM Studio Bot</strong>
                                <small class="text-muted ms-auto">12:30</small>
                            </div>
                            <p class="mb-2"><strong>Новая запись!</strong></p>
                            <p class="mb-1">👤 <strong>Анна Петрова</strong></p>
                            <p class="mb-1">💇‍♀️ <strong>Стрижка + укладка</strong></p>
                            <p class="mb-1">👩‍🎨 <strong>Мастер: Елена</strong></p>
                            <p class="mb-1">📅 <strong>Сегодня, 15:00</strong></p>
                            <p class="mb-0">💰 <strong>Стоимость: 2,500₽</strong></p>
                        </div>
                        <div class="text-center">
                            <small class="text-muted">Уведомления приходят мгновенно при создании записей</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Email Integration -->
<section class="bg-light py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 order-lg-2 animate-fade-in-right">
                <div class="d-flex align-items-center mb-4">
                    <div class="stat-icon bg-flat-color-3 me-3 feature-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h2 class="mb-0 section-title">Email интеграция</h2>
                </div>
                <p class="lead text-muted mb-4">Автоматическая отправка профессиональных писем клиентам о всех изменениях в записях.</p>
                
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="fw-bold mb-3">Типы писем:</h5>
                        <ul class="list-unstyled">
                            <li class="mb-2 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Подтверждение записи</li>
                            <li class="mb-2 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Изменение записи</li>
                            <li class="mb-2 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Отмена записи</li>
                            <li class="mb-2 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Напоминания</li>
                            <li class="mb-2 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Приветственные письма</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5 class="fw-bold mb-3">Возможности:</h5>
                        <ul class="list-unstyled">
                            <li class="mb-2 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Настраиваемые шаблоны</li>
                            <li class="mb-2 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Автоматическая отправка</li>
                            <li class="mb-2 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Отслеживание доставки</li>
                            <li class="mb-2 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Персонализация</li>
                        </ul>
                    </div>
                </div>
                
                <div class="mt-4">
                    <a href="#" class="btn btn-success btn-lg animate-glow" data-bs-toggle="modal" data-bs-target="#registerModal">
                        <i class="fas fa-envelope me-2"></i>Настроить Email
                    </a>
                </div>
            </div>
            <div class="col-lg-6 order-lg-1 animate-fade-in-left">
                <div class="card border-0 shadow-lg card-3d">
                    <div class="card-header bg-gradient-secondary text-white">
                        <h5 class="mb-0"><i class="fas fa-envelope me-2"></i>Пример письма</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="bg-light rounded p-3 mb-3 interactive-element">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-envelope text-success me-2"></i>
                                <strong>CRM Studio</strong>
                                <small class="text-muted ms-auto">15:00</small>
                            </div>
                            <p class="mb-2"><strong>Подтверждение записи</strong></p>
                            <p class="mb-1">Уважаемая Анна Петрова!</p>
                            <p class="mb-1">Ваша запись подтверждена:</p>
                            <p class="mb-1">📅 <strong>Дата: 15 января 2024</strong></p>
                            <p class="mb-1">🕐 <strong>Время: 15:00</strong></p>
                            <p class="mb-1">💇‍♀️ <strong>Услуга: Стрижка + укладка</strong></p>
                            <p class="mb-1">👩‍🎨 <strong>Мастер: Елена</strong></p>
                            <p class="mb-0">💰 <strong>Стоимость: 2,500₽</strong></p>
                        </div>
                        <div class="text-center">
                            <small class="text-muted">Письма отправляются автоматически</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Widget Integration -->
<section class="py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 animate-fade-in-left">
                <div class="d-flex align-items-center mb-4">
                    <div class="stat-icon bg-flat-color-2 me-3 feature-icon">
                        <i class="fas fa-globe"></i>
                    </div>
                    <h2 class="mb-0 section-title">Виджет для сайта</h2>
                </div>
                <p class="lead text-muted mb-4">Встраиваемый виджет для вашего сайта, позволяющий клиентам записываться онлайн.</p>
                
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="fw-bold mb-3">Функции виджета:</h5>
                        <ul class="list-unstyled">
                            <li class="mb-2 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Выбор услуг</li>
                            <li class="mb-2 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Выбор мастера</li>
                            <li class="mb-2 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Выбор даты и времени</li>
                            <li class="mb-2 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Ввод контактных данных</li>
                            <li class="mb-2 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Мгновенное подтверждение</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5 class="fw-bold mb-3">Настройки:</h5>
                        <ul class="list-unstyled">
                            <li class="mb-2 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Настраиваемый дизайн</li>
                            <li class="mb-2 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Выбор цветов</li>
                            <li class="mb-2 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Позиционирование</li>
                            <li class="mb-2 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Размер кнопки</li>
                        </ul>
                    </div>
                </div>
                
                <div class="mt-4">
                    <a href="#" class="btn btn-info btn-lg animate-glow" data-bs-toggle="modal" data-bs-target="#registerModal">
                        <i class="fas fa-globe me-2"></i>Получить виджет
                    </a>
                </div>
            </div>
            <div class="col-lg-6 animate-fade-in-right">
                <div class="card border-0 shadow-lg card-3d">
                    <div class="card-header bg-gradient-accent text-white">
                        <h5 class="mb-0"><i class="fas fa-globe me-2"></i>Виджет на сайте</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="bg-light rounded p-3 mb-3 interactive-element">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h6 class="mb-0">Записаться на услугу</h6>
                                <button class="btn btn-primary btn-sm">Записаться</button>
                            </div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <select class="form-select form-select-sm">
                                        <option>Выберите услугу</option>
                                        <option>Стрижка</option>
                                        <option>Окрашивание</option>
                                        <option>Маникюр</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <select class="form-select form-select-sm">
                                        <option>Выберите мастера</option>
                                        <option>Елена</option>
                                        <option>Мария</option>
                                        <option>Анна</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            <small class="text-muted">Виджет легко встраивается в любой сайт</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Web Booking -->
<section class="bg-light py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 order-lg-2 animate-fade-in-right">
                <div class="d-flex align-items-center mb-4">
                    <div class="stat-icon bg-flat-color-5 me-3 feature-icon">
                        <i class="fas fa-link"></i>
                    </div>
                    <h2 class="mb-0 section-title">Веб-запись</h2>
                </div>
                <p class="lead text-muted mb-4">Публичная форма записи, которую можно разместить в социальных сетях или отправить клиентам.</p>
                
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="fw-bold mb-3">Возможности:</h5>
                        <ul class="list-unstyled">
                            <li class="mb-2 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Публичная ссылка</li>
                            <li class="mb-2 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Полная форма записи</li>
                            <li class="mb-2 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Выбор услуг и времени</li>
                            <li class="mb-2 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Мгновенное подтверждение</li>
                            <li class="mb-2 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Интеграция с CRM</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5 class="fw-bold mb-3">Применение:</h5>
                        <ul class="list-unstyled">
                            <li class="mb-2 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Instagram профиль</li>
                            <li class="mb-2 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>WhatsApp статус</li>
                            <li class="mb-2 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Email рассылки</li>
                            <li class="mb-2 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Бизнес-карточки</li>
                        </ul>
                    </div>
                </div>
                
                <div class="mt-4">
                    <a href="#" class="btn btn-warning btn-lg animate-glow" data-bs-toggle="modal" data-bs-target="#registerModal">
                        <i class="fas fa-link me-2"></i>Создать веб-запись
                    </a>
                </div>
            </div>
            <div class="col-lg-6 order-lg-1 animate-fade-in-left">
                <div class="card border-0 shadow-lg card-3d">
                    <div class="card-header bg-gradient-accent text-white">
                        <h5 class="mb-0"><i class="fas fa-link me-2"></i>Веб-форма записи</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="bg-light rounded p-3 mb-3 interactive-element">
                            <h6 class="mb-3">Записаться в салон красоты</h6>
                            <div class="mb-3">
                                <label class="form-label">Выберите услугу</label>
                                <select class="form-select">
                                    <option>Стрижка + укладка</option>
                                    <option>Окрашивание</option>
                                    <option>Маникюр</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Выберите дату</label>
                                <input type="date" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Выберите время</label>
                                <select class="form-select">
                                    <option>15:00</option>
                                    <option>16:00</option>
                                    <option>17:00</option>
                                </select>
                            </div>
                            <button class="btn btn-primary w-100">Записаться</button>
                        </div>
                        <div class="text-center">
                            <small class="text-muted">Форма доступна по публичной ссылке</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="bg-gradient-primary text-white py-5">
    <div class="container text-center">
        <h2 class="display-6 fw-bold mb-4">Готовы автоматизировать?</h2>
        <p class="lead mb-4">Подключите интеграции и сделайте работу салона еще эффективнее</p>
        <a href="#" class="btn btn-light btn-lg animate-glow" data-bs-toggle="modal" data-bs-target="#registerModal">
            <i class="fas fa-rocket me-2"></i>Начать бесплатно
        </a>
    </div>
</section>

@include('landing.components.register-modal')
@endsection

@push('styles')
<style>
/* Специальные стили для страницы интеграций */
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