@extends('landing.layouts.app')

@section('title', 'CRM Studio - Система управления салоном красоты')
@section('description', 'Профессиональная CRM система для управления салоном красоты с веб-записью, Telegram уведомлениями и виджетом для сайта')

@section('content')

<!-- Hero Section -->
<section class="hero-section py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 animate-fade-in-left">
                <h1 class="display-4 fw-bold mb-4 text-dark gradient-text">Автоматизируйте записи клиентов</h1>
                <p class="lead mb-4 text-muted">CRM Studio - современная система с веб-записью, Telegram уведомлениями и виджетом для сайта. Увеличьте количество клиентов и упростите работу салона.</p>
                <div class="d-flex gap-3">
                    <a href="#" class="btn btn-primary btn-lg animate-pulse" data-bs-toggle="modal" data-bs-target="#registerModal" aria-label="Открыть форму регистрации">
                        <i class="fas fa-rocket me-2" aria-hidden="true"></i>Попробовать бесплатно
                    </a>
                    <a href="#features" class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-play me-2"></i>Смотреть функции
                    </a>
                </div>
            </div>
            <div class="col-lg-6 animate-fade-in-right">
                <div class="device-showcase">
                    <div class="laptop-mockup">
                        <div class="laptop-screen">
                            <div class="dashboard-preview">
                                <div class="dashboard-header bg-gradient-primary text-white p-3">
                                    <h6 class="mb-0"><i class="fas fa-chart-line me-2"></i>Панель управления</h6>
                                </div>
                                <div class="dashboard-content p-3">
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <div class="stat-mini bg-success-light p-2 rounded">
                                                <small class="text-muted">Прибыль</small>
                                                <div class="fw-bold">₴125,400</div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="stat-mini bg-info-light p-2 rounded">
                                                <small class="text-muted">Клиенты</small>
                                                <div class="fw-bold">1,247</div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="stat-mini bg-warning-light p-2 rounded">
                                                <small class="text-muted">Записи</small>
                                                <div class="fw-bold">89</div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="stat-mini bg-primary-light p-2 rounded">
                                                <small class="text-muted">Продажи</small>
                                                <div class="fw-bold">₴45,200</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="phone-mockup">
                        <div class="phone-screen">
                            <div class="mobile-preview">
                                <div class="mobile-header bg-gradient-primary text-white p-2">
                                    <small><i class="fas fa-calendar me-1"></i>Записи</small>
                                </div>
                                <div class="mobile-content p-2">
                                    <div class="appointment-item bg-light p-2 rounded mb-1">
                                        <small class="text-muted">14:00</small>
                                        <div class="fw-bold">Маникюр</div>
                                        <small>Анна П.</small>
                                    </div>
                                    <div class="appointment-item bg-light p-2 rounded mb-1">
                                        <small class="text-muted">15:30</small>
                                        <div class="fw-bold">Стрижка</div>
                                        <small>Мария К.</small>
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

