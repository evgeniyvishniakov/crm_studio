@extends('landing.layouts.app')

@php
    use Illuminate\Support\Facades\Auth;
@endphp

@section('title', 'Trimora - Система управления салоном красоты')
@section('description', 'Профессиональная система Trimora для управления салоном красоты с веб-записью, Telegram уведомлениями и виджетом для сайта')

@section('content')

<!-- Hero Section -->
    <section class="hero-section bg-light">
    <div class="container">
        <div class="hero-slider-container">
            <div class="hero-slider">
                <div class="slide active" data-slide="1">
                    <div class="slide-content">
                        <h1 class="slide-title">Управляйте своим салоном красоты с помощью Trimora</h1>
                        <p class="slide-description">Современная система управления салоном красоты с веб-записью, Telegram уведомлениями и виджетом для сайта. Увеличьте количество клиентов и упростите работу салона.</p>
                        <div class="hero-buttons">
                            @if(Auth::guard('client')->check())
                                <a href="{{ route('dashboard') }}" class="btn btn-primary me-3 animate-pulse" aria-label="Войти в систему">
                                    <i class="fas fa-sign-in-alt me-2" aria-hidden="true"></i>Войти в систему
                                </a>
                            @else
                                <a href="#" class="btn btn-primary me-3 animate-pulse" data-bs-toggle="modal" data-bs-target="#registerModal" aria-label="Открыть форму регистрации">
                                    <i class="fas fa-rocket me-2" aria-hidden="true"></i>Попробовать бесплатно 7 дней
                                </a>
                            @endif
                            <a href="#features-grid" class="btn btn-outline-primary">
                                <i class="fas fa-play me-2"></i>Смотреть функции
                            </a>
                        </div>
                        <div class="slide-features">
                            <div class="feature-item">
                                <i class="fas fa-calendar-check text-primary"></i>
                                <span>Онлайн-запись 24/7</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-bell text-success"></i>
                                <span>Автоматические уведомления</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-chart-line text-warning"></i>
                                <span>Аналитика и отчеты</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="slide" data-slide="2">
                    <div class="slide-content">
                        <h1 class="slide-title">Автоматизируйте записи клиентов</h1>
                        <p class="slide-description">Trimora - современная система с веб-записью, Telegram уведомлениями и виджетом для сайта. Увеличьте количество клиентов и упростите работу салона.</p>
                        <div class="hero-buttons">
                            @if(Auth::guard('client')->check())
                                <a href="{{ route('dashboard') }}" class="btn btn-primary me-3 animate-pulse" aria-label="Войти в систему">
                                    <i class="fas fa-sign-in-alt me-2" aria-hidden="true"></i>Войти в систему
                                </a>
                            @else
                                <a href="#" class="btn btn-primary me-3 animate-pulse" data-bs-toggle="modal" data-bs-target="#registerModal" aria-label="Открыть форму регистрации">
                                    <i class="fas fa-rocket me-2" aria-hidden="true"></i>Попробовать бесплатно 7 дней
                                </a>
                            @endif
                            <a href="#" class="btn btn-outline-primary">
                                <i class="fas fa-play me-2"></i>Смотреть функции
                            </a>
                        </div>
                        <div class="slide-features">
                            <div class="feature-item">
                                <i class="fas fa-users text-info"></i>
                                <span>База клиентов</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-wallet text-primary"></i>
                                <span>Финансовый учет</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-cog text-success"></i>
                                <span>Гибкие настройки</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="slide" data-slide="3">
                    <div class="slide-content">
                        <h1 class="slide-title">Универсальная CRM для любой ниши</h1>
                        <p class="slide-description">Trimora подходит для салонов красоты, медицинских клиник, автосервисов, образовательных центров и многих других сфер бизнеса.</p>
                        <div class="hero-buttons">
                            @if(Auth::guard('client')->check())
                                <a href="{{ route('dashboard') }}" class="btn btn-primary me-3 animate-pulse" aria-label="Войти в систему">
                                    <i class="fas fa-sign-in-alt me-2" aria-hidden="true"></i>Войти в систему
                                </a>
                            @else
                                <a href="#" class="btn btn-primary me-3 animate-pulse" data-bs-toggle="modal" data-bs-target="#registerModal" aria-label="Открыть форму регистрации">
                                    <i class="fas fa-rocket me-2" aria-hidden="true"></i>Попробовать бесплатно 7 дней
                                </a>
                            @endif
                            <a href="#niches-section" class="btn btn-outline-primary">
                                <i class="fas fa-briefcase me-2"></i>Смотреть ниши
                            </a>
                        </div>
                        <div class="slide-features">
                            <div class="feature-item">
                                <i class="fas fa-cut text-primary"></i>
                                <span>Красота и уход</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-heartbeat text-danger"></i>
                                <span>Медицинские услуги</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-car text-info"></i>
                                <span>Автосервисы</span>
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

                    <h2 class="display-5 fw-bold mb-4">🔗 Веб-запись через ссылки</h2>
                    <p class="lead mb-4">Клиенты могут записываться на услуги через ссылки в Instagram, Facebook и других социальных сетях. Простая интеграция в любой профиль.</p>
                    <div class="feature-benefits">
                        <div class="benefit-item mb-3">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>Ссылка для Instagram в био</span>
                        </div>
                        <div class="benefit-item mb-3">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>Запись за пару кликов</span>
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
                                                <div class="avatar-placeholder">Е</div>
            </div>
                                            <div class="master-info">
                                                <h5 class="mb-1">Елена</h5>
                                                <p>Парикмахер</p>
                        </div>
                    </div>
                                        <div class="master-option">
                                            <div class="master-avatar">
                                                <div class="avatar-placeholder">К</div>
                </div>
                                            <div class="master-info">
                                                <h5 class="mb-1">Катерина</h5>
                                                <p>Массажист</p>
            </div>
        </div>
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
                                        
                                        <!-- Дни недели -->
                                        <div class="calendar-weekdays">
                                            <div class="weekday">Пн</div>
                                            <div class="weekday">Вт</div>
                                            <div class="weekday">Ср</div>
                                            <div class="weekday">Чт</div>
                                            <div class="weekday">Пт</div>
                                            <div class="weekday">Сб</div>
                                            <div class="weekday">Вс</div>
                                        </div>
                                        
                                        <div class="calendar-grid-demo">
                                            <!-- Предыдущий месяц -->
                                            <div class="calendar-day-demo other-month">30</div>
                                            <div class="calendar-day-demo other-month">31</div>
                                            
                                            <!-- Текущий месяц -->
                                            <div class="calendar-day-demo disabled">1</div>
                                            <div class="calendar-day-demo disabled">2</div>
                                            <div class="calendar-day-demo">3</div>
                                            <div class="calendar-day-demo">4</div>
                                            <div class="calendar-day-demo today">5</div>
                                            <div class="calendar-day-demo selected">6</div>
                                            <div class="calendar-day-demo">7</div>
                                            <div class="calendar-day-demo">8</div>
                                            <div class="calendar-day-demo">9</div>
                                            <div class="calendar-day-demo">10</div>
                                            <div class="calendar-day-demo">11</div>
                                            <div class="calendar-day-demo">12</div>
                                            <div class="calendar-day-demo">13</div>
                                            <div class="calendar-day-demo">14</div>
                                            <div class="calendar-day-demo">15</div>
                                            <div class="calendar-day-demo">16</div>
                                            <div class="calendar-day-demo">17</div>
                                            <div class="calendar-day-demo">18</div>
                                            <div class="calendar-day-demo">19</div>
                                            <div class="calendar-day-demo">20</div>
                                            <div class="calendar-day-demo">21</div>
                                            <div class="calendar-day-demo">22</div>
                                            <div class="calendar-day-demo">23</div>
                                            <div class="calendar-day-demo">24</div>
                                            <div class="calendar-day-demo">25</div>
                                            <div class="calendar-day-demo">26</div>
                                            <div class="calendar-day-demo">27</div>
                                            <div class="calendar-day-demo">28</div>
                                            <div class="calendar-day-demo">29</div>
                                            <div class="calendar-day-demo">30</div>
                                            <div class="calendar-day-demo">31</div>
                                            
                                            <!-- Следующий месяц -->
                                            <div class="calendar-day-demo other-month">1</div>
                                            <div class="calendar-day-demo other-month">2</div>
                                </div>
                                        </div>
                                    <div class="time-slots-demo mt-3">
                                        <h6 class="mb-2">Доступное время</h6>
                                                                                <div class="time-slots-grid-demo">
                                            <div class="time-slot-demo">10:00</div>
                                            <div class="time-slot-demo">11:00</div>
                                            <div class="time-slot-demo">12:00</div>
                                            <div class="time-slot-demo">13:00</div>
                                            <div class="time-slot-demo selected">14:00</div>
                                            <div class="time-slot-demo">15:00</div>
                                            <div class="time-slot-demo">16:00</div>
                                            <div class="time-slot-demo">17:00</div>
                                        </div>
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
                                        <button class="btn btn-success btn-sm w-100">Записаться <i class="fas fa-check ms-1"></i></button>
                                    </div>
                                        </div>
                                        
                                <!-- Индикатор прогресса -->
                                <div class="demo-progress mt-3">
                                    <div class="progress-dots">
                                        <div class="progress-dot active"></div>
                                        <div class="progress-dot"></div>
                                        <div class="progress-dot"></div>
                                        <div class="progress-dot"></div>
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
                            <span>Напоминание о завтрашних записях</span>
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
                                <i class="fab fa-telegram me-2"></i>Trimora Bot
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
                                        <div>📦 Товаров продано: 5</div>
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
                    <h2 class="display-5 fw-bold mb-4">📧 Email рассылки</h2>
                    <p class="lead mb-4">Автоматические email уведомления для клиентов. Напоминания о записях, поздравления с днями рождения, акции и спецпредложения.</p>
                    <div class="feature-benefits">
                        <div class="benefit-item mb-3">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>Автоматические напоминания о записях</span>
                                            </div>
                        <div class="benefit-item mb-3">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>Подтверждение записи клиенту</span>
                                            </div>
                        <div class="benefit-item mb-3">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>Напоминание о завтрашних записях</span>
                                            </div>
                        <div class="benefit-item mb-3">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>Уведомления об отменах и переносах</span>
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
                            <span>Настройка кнопки под дизайн вашего сайта</span>
                                            </div>
                        <div class="benefit-item mb-3">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>Быстрая установка виджета</span>
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

