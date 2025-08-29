@extends('landing.layouts.app')

@php
    use Illuminate\Support\Facades\Auth;
@endphp

@section('title', __('landing.page_title'))
@section('description', __('landing.page_description'))
@section('keywords', __('landing.page_keywords'))
@section('author', 'Trimora')
@section('robots', 'index, follow')
@section('canonical', url()->current())
@section('og:title', __('landing.page_title'))
@section('og:description', __('landing.page_description'))
@section('og:type', 'website')
@section('og:url', url()->current())
@section('og:locale', app()->getLocale())
@section('og:locale:alternate', implode(',', ['ru', 'en', 'ua']))
@section('twitter:card', 'summary_large_image')
@section('twitter:title', __('landing.page_title'))
@section('twitter:description', __('landing.page_description'))

@section('content')

<!-- Hero Section -->
    <section class="hero-section bg-light">
    <div class="container">
        <div class="hero-slider-container">
            <div class="hero-slider">
                <div class="slide active" data-slide="1">
                    <div class="slide-content">
                                        <h1 class="slide-title">{{ __('landing.hero_title_1') }}</h1>
                <p class="slide-description">{{ __('landing.hero_description_1') }}</p>
                        <div class="hero-buttons">
                            @if(Auth::guard('client')->check())
                                <a href="{{ route('dashboard') }}" class="btn btn-primary me-3 animate-pulse" aria-label="{{ __('landing.enter_system') }}">
                                    <i class="fas fa-sign-in-alt me-2" aria-hidden="true"></i>{{ __('landing.enter_system') }}
                                </a>
                            @else
                                <a href="#" class="btn btn-primary me-3 animate-pulse" data-bs-toggle="modal" data-bs-target="#registerModal" aria-label="{{ __('landing.try_free_7_days') }}">
                                    <i class="fas fa-rocket me-2" aria-hidden="true"></i>{{ __('landing.try_free_7_days') }}
                                </a>
                            @endif
                            <a href="#features-grid" class="btn btn-outline-primary">
                                <i class="fas fa-play me-2"></i>{{ __('landing.view_functions') }}
                            </a>
                        </div>
                        <div class="slide-features">
                            <div class="feature-item">
                                <i class="fas fa-calendar-check text-primary"></i>
                                <span>{{ __('landing.online_booking_24_7') }}</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-bell text-success"></i>
                                <span>{{ __('landing.automatic_notifications') }}</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-chart-line text-warning"></i>
                                <span>{{ __('landing.analytics_reports') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="slide" data-slide="2">
                    <div class="slide-content">
                        <h1 class="slide-title">{{ __('landing.hero_title_2') }}</h1>
                        <p class="slide-description">{{ __('landing.hero_description_2') }}</p>
                        <div class="hero-buttons">
                            @if(Auth::guard('client')->check())
                                <a href="{{ route('dashboard') }}" class="btn btn-primary me-3 animate-pulse" aria-label="{{ __('landing.enter_system') }}">
                                    <i class="fas fa-sign-in-alt me-2" aria-hidden="true"></i>{{ __('landing.enter_system') }}
                                </a>
                            @else
                                <a href="#" class="btn btn-primary me-3 animate-pulse" data-bs-toggle="modal" data-bs-target="#registerModal" aria-label="{{ __('landing.try_free_7_days') }}">
                                    <i class="fas fa-rocket me-2" aria-hidden="true"></i>{{ __('landing.try_free_7_days') }}
                                </a>
                            @endif
                            <a href="#" class="btn btn-outline-primary">
                                <i class="fas fa-play me-2"></i>{{ __('landing.view_functions') }}
                            </a>
                        </div>
                        <div class="slide-features">
                            <div class="feature-item">
                                <i class="fas fa-users text-info"></i>
                                <span>{{ __('landing.client_database') }}</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-wallet text-primary"></i>
                                <span>{{ __('landing.financial_accounting') }}</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-cog text-success"></i>
                                <span>{{ __('landing.flexible_settings') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="slide" data-slide="3">
                    <div class="slide-content">
                        <h1 class="slide-title">{{ __('landing.hero_title_3') }}</h1>
                        <p class="slide-description">{{ __('landing.hero_description_3') }}</p>
                        <div class="hero-buttons">
                            @if(Auth::guard('client')->check())
                                <a href="{{ route('dashboard') }}" class="btn btn-primary me-3 animate-pulse" aria-label="{{ __('landing.enter_system') }}">
                                    <i class="fas fa-sign-in-alt me-2" aria-hidden="true"></i>{{ __('landing.enter_system') }}
                                </a>
                            @else
                                <a href="#" class="btn btn-primary me-3 animate-pulse" data-bs-toggle="modal" data-bs-target="#registerModal" aria-label="{{ __('landing.try_free_7_days') }}">
                                    <i class="fas fa-rocket me-2" aria-hidden="true"></i>{{ __('landing.try_free_7_days') }}
                                </a>
                            @endif
                            <a href="#niches-section" class="btn btn-outline-primary">
                                <i class="fas fa-briefcase me-2"></i>{{ __('landing.view_niches') }}
                            </a>
                        </div>
                        <div class="slide-features">
                            <div class="feature-item">
                                <i class="fas fa-cut text-primary"></i>
                                <span>{{ __('landing.beauty_care') }}</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-heartbeat text-danger"></i>
                                <span>{{ __('landing.medical_services') }}</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-car text-info"></i>
                                <span>{{ __('landing.auto_services') }}</span>
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

                    <h2 class="display-5 fw-bold mb-4">{{ __('landing.web_booking_title') }}</h2>
                    <p class="lead mb-4">{{ __('landing.web_booking_description') }}</p>
                    <div class="feature-benefits">
                        <div class="benefit-item mb-3">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>{{ __('landing.web_booking_benefits.instagram_bio_link') }}</span>
                        </div>
                        <div class="benefit-item mb-3">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>{{ __('landing.web_booking_benefits.booking_few_clicks') }}</span>
                    </div>
                        <div class="benefit-item mb-3">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>{{ __('landing.web_booking_benefits.real_time_selection') }}</span>
                </div>
                        <div class="benefit-item mb-3">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>{{ __('landing.web_booking_benefits.automatic_notifications') }}</span>
            </div>
                        </div>
                    </div>
                </div>
            <div class="col-lg-6">
                <div class="feature-demo">
                    <div class="booking-form-preview">
                        <div class="booking-form-container">
                            <div class="form-header bg-gradient-primary text-white p-3 rounded-top">
                                <h6 class="mb-0"><i class="fas fa-calendar-plus me-2"></i>{{ __('landing.online_booking') }}</h6>
            </div>
                            <div class="form-body p-3 bg-white rounded-bottom">
                                <!-- Шаг 1: Выбор услуги -->
                                <div class="form-step active" id="demo-step1">
                                    <h6 class="mb-3">{{ __('landing.select_service') }}</h6>
                                    <div class="service-options">
                                        <div class="service-option selected">
                                            <div class="service-info">
                                                <h5 class="mb-1">{{ __('landing.manicure') }}</h5>
                                                <p>від 1500 ₴ • 60 хв</p>
                        </div>
                    </div>
                                        <div class="service-option">
                                            <div class="service-info">
                                                <h5 class="mb-1">{{ __('landing.haircut') }}</h5>
                                                <p>від 2000 ₴ • 45 хв</p>
                </div>
            </div>
                                        <div class="service-option">
                                            <div class="service-info">
                                                <h5 class="mb-1">{{ __('landing.massage') }}</h5>
                                                <p>від 3000 ₴ • 90 хв</p>
                        </div>
                    </div>
                </div>
            </div>
            
                                <!-- Шаг 2: Выбор мастера -->
                                <div class="form-step" id="demo-step2">
                                    <h6 class="mb-3">{{ __('landing.select_master') }}</h6>
                                    <div class="master-options">
                                        <div class="master-option selected">
                                            <div class="master-avatar">
                                                <div class="avatar-placeholder">А</div>
                        </div>
                                            <div class="master-info">
                                                <h5 class="mb-1">{{ __('landing.anna') }}</h5>
                                                <p>{{ __('landing.manicure_master') }}</p>
                    </div>
                </div>
                                        <div class="master-option">
                                            <div class="master-avatar">
                                                <div class="avatar-placeholder">Е</div>
            </div>
                                            <div class="master-info">
                                                <h5 class="mb-1">{{ __('landing.elena') }}</h5>
                                                <p>{{ __('landing.hairdresser') }}</p>
                        </div>
                    </div>
                                        <div class="master-option">
                                            <div class="master-avatar">
                                                <div class="avatar-placeholder">К</div>
                </div>
                                            <div class="master-info">
                                                <h5 class="mb-1">{{ __('landing.katerina') }}</h5>
                                                <p>{{ __('landing.masseur') }}</p>
            </div>
        </div>
    </div>
                                    </div>
                                    
                                <!-- Шаг 3: Выбор даты и времени -->
                                <div class="form-step" id="demo-step3">
                                    <h6 class="mb-3">{{ __('landing.select_date_time') }}</h6>
                                    <div class="calendar-demo">
                                        <div class="calendar-header">
                                            <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-chevron-left"></i></button>
                                            <h5 class="mb-0">{{ __('landing.january_2025') }}</h5>
                                            <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-chevron-right"></i></button>
                                        </div>
                                        
                                        <!-- Дни недели -->
                                        <div class="calendar-weekdays">
                                            <div class="weekday">{{ __('landing.monday') }}</div>
                                            <div class="weekday">{{ __('landing.tuesday') }}</div>
                                            <div class="weekday">{{ __('landing.wednesday') }}</div>
                                            <div class="weekday">{{ __('landing.thursday') }}</div>
                                            <div class="weekday">{{ __('landing.friday') }}</div>
                                            <div class="weekday">{{ __('landing.saturday') }}</div>
                                            <div class="weekday">{{ __('landing.sunday') }}</div>
                                        </div>
                                        
                                        <div class="calendar-grid-demo">
                                            <!-- Предыдущий месяц -->
                                            <div class="calendar-day-demo other-month" title="{{ __('landing.other_month') }}">30</div>
                                            <div class="calendar-day-demo other-month" title="{{ __('landing.other_month') }}">31</div>
                                            
                                            <!-- Текущий месяц -->
                                            <div class="calendar-day-demo disabled" title="{{ __('landing.disabled') }}">1</div>
                                            <div class="calendar-day-demo disabled" title="{{ __('landing.disabled') }}">2</div>
                                            <div class="calendar-day-demo">3</div>
                                            <div class="calendar-day-demo">4</div>
                                            <div class="calendar-day-demo today" title="{{ __('landing.today') }}">5</div>
                                            <div class="calendar-day-demo selected" title="{{ __('landing.selected') }}">6</div>
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
                                        <h6 class="mb-2">{{ __('landing.available_time') }}</h6>
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
                                    <h6 class="mb-3">{{ __('landing.client_data') }}</h6>
                                    <div class="form-fields">
                                        <div class="form-group mb-2">
                                            <input type="text" class="form-control form-control-sm" placeholder="{{ __('landing.your_name') }}" value="Анна Петрова">
                                            </div>
                                        <div class="form-group mb-2">
                                            <input type="tel" class="form-control form-control-sm" placeholder="{{ __('landing.phone') }}" value="+380 99 123 45 67">
                                        </div>
                                        <div class="form-group mb-3">
                                            <textarea class="form-control form-control-sm" rows="2" placeholder="{{ __('landing.comment_optional') }}"></textarea>
                                        </div>
                                    </div>
                                    <div class="form-actions">
                                        <button class="btn btn-success btn-sm w-100">{{ __('landing.book_appointment') }} <i class="fas fa-check ms-1"></i></button>
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
                    <h2 class="display-5 fw-bold mb-4">{{ __('landing.telegram_notifications_title') }}</h2>
                    <p class="lead mb-4">{{ __('landing.telegram_notifications_description') }}</p>
                    <div class="feature-benefits">
                        <div class="benefit-item mb-3">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>{{ __('landing.instant_notifications') }}</span>
                                                </div>
                        <div class="benefit-item mb-3">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>{{ __('landing.notifications_cancellations_reschedules') }}</span>
                                            </div>
                        <div class="benefit-item mb-3">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>{{ __('landing.reminders_tomorrow_appointments') }}</span>
                                                </div>
                        <div class="benefit-item mb-3">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>{{ __('landing.statistics_reports_chat') }}</span>
                                            </div>
                                            </div>
                                        </div>
                                                </div>
            <div class="col-lg-6 order-lg-1">
                <div class="feature-demo">
                    <div class="telegram-preview">
                        <div class="telegram-chat">
                            <div class="chat-header bg-primary text-white p-2 rounded-top">
                                <i class="fab fa-telegram me-2"></i>{{ __('landing.trimora_bot') }}
                                                </div>
                            <div class="chat-messages p-3 bg-white rounded-bottom">
                                <div class="message bot-message mb-2">
                                    <div class="message-content bg-light p-2 rounded">
                                        <small class="text-muted">{{ __('landing.new_appointment') }}</small>
                                        <div>{{ __('landing.client') }}: {{ __('landing.anna_petrova') }}</div>
                                        <div>{{ __('landing.service') }}: {{ __('landing.manicure') }}</div>
                                        <div>{{ __('landing.date') }}: {{ __('landing.january_15_2025') }}, {{ __('landing.time') }}: {{ __('landing.14_00') }}</div>
                                            </div>
                                            </div>
                                <div class="message bot-message mb-2">
                                    <div class="message-content bg-light p-2 rounded">
                                        <small class="text-muted">{{ __('landing.reminder') }}</small>
                                        <div>{{ __('landing.tomorrow_at_10_00') }}: {{ __('landing.massage') }}</div>
                                        <div>{{ __('landing.client') }}: {{ __('landing.maria_kozlova') }}</div>
                                            </div>
                                        </div>
                                <div class="message bot-message">
                                    <div class="message-content bg-light p-2 rounded">
                                        <small class="text-muted">{{ __('landing.statistics_today') }}</small>
                                        <div>{{ __('landing.appointments') }}: 8</div>
                                        <div>{{ __('landing.products_sold') }}: 5</div>
                                        <div>{{ __('landing.revenue') }}: ₴{{ __('landing.12_500') }}</div>
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
                    <h2 class="display-5 fw-bold mb-4">{{ __('landing.email_marketing_title') }}</h2>
                    <p class="lead mb-4">{{ __('landing.email_marketing_description') }}</p>
                    <div class="feature-benefits">
                        <div class="benefit-item mb-3">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>{{ __('landing.automatic_reminders') }}</span>
                                            </div>
                        <div class="benefit-item mb-3">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>{{ __('landing.appointment_confirmation') }}</span>
                                            </div>
                        <div class="benefit-item mb-3">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>{{ __('landing.reminders_tomorrow_appointments') }}</span>
                                            </div>
                        <div class="benefit-item mb-3">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>{{ __('landing.notifications_cancellations_reschedules') }}</span>
                                        </div>
                                        </div>
                                                </div>
                                            </div>
            <div class="col-lg-6">
                <div class="feature-demo">
                    <div class="email-preview">
                        <div class="email-template">
                            <div class="email-header bg-gradient-primary text-white p-3 rounded-top">
                                <h6 class="mb-0"><i class="fas fa-envelope me-2"></i>{{ __('landing.appointment_reminder') }}</h6>
                                                </div>
                            <div class="email-body p-3 bg-white rounded-bottom">
                                <div class="email-content">
                                    <h6>{{ __('landing.hello_anna') }}</h6>
                                    <p>{{ __('landing.appointment_reminder_description') }}</p>
                                    <div class="appointment-details bg-light p-3 rounded mb-3">
                                        <div><strong>{{ __('landing.service') }}:</strong> {{ __('landing.manicure') }}</div>
                                        <div><strong>{{ __('landing.date') }}:</strong> {{ __('landing.january_15_2025') }}</div>
                                        <div><strong>{{ __('landing.time') }}:</strong> {{ __('landing.14_00') }}</div>
                                        <div><strong>{{ __('landing.master') }}:</strong> {{ __('landing.elena') }}</div>
                                            </div>
                                    <p>{{ __('landing.we_await_you') }}</p>
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
                    <h2 class="display-5 fw-bold mb-4">{{ __('landing.website_widget_title') }}</h2>
                    <p class="lead mb-4">{{ __('landing.website_widget_description') }}</p>
                    <div class="feature-benefits">
                        <div class="benefit-item mb-3">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>{{ __('landing.simple_one_line_code_integration') }}</span>
                                            </div>
                        <div class="benefit-item mb-3">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>{{ __('landing.responsive_design_for_all_devices') }}</span>
                                            </div>
                        <div class="benefit-item mb-3">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>{{ __('landing.website_widget_settings') }}</span>
                                            </div>
                        <div class="benefit-item mb-3">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>{{ __('landing.quick_widget_installation') }}</span>
                                            </div>
                                            </div>
                                            </div>
                                        </div>
            <div class="col-lg-6 order-lg-1">
                <div class="feature-demo">
                    <div class="code-preview">
                        <div class="code-header bg-dark text-white p-2 rounded-top">
                            <small><i class="fas fa-code me-2"></i>{{ __('landing.code_for_insertion') }}</small>
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
                    <h2 class="display-5 fw-bold mb-4">{{ __('landing.roles_system_title') }}</h2>
                    <p class="lead mb-4">{{ __('landing.roles_system_description') }}</p>
                    <div class="feature-benefits">
                        <div class="benefit-item mb-3">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>{{ __('landing.roles') }}: {{ __('landing.administrator') }}, {{ __('landing.manager') }}, {{ __('landing.master') }}</span>
                                            </div>
                        <div class="benefit-item mb-3">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>{{ __('landing.flexible_access_rights') }}</span>
                                        </div>
                        <div class="benefit-item mb-3">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>{{ __('landing.data_security_and_confidentiality') }}</span>
                                    </div>
                        <div class="benefit-item mb-3">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>{{ __('landing.audit_of_user_actions') }}</span>
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
                                        <h6 class="mb-0"><i class="fas fa-crown me-2"></i>{{ __('landing.administrator') }}</h6>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-unstyled mb-0">
                                            <li><i class="fas fa-check text-success me-2"></i>{{ __('landing.full_access_to_all_functions') }}</li>
                                            <li><i class="fas fa-check text-success me-2"></i>{{ __('landing.user_management') }}</li>
                                            <li><i class="fas fa-check text-success me-2"></i>{{ __('landing.financial_reporting') }}</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="role-card mb-3">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0"><i class="fas fa-user-tie me-2"></i>{{ __('landing.manager') }}</h6>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-unstyled mb-0">
                                            <li><i class="fas fa-check text-success me-2"></i>{{ __('landing.appointment_management') }}</li>
                                            <li><i class="fas fa-check text-success me-2"></i>{{ __('landing.work_with_clients') }}</li>
                                            <li><i class="fas fa-check text-success me-2"></i>{{ __('landing.basic_analytics') }}</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="role-card">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-success text-white">
                                        <h6 class="mb-0"><i class="fas fa-user me-2"></i>{{ __('landing.master') }}</h6>
                        </div>
                                    <div class="card-body">
                                        <ul class="list-unstyled mb-0">
                                            <li><i class="fas fa-check text-success me-2"></i>{{ __('landing.view_your_appointments') }}</li>
                                            <li><i class="fas fa-check text-success me-2"></i>{{ __('landing.work_schedule') }}</li>
                                            <li><i class="fas fa-check text-success me-2"></i>{{ __('landing.personal_statistics') }}</li>
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
                    <h2 class="display-5 fw-bold mb-4">{{ __('landing.detailed_analytics_title') }}</h2>
                    <p class="lead mb-4">{{ __('landing.detailed_analytics_description') }}</p>
                    <div class="feature-benefits">
                        <div class="benefit-item mb-3">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>{{ __('landing.analytics_clients_appointments') }}</span>
                        </div>
                        <div class="benefit-item mb-3">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>{{ __('landing.income_reports_and_profit') }}</span>
                        </div>
                        <div class="benefit-item mb-3">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>{{ __('landing.popularity_services_masters') }}</span>
                        </div>
                        <div class="benefit-item mb-3">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>{{ __('landing.interactive_charts_and_diagrams') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 order-lg-1">
                <div class="feature-demo">
                    <div class="analytics-preview">
                        <div class="analytics-dashboard">
                            <div class="dashboard-header bg-gradient-primary text-white p-3 rounded-top">
                                <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>{{ __('landing.salon_analytics') }}</h6>
                            </div>
                            <div class="dashboard-content p-3 bg-white rounded-bottom">
                                <div class="row g-3 mb-4">
                                    <div class="col-6">
                                        <div class="stat-mini-large bg-success-light p-3 rounded">
                                            <small class="text-muted d-block mb-1">{{ __('landing.profit') }}</small>
                                            <div class="fw-bold fs-5">₴{{ __('landing.125_400') }}</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="stat-mini-large bg-info-light p-3 rounded">
                                            <small class="text-muted d-block mb-1">{{ __('landing.clients') }}</small>
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
                                        <small class="text-muted d-block text-center mt-2">{{ __('landing.sales_dynamics_week') }}</small>
                                    </div>
                                </div>
                                
                                <!-- График 2: Горизонтальные столбики -->
                                <div class="chart-step" id="chart-step2">
                                    <div class="chart-preview bg-light p-3 rounded">
                                        <div class="horizontal-bars-large">
                                            <div class="horizontal-bar-large">
                                                <span class="bar-label-large">{{ __('landing.manicure') }}</span>
                                                <div class="bar-container-large">
                                                    <div class="bar-fill-large" style="width: 85%;"></div>
                            </div>
                                                <span class="bar-value-large">85%</span>
                                            </div>
                                            <div class="horizontal-bar-large">
                                                <span class="bar-label-large">{{ __('landing.haircut') }}</span>
                                                <div class="bar-container-large">
                                                    <div class="bar-fill-large" style="width: 65%;"></div>
                                                </div>
                                                <span class="bar-value-large">65%</span>
                                            </div>
                                            <div class="horizontal-bar-large">
                                                <span class="bar-label-large">{{ __('landing.massage') }}</span>
                                                <div class="bar-container-large">
                                                    <div class="bar-fill-large" style="width: 45%;"></div>
                                                </div>
                                                <span class="bar-value-large">45%</span>
                                            </div>
                                        </div>
                                        <small class="text-muted d-block text-center mt-2">{{ __('landing.service_popularity') }}</small>
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
                                                    <div class="pie-label-large">{{ __('landing.profit') }}</div>
                        </div>
                    </div>
                                            <div class="pie-legend-large">
                                                <div class="legend-item-large">
                                                    <span class="legend-color-large" style="background: #667eea;"></span>
                                                    <span class="legend-text-large">{{ __('landing.services') }} (33%)</span>
                        </div>
                                                <div class="legend-item-large">
                                                    <span class="legend-color-large" style="background: #e5e7eb;"></span>
                                                    <span class="legend-text-large">{{ __('landing.products') }} (67%)</span>
                    </div>
                                            </div>
                                        </div>
                                        <small class="text-muted d-block text-center mt-2">{{ __('landing.income_structure') }}</small>
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
                                                <text x="20" y="115" font-size="10" fill="#6b7280" text-anchor="middle">{{ __('landing.january') }}</text>
                                                <text x="60" y="115" font-size="10" fill="#6b7280" text-anchor="middle">{{ __('landing.february') }}</text>
                                                <text x="100" y="115" font-size="10" fill="#6b7280" text-anchor="middle">{{ __('landing.march') }}</text>
                                                <text x="140" y="115" font-size="10" fill="#6b7280" text-anchor="middle">{{ __('landing.april') }}</text>
                                                <text x="180" y="115" font-size="10" fill="#6b7280" text-anchor="middle">{{ __('landing.may') }}</text>
                                                <text x="220" y="115" font-size="10" fill="#6b7280" text-anchor="middle">{{ __('landing.june') }}</text>
                                                <text x="260" y="115" font-size="10" fill="#6b7280" text-anchor="middle">{{ __('landing.july') }}</text>
                                                <text x="280" y="115" font-size="10" fill="#6b7280" text-anchor="middle">{{ __('landing.august') }}</text>
                                                
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
                                        <small class="text-muted d-block text-center mt-2">{{ __('landing.client_base_growth') }}</small>
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
                <h2 class="display-5 fw-bold mb-4 section-title">{{ __('landing.testimonials_title') }}</h2>
                <p class="lead text-muted">{{ __('landing.testimonials_description') }}</p>
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
                                <h5 class="mb-1">{{ __('landing.anna_petrova') }}</h5>
                                <small class="text-muted">{{ __('landing.salon_beauty') }}</small>
                            </div>
                        </div>
                        <div class="mb-3">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                        <p class="card-text">"{{ __('landing.web_booking_testimonial_1') }}"</p>
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
                                <h5 class="mb-1">{{ __('landing.mikhail_sidorov') }}</h5>
                                <small class="text-muted">{{ __('landing.elegant_studio') }}</small>
                            </div>
                        </div>
                        <div class="mb-3">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                        <p class="card-text">"{{ __('landing.roles_system_testimonial_1') }}"</p>
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
                                <h5 class="mb-1">{{ __('landing.elena_kozlova') }}</h5>
                                <small class="text-muted">{{ __('landing.gracia_salon') }}</small>
                            </div>
                        </div>
                        <div class="mb-3">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                        <p class="card-text">"{{ __('landing.analytics_testimonial_1') }}"</p>
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
                <h2 class="display-5 fw-bold mb-4">{{ __('landing.try_trimora_free_cta_title') }}</h2>
                <p class="lead mb-4">{{ __('landing.try_trimora_free_cta_description') }}</p>
                <div class="d-flex gap-3 justify-content-center">
                    @if(Auth::guard('client')->check())
                        <a href="{{ route('dashboard') }}" class="btn btn-light btn-lg animate-pulse">
                            <i class="fas fa-sign-in-alt me-2"></i>{{ __('landing.enter_system') }}
                        </a>
                    @else
                        <a href="#" class="btn btn-light btn-lg animate-pulse" data-bs-toggle="modal" data-bs-target="#registerModal">
                            <i class="fas fa-rocket me-2"></i>{{ __('landing.try_free_7_days') }}
                        </a>
                    @endif
                    <a href="#features-grid" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-list me-2"></i>{{ __('landing.view_functions') }}
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
            <h2 class="display-5 fw-bold mb-4">{{ __('landing.full_crm_functional_title') }}</h2>
            <p class="lead">{{ __('landing.full_crm_functional_description') }}</p>
        </div>
        
        <div class="row g-4">
            <!-- Онлайн-запись -->
            <div class="col-lg-3 col-md-6">
                <div class="feature-grid-card">
                    <div class="feature-grid-icon">
                        <i class="fas fa-calendar-plus"></i>
                    </div>
                    <h5>{{ __('landing.online_booking') }}</h5>
                    <p>{{ __('landing.online_booking_description') }}</p>
                </div>
            </div>
            
            <!-- Электронный журнал -->
            <div class="col-lg-3 col-md-6">
                <div class="feature-grid-card">
                    <div class="feature-grid-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h5>{{ __('landing.electronic_journal') }}</h5>
                    <p>{{ __('landing.electronic_journal_description') }}</p>
                </div>
            </div>
            
            <!-- Клиентская база -->
            <div class="col-lg-3 col-md-6">
                <div class="feature-grid-card">
                    <div class="feature-grid-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h5>{{ __('landing.client_database') }}</h5>
                    <p>{{ __('landing.client_database_description') }}</p>
                </div>
            </div>
            
            <!-- Уведомления -->
            <div class="col-lg-3 col-md-6">
                <div class="feature-grid-card">
                    <div class="feature-grid-icon">
                        <i class="fas fa-bell"></i>
                    </div>
                    <h5>{{ __('landing.notifications') }}</h5>
                    <p>{{ __('landing.notifications_description') }}</p>
                </div>
            </div>
            
            <!-- Управление товарами -->
            <div class="col-lg-3 col-md-6">
                <div class="feature-grid-card">
                    <div class="feature-grid-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <h5>{{ __('landing.product_management') }}</h5>
                    <p>{{ __('landing.product_management_description') }}</p>
                </div>
            </div>
            
            <!-- Инвентаризация -->
            <div class="col-lg-3 col-md-6">
                <div class="feature-grid-card">
                    <div class="feature-grid-icon">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <h5>{{ __('landing.inventory') }}</h5>
                    <p>{{ __('landing.inventory_description') }}</p>
                </div>
            </div>
            
            <!-- Финансовая отчетность -->
            <div class="col-lg-3 col-md-6">
                <div class="feature-grid-card">
                    <div class="feature-grid-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h5>{{ __('landing.financial_reporting') }}</h5>
                    <p>{{ __('landing.financial_reporting_description') }}</p>
                </div>
            </div>
            
            <!-- Аналитика и статистика -->
            <div class="col-lg-3 col-md-6">
                <div class="feature-grid-card">
                    <div class="feature-grid-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <h5>{{ __('landing.analytics_statistics') }}</h5>
                    <p>{{ __('landing.analytics_statistics_description') }}</p>
                </div>
            </div>
            
            <!-- Программы Лояльности -->
            <div class="col-lg-3 col-md-6">
                <div class="feature-grid-card">
                    <div class="feature-grid-icon">
                        <i class="fas fa-gift"></i>
                    </div>
                    <h5>{{ __('landing.loyalty_programs') }}</h5>
                    <p>{{ __('landing.loyalty_programs_description') }}</p>
                </div>
            </div>
            
            <!-- Закупки и продажи -->
            <div class="col-lg-3 col-md-6">
                <div class="feature-grid-card">
                    <div class="feature-grid-icon">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                                            <h5>{{ __('landing.purchases_sales') }}</h5>
                        <p>{{ __('landing.purchases_sales_description') }}</p>
                </div>
            </div>
            
            <!-- Система ролей -->
            <div class="col-lg-3 col-md-6">
                <div class="feature-grid-card">
                    <div class="feature-grid-icon">
                        <i class="fas fa-user-shield"></i>
                    </div>
                                            <h5>{{ __('landing.roles_system') }}</h5>
                        <p>{{ __('landing.roles_system_description') }}</p>
                </div>
            </div>
            
            <!-- Резервные копии -->
            <div class="col-lg-3 col-md-6">
                <div class="feature-grid-card">
                    <div class="feature-grid-icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <h5>{{ __('landing.salary') }}</h5>
                    <p>{{ __('landing.salary_description') }}</p>
                </div>
            </div>
            
            <!-- Управление записями -->
            <div class="col-lg-3 col-md-6">
                <div class="feature-grid-card">
                    <div class="feature-grid-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h5>{{ __('landing.appointment_management') }}</h5>
                    <p>{{ __('landing.appointment_management_description') }}</p>
                </div>
            </div>
            
            <!-- Аналитика по клиентам -->
            <div class="col-lg-3 col-md-6">
                <div class="feature-grid-card">
                    <div class="feature-grid-icon">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <h5>{{ __('landing.client_analytics') }}</h5>
                    <p>{{ __('landing.client_analytics_description') }}</p>
                </div>
            </div>
            
            <!-- Аналитика товарооборота -->
            <div class="col-lg-3 col-md-6">
                <div class="feature-grid-card">
                    <div class="feature-grid-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h5>{{ __('landing.product_sales_analytics') }}</h5>
                    <p>{{ __('landing.product_sales_analytics_description') }}</p>
                </div>
            </div>
            
            <!-- Виджет и Ссылки -->
            <div class="col-lg-3 col-md-6">
                <div class="feature-grid-card">
                    <div class="feature-grid-icon">
                        <i class="fas fa-link"></i>
                    </div>
                    <h5>{{ __('landing.widget_links') }}</h5>
                    <p>{{ __('landing.widget_links_description') }}</p>
                </div>
            </div>
        </div>
        
        <!-- CTA после функций -->
        <div class="text-center mt-5">
            <h3 class="fw-bold mb-3">{{ __('landing.try_now_cta_title') }}</h3>
                            <p class="lead text-muted mb-4">{{ __('landing.try_7_days_test_description') }}</p>
            @if(Auth::guard('client')->check())
                <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg animate-pulse">
                    <i class="fas fa-sign-in-alt me-2"></i>{{ __('landing.enter_system') }}
                </a>
            @else
                <a href="#" class="btn btn-primary btn-lg animate-pulse" data-bs-toggle="modal" data-bs-target="#registerModal">
                    <i class="fas fa-rocket me-2"></i>{{ __('landing.try_free_7_days') }}
                </a>
            @endif
        </div>
    </div>
</section>

<!-- Секция "Для каких ниш подходит" -->
<section id="niches-section" class="niches-section py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold mb-4">{{ __('landing.niches_title') }}</h2>
            <p class="lead">{{ __('landing.niches_description') }}</p>
        </div>
        
        <div class="row g-4">
            <!-- Красота и уход -->
            <div class="col-lg-3 col-md-6">
                <div class="niche-card">
                    <div class="niche-icon">
                        <i class="fas fa-cut text-primary"></i>
                    </div>
                    <h4 class="niche-title">{{ __('landing.beauty_care') }}</h4>
                    <p class="niche-description">{{ __('landing.beauty_care_description') }}</p>
                </div>
            </div>
            
            <!-- Медицинские услуги -->
            <div class="col-lg-3 col-md-6">
                <div class="niche-card">
                    <div class="niche-icon">
                        <i class="fas fa-heartbeat text-danger"></i>
                    </div>
                    <h4 class="niche-title">{{ __('landing.medical_services') }}</h4>
                    <p class="niche-description">{{ __('landing.medical_services_description') }}</p>
                </div>
            </div>
            
            <!-- Творческие услуги -->
            <div class="col-lg-3 col-md-6">
                <div class="niche-card">
                    <div class="niche-icon">
                        <i class="fas fa-palette text-warning"></i>
                    </div>
                    <h4 class="niche-title">{{ __('landing.creative_services') }}</h4>
                    <p class="niche-description">{{ __('landing.creative_services_description') }}</p>
                </div>
            </div>
            
            <!-- Автосервисы -->
            <div class="col-lg-3 col-md-6">
                <div class="niche-card">
                    <div class="niche-icon">
                        <i class="fas fa-car text-info"></i>
                    </div>
                    <h4 class="niche-title">{{ __('landing.auto_services') }}</h4>
                    <p class="niche-description">{{ __('landing.auto_services_description') }}</p>
                </div>
            </div>
            
            <!-- Бытовые услуги -->
            <div class="col-lg-3 col-md-6">
                <div class="niche-card">
                    <div class="niche-icon">
                        <i class="fas fa-tools text-secondary"></i>
                    </div>
                    <h4 class="niche-title">{{ __('landing.household_services') }}</h4>
                    <p class="niche-description">{{ __('landing.household_services_description') }}</p>
                </div>
            </div>
            
            <!-- Образование -->
            <div class="col-lg-3 col-md-6">
                <div class="niche-card">
                    <div class="niche-icon">
                        <i class="fas fa-graduation-cap text-success"></i>
                    </div>
                    <h4 class="niche-title">{{ __('landing.education') }}</h4>
                    <p class="niche-description">{{ __('landing.education_description') }}</p>
                </div>
            </div>
            
            <!-- Ресторанный бизнес -->
            <div class="col-lg-3 col-md-6">
                <div class="niche-card">
                    <div class="niche-icon">
                        <i class="fas fa-utensils text-warning"></i>
                    </div>
                    <h4 class="niche-title">{{ __('landing.restaurant_business') }}</h4>
                    <p class="niche-description">{{ __('landing.restaurant_business_description') }}</p>
                </div>
            </div>
            
            <!-- Консалтинговые услуги -->
            <div class="col-lg-3 col-md-6">
                <div class="niche-card">
                    <div class="niche-icon">
                        <i class="fas fa-briefcase text-dark"></i>
                    </div>
                    <h4 class="niche-title">{{ __('landing.consulting_services') }}</h4>
                    <p class="niche-description">{{ __('landing.consulting_services_description') }}</p>
                </div>
            </div>
        </div>
        
                        <div class="text-center mt-5">
                    <p class="text-muted">{{ __('landing.niches_not_found_description') }}</p>
                </div>
    </div>
</section>



@include('landing.components.register-modal')
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const slides = document.querySelectorAll('.slide');
    const heroButtons = document.querySelectorAll('.hero-buttons .btn');
    
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