<!-- Веб-запись Section -->
<section id="web-booking" class="py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="feature-content">
                    <div class="feature-icon mb-4">
                        <div class="stat-icon bg-flat-color-1">
                            <i class="fas fa-link"></i>
                        </div>
                    </div>
                    <h2 class="display-5 fw-bold mb-4">🔗 Веб-запись через ссылки</h2>
                    <p class="lead mb-4">Клиенты могут записываться на услуги через ссылки в Instagram, Facebook и других социальных сетях. Простая интеграция в любой профиль.</p>
                    <div class="feature-benefits">
                        <div class="benefit-item mb-3">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>Ссылка для Instagram в био</span>
                        </div>
                        <div class="benefit-item mb-3">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>Интеграция с Facebook</span>
                        </div>
                        <div class="benefit-item mb-3">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>Выбор услуг и мастеров в реальном времени</span>
                        </div>
                        <div class="benefit-item mb-3">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>Автоматические уведомления о записях</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="feature-demo">
                    <div class="booking-form-preview">
                        <div class="booking-form-container">
                            <div class="form-header bg-gradient-primary text-white p-3 rounded-top">
                                <h6 class="mb-0"><i class="fas fa-calendar-plus me-2"></i>Онлайн запись</h6>
                            </div>
                            <div class="form-body p-3 bg-white rounded-bottom">
                                <!-- Шаг 1: Выбор услуги -->
                                <div class="form-step active" id="demo-step1">
                                    <h6 class="mb-3">Выберите услугу</h6>
                                    <div class="service-options">
                                        <div class="service-option selected">
                                            <div class="service-info">
                                                <h5 class="mb-1">Маникюр</h5>
                                                <p>от 1500 ₴ • 60 мин</p>
                                            </div>
                                        </div>
                                        <div class="service-option">
                                            <div class="service-info">
                                                <h5 class="mb-1">Стрижка</h5>
                                                <p>от 2000 ₴ • 45 мин</p>
                                            </div>
                                        </div>
                                        <div class="service-option">
                                            <div class="service-info">
                                                <h5 class="mb-1">Массаж</h5>
                                                <p>от 3000 ₴ • 90 мин</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-actions mt-3">
                                        <button class="btn btn-primary btn-sm w-100">Далее <i class="fas fa-arrow-right ms-1"></i></button>
                                    </div>
                                </div>
                                
                                <!-- Шаг 2: Выбор мастера -->
                                <div class="form-step" id="demo-step2">
                                    <h6 class="mb-3">Выберите мастера</h6>
                                    <div class="master-options">
                                        <div class="master-option selected">
                                            <div class="master-avatar">
                                                <div class="avatar-placeholder">А</div>
                                            </div>
                                            <div class="master-info">
                                                <h5 class="mb-1">Анна</h5>
                                                <p>Мастер маникюра</p>
                                            </div>
                                        </div>
                                        <div class="master-option">
                                            <div class="master-avatar">
                                                <div class="avatar-placeholder">М</div>
                                            </div>
                                            <div class="master-info">
                                                <h5 class="mb-1">Мария</h5>
                                                <p>Парикмахер</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-actions mt-3">
                                        <button class="btn btn-secondary btn-sm me-2">Назад</button>
                                        <button class="btn btn-primary btn-sm">Далее <i class="fas fa-arrow-right ms-1"></i></button>
                                    </div>
                                </div>
                                
                                <!-- Шаг 3: Выбор даты и времени -->
                                <div class="form-step" id="demo-step3">
                                    <h6 class="mb-3">Выберите дату и время</h6>
                                    <div class="calendar-demo">
                                        <div class="calendar-header">
                                            <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-chevron-left"></i></button>
                                            <h5 class="mb-0">Январь 2025</h5>
                                            <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-chevron-right"></i></button>
                                        </div>
                                        <div class="calendar-grid-demo">
                                            <div class="calendar-day-demo disabled">1</div>
                                            <div class="calendar-day-demo disabled">2</div>
                                            <div class="calendar-day-demo">3</div>
                                            <div class="calendar-day-demo">4</div>
                                            <div class="calendar-day-demo selected">5</div>
                                            <div class="calendar-day-demo">6</div>
                                            <div class="calendar-day-demo">7</div>
                                        </div>
                                    </div>
                                    <div class="time-slots-demo mt-3">
                                        <h6 class="mb-2">Доступное время</h6>
                                        <div class="time-slots-grid-demo">
                                            <div class="time-slot-demo">10:00</div>
                                            <div class="time-slot-demo">11:00</div>
                                            <div class="time-slot-demo selected">14:00</div>
                                            <div class="time-slot-demo">15:00</div>
                                        </div>
                                    </div>
                                    <div class="form-actions mt-3">
                                        <button class="btn btn-secondary btn-sm me-2">Назад</button>
                                        <button class="btn btn-primary btn-sm">Далее <i class="fas fa-arrow-right ms-1"></i></button>
                                    </div>
                                </div>
                                
                                <!-- Шаг 4: Данные клиента -->
                                <div class="form-step" id="demo-step4">
                                    <h6 class="mb-3">Ваши данные</h6>
                                    <div class="form-fields">
                                        <div class="form-group mb-2">
                                            <input type="text" class="form-control form-control-sm" placeholder="Ваше имя" value="Анна Петрова">
                                        </div>
                                        <div class="form-group mb-2">
                                            <input type="tel" class="form-control form-control-sm" placeholder="Телефон" value="+380 99 123 45 67">
                                        </div>
                                        <div class="form-group mb-3">
                                            <textarea class="form-control form-control-sm" rows="2" placeholder="Комментарий (необязательно)"></textarea>
                                        </div>
                                    </div>
                                    <div class="form-actions">
                                        <button class="btn btn-secondary btn-sm me-2">Назад</button>
                                        <button class="btn btn-success btn-sm">Записаться <i class="fas fa-check ms-1"></i></button>
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