<!-- Аналитика Section -->
<section id="analytics" class="py-5 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 order-lg-2">
                <div class="feature-content">
                    <h2 class="display-5 fw-bold mb-4">📊 Подробная аналитика</h2>
                    <p class="lead mb-4">Полная картина вашего бизнеса с детальными отчетами и графиками. Отслеживайте прибыль, клиентов, популярные услуги и эффективность сотрудников.</p>
                    <div class="feature-benefits">
                        <div class="benefit-item mb-3">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>Аналитика по клиентам и записям</span>
                        </div>
                        <div class="benefit-item mb-3">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>Отчеты по товарообороту и прибыли</span>
                        </div>
                        <div class="benefit-item mb-3">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>Популярность услуг и мастеров</span>
                        </div>
                        <div class="benefit-item mb-3">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>Интерактивные графики и диаграммы</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 order-lg-1">
                <div class="feature-demo">
                    <div class="analytics-preview">
                        <div class="analytics-dashboard">
                            <div class="dashboard-header bg-gradient-primary text-white p-3 rounded-top">
                                <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Аналитика салона</h6>
                            </div>
                            <div class="dashboard-content p-3 bg-white rounded-bottom">
                                <div class="row g-3 mb-4">
                                    <div class="col-6">
                                        <div class="stat-mini-large bg-success-light p-3 rounded">
                                            <small class="text-muted d-block mb-1">Прибыль</small>
                                            <div class="fw-bold fs-5">₴125,400</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="stat-mini-large bg-info-light p-3 rounded">
                                            <small class="text-muted d-block mb-1">Клиенты</small>
                                            <div class="fw-bold fs-5">1,247</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- График 1: Вертикальные столбики -->
                                <div class="chart-step active" id="chart-step1">
                                    <div class="chart-preview bg-light p-2 rounded">
                                        <div class="chart-bars">
                                            <div class="chart-bar" style="height: 60%; background: linear-gradient(135deg, #667eea, #764ba2);"></div>
                                            <div class="chart-bar" style="height: 80%; background: linear-gradient(135deg, #667eea, #764ba2);"></div>
                                            <div class="chart-bar" style="height: 45%; background: linear-gradient(135deg, #667eea, #764ba2);"></div>
                                            <div class="chart-bar" style="height: 90%; background: linear-gradient(135deg, #667eea, #764ba2);"></div>
                                            <div class="chart-bar" style="height: 70%; background: linear-gradient(135deg, #667eea, #764ba2);"></div>
                                            <div class="chart-bar" style="height: 85%; background: linear-gradient(135deg, #667eea, #764ba2);"></div>
                                            <div class="chart-bar" style="height: 55%; background: linear-gradient(135deg, #667eea, #764ba2);"></div>
                                        </div>
                                        <small class="text-muted d-block text-center mt-2">Динамика продаж за неделю</small>
                                    </div>
                                </div>
                                
                                <!-- График 2: Горизонтальные столбики -->
                                <div class="chart-step" id="chart-step2">
                                    <div class="chart-preview bg-light p-3 rounded">
                                        <div class="horizontal-bars-large">
                                            <div class="horizontal-bar-large">
                                                <span class="bar-label-large">Маникюр</span>
                                                <div class="bar-container-large">
                                                    <div class="bar-fill-large" style="width: 85%;"></div>
                            </div>
                                                <span class="bar-value-large">85%</span>
                                            </div>
                                            <div class="horizontal-bar-large">
                                                <span class="bar-label-large">Стрижка</span>
                                                <div class="bar-container-large">
                                                    <div class="bar-fill-large" style="width: 65%;"></div>
                                                </div>
                                                <span class="bar-value-large">65%</span>
                                            </div>
                                            <div class="horizontal-bar-large">
                                                <span class="bar-label-large">Массаж</span>
                                                <div class="bar-container-large">
                                                    <div class="bar-fill-large" style="width: 45%;"></div>
                                                </div>
                                                <span class="bar-value-large">45%</span>
                                            </div>
                                        </div>
                                        <small class="text-muted d-block text-center mt-2">Популярность услуг</small>
                        </div>
                    </div>
                    
                                <!-- График 3: Круговая диаграмма -->
                                <div class="chart-step" id="chart-step3">
                                    <div class="chart-preview bg-light p-3 rounded">
                                        <div class="pie-chart-large-container">
                                            <div class="pie-chart-large">
                                                <div class="pie-segment-large"></div>
                                                <div class="pie-center-large">
                                                    <div class="pie-value-large">₴125K</div>
                                                    <div class="pie-label-large">Прибыль</div>
                        </div>
                    </div>
                                            <div class="pie-legend-large">
                                                <div class="legend-item-large">
                                                    <span class="legend-color-large" style="background: #667eea;"></span>
                                                    <span class="legend-text-large">Услуги (33%)</span>
                        </div>
                                                <div class="legend-item-large">
                                                    <span class="legend-color-large" style="background: #e5e7eb;"></span>
                                                    <span class="legend-text-large">Товары (67%)</span>
                    </div>
                                            </div>
                                        </div>
                                        <small class="text-muted d-block text-center mt-2">Структура доходов</small>
                </div>
            </div>
            
                                <!-- График 4: Линейный график -->
                                <div class="chart-step" id="chart-step4">
                                    <div class="chart-preview bg-light p-3 rounded">
                                        <div class="line-chart-large-container">
                                            <svg width="300" height="120" viewBox="0 0 300 120">
                                                <defs>
                                                    <linearGradient id="lineGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                                                        <stop offset="0%" style="stop-color:#667eea;stop-opacity:0.8" />
                                                        <stop offset="100%" style="stop-color:#667eea;stop-opacity:0.1" />
                                                    </linearGradient>
                                                </defs>
                                                <!-- Сетка -->
                                                <line x1="0" y1="24" x2="300" y2="24" stroke="#e5e7eb" stroke-width="1"/>
                                                <line x1="0" y1="48" x2="300" y2="48" stroke="#e5e7eb" stroke-width="1"/>
                                                <line x1="0" y1="72" x2="300" y2="72" stroke="#e5e7eb" stroke-width="1"/>
                                                <line x1="0" y1="96" x2="300" y2="96" stroke="#e5e7eb" stroke-width="1"/>
                                                
                                                <!-- Подписи осей Y -->
                                                <text x="5" y="29" font-size="10" fill="#6b7280">350</text>
                                                <text x="5" y="53" font-size="10" fill="#6b7280">280</text>
                                                <text x="5" y="77" font-size="10" fill="#6b7280">180</text>
                                                <text x="5" y="101" font-size="10" fill="#6b7280">120</text>
                                                
                                                <!-- Подписи осей X -->
                                                <text x="20" y="115" font-size="10" fill="#6b7280" text-anchor="middle">Янв</text>
                                                <text x="60" y="115" font-size="10" fill="#6b7280" text-anchor="middle">Фев</text>
                                                <text x="100" y="115" font-size="10" fill="#6b7280" text-anchor="middle">Мар</text>
                                                <text x="140" y="115" font-size="10" fill="#6b7280" text-anchor="middle">Апр</text>
                                                <text x="180" y="115" font-size="10" fill="#6b7280" text-anchor="middle">Май</text>
                                                <text x="220" y="115" font-size="10" fill="#6b7280" text-anchor="middle">Июн</text>
                                                <text x="260" y="115" font-size="10" fill="#6b7280" text-anchor="middle">Июл</text>
                                                <text x="280" y="115" font-size="10" fill="#6b7280" text-anchor="middle">Авг</text>
                                                
                                                <!-- Линия графика -->
                                                <path d="M 20 96 L 60 72 L 100 48 L 140 32 L 180 24 L 220 40 L 260 16 L 280 8" 
                                                      stroke="#667eea" stroke-width="3" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                                                
                                                <!-- Точки на графике -->
                                                <circle cx="20" cy="96" r="4" fill="#667eea"/>
                                                <circle cx="60" cy="72" r="4" fill="#667eea"/>
                                                <circle cx="100" cy="48" r="4" fill="#667eea"/>
                                                <circle cx="140" cy="32" r="4" fill="#667eea"/>
                                                <circle cx="180" cy="24" r="4" fill="#667eea"/>
                                                <circle cx="220" cy="40" r="4" fill="#667eea"/>
                                                <circle cx="260" cy="16" r="4" fill="#667eea"/>
                                                <circle cx="280" cy="8" r="4" fill="#667eea"/>
                                                
                                                <!-- Заливка под линией -->
                                                <path d="M 20 96 L 60 72 L 100 48 L 140 32 L 180 24 L 220 40 L 260 16 L 280 8 L 280 120 L 20 120 Z" 
                                                      fill="url(#lineGradient)"/>
                                            </svg>
                                </div>
                                        <small class="text-muted d-block text-center mt-2">Рост клиентской базы</small>
                            </div>
                        </div>
                        
                                <!-- Индикатор прогресса для графиков -->
                                <div class="chart-progress mt-3">
                                    <div class="chart-dots">
                                        <div class="chart-dot active"></div>
                                        <div class="chart-dot"></div>
                                        <div class="chart-dot"></div>
                                        <div class="chart-dot"></div>
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



<!-- Testimonials Section -->
<section class="py-5">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-lg-8 mx-auto">
                <h2 class="display-5 fw-bold mb-4 section-title">Отзывы наших клиентов</h2>
                <p class="lead text-muted">Что говорят владельцы салонов красоты о Trimora</p>
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

<!-- CTA Middle Section -->
<section class="py-5 bg-gradient-primary text-white">
    <div class="container">
        <div class="row text-center">
            <div class="col-lg-8 mx-auto">
                <h2 class="display-5 fw-bold mb-4">Попробуйте Trimora бесплатно</h2>
                <p class="lead mb-4">7 дней полного доступа ко всем функциям без ограничений</p>
                <div class="d-flex gap-3 justify-content-center">
                    @if(Auth::guard('client')->check())
                        <a href="{{ route('dashboard') }}" class="btn btn-light btn-lg animate-pulse">
                            <i class="fas fa-sign-in-alt me-2"></i>Войти в систему
                        </a>
                    @else
                        <a href="#" class="btn btn-light btn-lg animate-pulse" data-bs-toggle="modal" data-bs-target="#registerModal">
                            <i class="fas fa-rocket me-2"></i>Попробовать бесплатно 7 дней
                        </a>
                    @endif
                    <a href="#features-grid" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-list me-2"></i>Посмотреть функции
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Функционал Section -->
<section id="features-grid" class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold mb-4">🚀 Полный функционал CRM</h2>
            <p class="lead">Все необходимые инструменты для управления вашим бизнесом в одном месте</p>
        </div>
        
        <div class="row g-4">
            <!-- Онлайн-запись -->
            <div class="col-lg-3 col-md-6">
                <div class="feature-grid-card">
                    <div class="feature-grid-icon">
                        <i class="fas fa-calendar-plus"></i>
                    </div>
                    <h5>Онлайн-запись</h5>
                    <p>Дайте вашим клиентам возможность записываться 24/7 через ссылки в соцсетях</p>
                </div>
            </div>
            
            <!-- Электронный журнал -->
            <div class="col-lg-3 col-md-6">
                <div class="feature-grid-card">
                    <div class="feature-grid-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h5>Электронный журнал</h5>
                    <p>Составляйте и обрабатывайте расписание для каждого сотрудника</p>
                </div>
            </div>
            
            <!-- Клиентская база -->
            <div class="col-lg-3 col-md-6">
                <div class="feature-grid-card">
                    <div class="feature-grid-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h5>Клиентская база</h5>
                    <p>Управляйте всей информацией о своих клиентах в одном месте</p>
                </div>
            </div>
            
            <!-- Уведомления -->
            <div class="col-lg-3 col-md-6">
                <div class="feature-grid-card">
                    <div class="feature-grid-icon">
                        <i class="fas fa-bell"></i>
                    </div>
                    <h5>Уведомления</h5>
                    <p>Различные автоматизированные SMS, WhatsApp и Email уведомления</p>
                </div>
            </div>
            
            <!-- Управление товарами -->
            <div class="col-lg-3 col-md-6">
                <div class="feature-grid-card">
                    <div class="feature-grid-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <h5>Управление товарами</h5>
                    <p>Полный контроль над товарами, категориями, брендами и поставщиками</p>
                </div>
            </div>
            
            <!-- Инвентаризация -->
            <div class="col-lg-3 col-md-6">
                <div class="feature-grid-card">
                    <div class="feature-grid-icon">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <h5>Инвентаризация</h5>
                    <p>Управляйте инвентарями и автоматически рассчитывайте расходы</p>
                </div>
            </div>
            
            <!-- Финансовая отчетность -->
            <div class="col-lg-3 col-md-6">
                <div class="feature-grid-card">
                    <div class="feature-grid-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h5>Финансовая отчетность</h5>
                    <p>Подробные отчеты о доходах, расходах и остатках</p>
                </div>
            </div>
            
            <!-- Аналитика и статистика -->
            <div class="col-lg-3 col-md-6">
                <div class="feature-grid-card">
                    <div class="feature-grid-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <h5>Аналитика и статистика</h5>
                    <p>Подробные данные и графики, необходимые для роста и расширения бизнеса</p>
                </div>
            </div>
            
            <!-- Программы Лояльности -->
            <div class="col-lg-3 col-md-6">
                <div class="feature-grid-card">
                    <div class="feature-grid-icon">
                        <i class="fas fa-gift"></i>
                    </div>
                    <h5>Программы Лояльности</h5>
                    <p>Создавайте программы лояльности для ваших клиентов</p>
                </div>
            </div>
            
            <!-- Закупки и продажи -->
            <div class="col-lg-3 col-md-6">
                <div class="feature-grid-card">
                    <div class="feature-grid-icon">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                    <h5>Закупки и продажи</h5>
                    <p>Управление закупками товаров и продажами с полной аналитикой</p>
                </div>
            </div>
            
            <!-- Система ролей -->
            <div class="col-lg-3 col-md-6">
                <div class="feature-grid-card">
                    <div class="feature-grid-icon">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <h5>Система ролей</h5>
                    <p>Гибкая система ролей для администраторов, менеджеров и мастеров</p>
                </div>
            </div>
            
            <!-- Резервные копии -->
            <div class="col-lg-3 col-md-6">
                <div class="feature-grid-card">
                    <div class="feature-grid-icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <h5>Зарплата</h5>
                    <p>Автоматический расчёт зарплаты сотрудников с поддержкой бонусов и процентов</p>
                </div>
            </div>
            
            <!-- Управление записями -->
            <div class="col-lg-3 col-md-6">
                <div class="feature-grid-card">
                    <div class="feature-grid-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h5>Управление записями</h5>
                    <p>Полный контроль над записями клиентов и расписанием мастеров</p>
                </div>
            </div>
            
            <!-- Аналитика по клиентам -->
            <div class="col-lg-3 col-md-6">
                <div class="feature-grid-card">
                    <div class="feature-grid-icon">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <h5>Аналитика по клиентам</h5>
                    <p>Детальная аналитика клиентской базы и динамики записей</p>
                </div>
            </div>
            
            <!-- Аналитика товарооборота -->
            <div class="col-lg-3 col-md-6">
                <div class="feature-grid-card">
                    <div class="feature-grid-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h5>Аналитика товарооборота</h5>
                    <p>Подробные отчеты по продажам, закупкам и товарообороту</p>
                </div>
            </div>
            
            <!-- Виджет и Ссылки -->
            <div class="col-lg-3 col-md-6">
                <div class="feature-grid-card">
                    <div class="feature-grid-icon">
                        <i class="fas fa-link"></i>
                    </div>
                    <h5>Виджет и Ссылки</h5>
                    <p>Генерируйте ссылки и виджеты для вашего бизнеса</p>
                </div>
            </div>
        </div>
        
        <!-- CTA после функций -->
        <div class="text-center mt-5">
            <h3 class="fw-bold mb-3">Понравились функции? Попробуйте прямо сейчас!</h3>
                            <p class="lead text-muted mb-4">7 дней бесплатного тестирования всех возможностей Trimora</p>
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
</section>