<!-- Telegram уведомления Section -->
<section id="telegram-notifications" class="py-5 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 order-lg-2">
                <div class="feature-content">
                    <div class="feature-icon mb-4">
                        <div class="stat-icon bg-flat-color-3">
                            <i class="fab fa-telegram"></i>
                        </div>
                    </div>
                    <h2 class="display-5 fw-bold mb-4">📱 Telegram уведомления</h2>
                    <p class="lead mb-4">Получайте мгновенные уведомления о новых записях, отменах и изменениях в Telegram. Быстрая связь с клиентами.</p>
                    <div class="feature-benefits">
                        <div class="benefit-item mb-3">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>Мгновенные уведомления о новых записях</span>
                        </div>
                        <div class="benefit-item mb-3">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>Уведомления об отменах и переносах</span>
                        </div>
                        <div class="benefit-item mb-3">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>Автоматические напоминания клиентам</span>
                        </div>
                        <div class="benefit-item mb-3">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>Статистика и отчеты прямо в чате</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 order-lg-1">
                <div class="feature-demo">
                    <div class="telegram-preview">
                        <div class="telegram-chat">
                            <div class="chat-header bg-primary text-white p-2 rounded-top">
                                <i class="fab fa-telegram me-2"></i>CRM Studio Bot
                            </div>
                            <div class="chat-messages p-3 bg-white rounded-bottom">
                                <div class="message bot-message mb-2">
                                    <div class="message-content bg-light p-2 rounded">
                                        <small class="text-muted">Новая запись!</small>
                                        <div>Клиент: Анна Петрова</div>
                                        <div>Услуга: Маникюр</div>
                                        <div>Дата: 15.01.2025, 14:00</div>
                                    </div>
                                </div>
                                <div class="message bot-message mb-2">
                                    <div class="message-content bg-light p-2 rounded">
                                        <small class="text-muted">Напоминание</small>
                                        <div>Завтра в 10:00 - Массаж</div>
                                        <div>Клиент: Мария Козлова</div>
                                    </div>
                                </div>
                                <div class="message bot-message">
                                    <div class="message-content bg-light p-2 rounded">
                                        <small class="text-muted">Статистика за сегодня</small>
                                        <div>✅ Записей: 8</div>
                                        <div>💰 Выручка: ₴12,500</div>
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

<!-- Email рассылки Section -->
<section id="email-marketing" class="py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="feature-content">
                    <div class="feature-icon mb-4">
                        <div class="stat-icon bg-flat-color-2">
                            <i class="fas fa-envelope"></i>
                        </div>
                    </div>
                    <h2 class="display-5 fw-bold mb-4">📧 Email рассылки</h2>
                    <p class="lead mb-4">Автоматические email уведомления для клиентов. Напоминания о записях, поздравления с днями рождения, акции и спецпредложения.</p>
                    <div class="feature-benefits">
                        <div class="benefit-item mb-3">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>Автоматические напоминания о записях</span>
                        </div>
                        <div class="benefit-item mb-3">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>Поздравления с днями рождения</span>
                        </div>
                        <div class="benefit-item mb-3">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>Персональные акции и скидки</span>
                        </div>
                        <div class="benefit-item mb-3">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>Красивые шаблоны писем</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="feature-demo">
                    <div class="email-preview">
                        <div class="email-template">
                            <div class="email-header bg-gradient-primary text-white p-3 rounded-top">
                                <h6 class="mb-0"><i class="fas fa-envelope me-2"></i>Напоминание о записи</h6>
                            </div>
                            <div class="email-body p-3 bg-white rounded-bottom">
                                <div class="email-content">
                                    <h6>Здравствуйте, Анна!</h6>
                                    <p>Напоминаем о записи на завтра:</p>
                                    <div class="appointment-details bg-light p-3 rounded mb-3">
                                        <div><strong>Услуга:</strong> Маникюр</div>
                                        <div><strong>Дата:</strong> 15 января 2025</div>
                                        <div><strong>Время:</strong> 14:00</div>
                                        <div><strong>Мастер:</strong> Елена</div>
                                    </div>
                                    <p>Ждем вас в салоне!</p>
                                    <button class="btn btn-primary btn-sm">Подтвердить</button>
                                    <button class="btn btn-outline-secondary btn-sm ms-2">Перенести</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Виджет для сайта Section -->
<section id="website-widget" class="py-5 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 order-lg-2">
                <div class="feature-content">
                    <div class="feature-icon mb-4">
                        <div class="stat-icon bg-flat-color-4">
                            <i class="fas fa-code"></i>
                        </div>
                    </div>
                    <h2 class="display-5 fw-bold mb-4">🌐 Виджет для сайта</h2>
                    <p class="lead mb-4">Красивый и функциональный виджет для вашего сайта. Простая интеграция одной строкой кода.</p>
                    <div class="feature-benefits">
                        <div class="benefit-item mb-3">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>Простая интеграция одной строкой кода</span>
                        </div>
                        <div class="benefit-item mb-3">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>Адаптивный дизайн для всех устройств</span>
                        </div>
                        <div class="benefit-item mb-3">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>Настройка под дизайн вашего сайта</span>
                        </div>
                        <div class="benefit-item mb-3">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>Аналитика кликов и конверсий</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 order-lg-1">
                <div class="feature-demo">
                    <div class="code-preview">
                        <div class="code-header bg-dark text-white p-2 rounded-top">
                            <small><i class="fas fa-code me-2"></i>Код для вставки</small>
                        </div>
                        <div class="code-body bg-dark text-light p-3 rounded-bottom">
                            <pre class="mb-0"><code>&lt;script src="https://crmstudio.com/widget.js"&gt;&lt;/script&gt;
&lt;script&gt;
  CRMStudio.init({
    salonId: 'your-salon-id',
    theme: 'light',
    position: 'bottom-right'
  });
&lt;/script&gt;</code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Система ролей Section -->
<section id="roles-system" class="py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="feature-content">
                    <div class="feature-icon mb-4">
                        <div class="stat-icon bg-flat-color-5">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                    </div>
                    <h2 class="display-5 fw-bold mb-4">🔒 Система ролей и прав</h2>
                    <p class="lead mb-4">Гибкая система ролей для разных сотрудников. Администратор, менеджер, мастер - каждый видит только нужную информацию.</p>
                    <div class="feature-benefits">
                        <div class="benefit-item mb-3">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>Роли: Администратор, Менеджер, Мастер</span>
                        </div>
                        <div class="benefit-item mb-3">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>Гибкие права доступа к функциям</span>
                        </div>
                        <div class="benefit-item mb-3">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>Безопасность данных и конфиденциальность</span>
                        </div>
                        <div class="benefit-item mb-3">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>Аудит действий пользователей</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="feature-demo">
                    <div class="roles-preview">
                        <div class="roles-cards">
                            <div class="role-card mb-3">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0"><i class="fas fa-crown me-2"></i>Администратор</h6>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-unstyled mb-0">
                                            <li><i class="fas fa-check text-success me-2"></i>Полный доступ ко всем функциям</li>
                                            <li><i class="fas fa-check text-success me-2"></i>Управление пользователями</li>
                                            <li><i class="fas fa-check text-success me-2"></i>Финансовая отчетность</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="role-card mb-3">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0"><i class="fas fa-user-tie me-2"></i>Менеджер</h6>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-unstyled mb-0">
                                            <li><i class="fas fa-check text-success me-2"></i>Управление записями</li>
                                            <li><i class="fas fa-check text-success me-2"></i>Работа с клиентами</li>
                                            <li><i class="fas fa-check text-success me-2"></i>Базовая аналитика</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="role-card">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-success text-white">
                                        <h6 class="mb-0"><i class="fas fa-user me-2"></i>Мастер</h6>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-unstyled mb-0">
                                            <li><i class="fas fa-check text-success me-2"></i>Просмотр своих записей</li>
                                            <li><i class="fas fa-check text-success me-2"></i>Расписание работы</li>
                                            <li><i class="fas fa-check text-success me-2"></i>Личная статистика</li>
                                        </ul>
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