<!-- Секция "Для каких ниш подходит" -->
<section id="niches-section" class="niches-section py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold mb-4">🎯 Для каких ниш подходит Trimora?</h2>
            <p class="lead">Универсальная CRM для сервисного бизнеса любого масштаба</p>
        </div>
        
        <div class="row g-4">
            <!-- Красота и уход -->
            <div class="col-lg-3 col-md-6">
                <div class="niche-card">
                    <div class="niche-icon">
                        <i class="fas fa-cut text-primary"></i>
                    </div>
                    <h4 class="niche-title">Красота и уход</h4>
                    <p class="niche-description">Салоны красоты, парикмахерские, маникюрные салоны, СПА-центры, массажные салоны</p>
                </div>
            </div>
            
            <!-- Медицинские услуги -->
            <div class="col-lg-3 col-md-6">
                <div class="niche-card">
                    <div class="niche-icon">
                        <i class="fas fa-heartbeat text-danger"></i>
                    </div>
                    <h4 class="niche-title">Медицинские услуги</h4>
                    <p class="niche-description">Стоматологические клиники, клиники эстетической медицины, физиотерапевтические центры</p>
                </div>
            </div>
            
            <!-- Творческие услуги -->
            <div class="col-lg-3 col-md-6">
                <div class="niche-card">
                    <div class="niche-icon">
                        <i class="fas fa-palette text-warning"></i>
                    </div>
                    <h4 class="niche-title">Творческие услуги</h4>
                    <p class="niche-description">Фотостудии, студии звукозаписи, художественные мастерские, музыкальные школы</p>
                </div>
            </div>
            
            <!-- Автосервисы -->
            <div class="col-lg-3 col-md-6">
                <div class="niche-card">
                    <div class="niche-icon">
                        <i class="fas fa-car text-info"></i>
                    </div>
                    <h4 class="niche-title">Автосервисы</h4>
                    <p class="niche-description">Автомойки, автосервисы, шиномонтажи, автодетейлинг</p>
                </div>
            </div>
            
            <!-- Бытовые услуги -->
            <div class="col-lg-3 col-md-6">
                <div class="niche-card">
                    <div class="niche-icon">
                        <i class="fas fa-tools text-secondary"></i>
                    </div>
                    <h4 class="niche-title">Бытовые услуги</h4>
                    <p class="niche-description">Клининговые компании, ремонтные бригады, электрики, мастера по ремонту</p>
                </div>
            </div>
            
            <!-- Образование -->
            <div class="col-lg-3 col-md-6">
                <div class="niche-card">
                    <div class="niche-icon">
                        <i class="fas fa-graduation-cap text-success"></i>
                    </div>
                    <h4 class="niche-title">Образование</h4>
                    <p class="niche-description">Частные репетиторы, языковые школы, курсы повышения квалификации</p>
                </div>
            </div>
            
            <!-- Ресторанный бизнес -->
            <div class="col-lg-3 col-md-6">
                <div class="niche-card">
                    <div class="niche-icon">
                        <i class="fas fa-utensils text-warning"></i>
                    </div>
                    <h4 class="niche-title">Ресторанный бизнес</h4>
                    <p class="niche-description">Рестораны с предзаказом, кафе с доставкой, кейтеринг-услуги</p>
                </div>
            </div>
            
            <!-- Консалтинговые услуги -->
            <div class="col-lg-3 col-md-6">
                <div class="niche-card">
                    <div class="niche-icon">
                        <i class="fas fa-briefcase text-dark"></i>
                    </div>
                    <h4 class="niche-title">Консалтинговые услуги</h4>
                    <p class="niche-description">Юридические консультации, бухгалтерские услуги, бизнес-консультанты</p>
                </div>
            </div>
        </div>
        
                        <div class="text-center mt-5">
                    <p class="text-muted">Не нашли свою нишу? Trimora легко адаптируется под любой сервисный бизнес!</p>
                </div>
    </div>
</section>



@include('landing.components.register-modal')
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('=== СЛАЙДЕР ЗАГРУЖЕН ===');
    
    const slides = document.querySelectorAll('.slide');
    const heroButtons = document.querySelectorAll('.hero-buttons .btn');
    
    console.log(`Найдено слайдов: ${slides.length}`);
    console.log(`Найдено кнопок: ${heroButtons.length}`);
    
    let currentSlide = 0;
    
    function showSlide(index) {
        slides.forEach((slide, i) => {
            if (i === index) {
                slide.style.display = 'block';
                slide.classList.add('active');
            } else {
                slide.style.display = 'none';
                slide.classList.remove('active');
            }
        });
        currentSlide = index;
    }
    
    function nextSlide() {
        currentSlide = (currentSlide + 1) % slides.length;
        showSlide(currentSlide);
    }
    
    // Показываем первый слайд
    showSlide(0);
    
    // Автоматическое переключение каждые 5 секунд
    setInterval(nextSlide, 5000);
    
    console.log('=== СЛАЙДЕР ГОТОВ ===');
    console.log('ХОВЕР РАБОТАЕТ ЧЕРЕЗ CSS СТИЛИ');
});
</script>
@endpush

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