<!-- Statistics Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row text-center">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card bg-success-light interactive-element">
                    <div class="stat-icon bg-flat-color-1">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-title">Клиентов</h3>
                        <p class="stat-value counter" data-target="2500">2,500+</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card bg-info-light interactive-element">
                    <div class="stat-icon bg-flat-color-3">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-title">Записей</h3>
                        <p class="stat-value counter" data-target="15000">15,000+</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card bg-warning-light interactive-element">
                    <div class="stat-icon bg-flat-color-4">
                        <i class="fas fa-coins"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-title">Прибыль</h3>
                        <p class="stat-value counter" data-target="500000">₴500,000+</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card bg-primary-light interactive-element">
                    <div class="stat-icon bg-flat-color-2">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-title">Оценка</h3>
                        <p class="stat-value">4.9/5</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="py-5">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-lg-8 mx-auto">
                <h2 class="display-5 fw-bold mb-4 section-title">Отзывы наших клиентов</h2>
                <p class="lead text-muted">Что говорят владельцы салонов красоты о CRM Studio</p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm card-3d">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar me-3 bg-gradient-primary d-flex align-items-center justify-content-center">
                                <i class="fas fa-user text-white"></i>
                            </div>
                            <div>
                                <h5 class="mb-1">Анна Петрова</h5>
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
                        <p class="card-text">"Веб-запись через виджет увеличила количество клиентов на 40%! Клиенты записываются в любое время, даже ночью. Telegram уведомления помогают не пропустить ни одной записи."</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm card-3d">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar me-3 bg-gradient-info d-flex align-items-center justify-content-center">
                                <i class="fas fa-user text-white"></i>
                            </div>
                            <div>
                                <h5 class="mb-1">Михаил Сидоров</h5>
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
                        <p class="card-text">"Система ролей и прав очень удобна. Каждый мастер видит только свои записи, администратор контролирует все процессы. Email рассылки помогают удерживать клиентов."</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm card-3d">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar me-3 bg-gradient-success d-flex align-items-center justify-content-center">
                                <i class="fas fa-user text-white"></i>
                            </div>
                            <div>
                                <h5 class="mb-1">Елена Козлова</h5>
                                <small class="text-muted">Салон "Грация"</small>
                            </div>
                        </div>
                        <div class="mb-3">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                        <p class="card-text">"Аналитика и отчеты показывают реальную картину бизнеса. Видим, какие услуги популярны, какие мастера эффективнее работают. Прибыль выросла на 25% за полгода."</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5 bg-gradient-primary text-white">
    <div class="container">
        <div class="row text-center">
            <div class="col-lg-8 mx-auto">
                <h2 class="display-5 fw-bold mb-4">Готовы автоматизировать свой салон?</h2>
                <p class="lead mb-4">Присоединяйтесь к тысячам салонов красоты, которые уже используют CRM Studio</p>
                <div class="d-flex gap-3 justify-content-center">
                    <a href="#" class="btn btn-light btn-lg animate-pulse" data-bs-toggle="modal" data-bs-target="#registerModal">
                        <i class="fas fa-rocket me-2"></i>Начать бесплатно
                    </a>
                    <a href="/pricing" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-list me-2"></i>Посмотреть тарифы
                    </a>
                </div>
            </div>
        </div>
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
