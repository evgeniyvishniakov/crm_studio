@extends('landing.layouts.app')

@section('title', 'Тарифы - CRM Studio')
@section('description', 'Выберите подходящий тариф для вашего салона красоты. Начните с бесплатного пробного периода на 7 дней.')

@section('content')
<!-- Hero Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold mb-4 text-dark">Тарифы</h1>
                <p class="lead mb-4 text-muted">Выберите план в зависимости от количества сотрудников в вашем салоне</p>
                <p class="text-muted mb-4">Все тарифы включают полный набор функций CRM Studio</p>
                <a href="#" class="btn btn-primary btn-lg animate-pulse" data-bs-toggle="modal" data-bs-target="#registerModal">
                    <i class="fas fa-rocket me-2"></i>Попробовать бесплатно 7 дней
                </a>
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
                        <i class="fas fa-chart-line text-warning fa-lg"></i>
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
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="small-tab" data-bs-toggle="pill" data-bs-target="#small" type="button" role="tab">
                            До 2 сотрудников
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="medium-tab" data-bs-toggle="pill" data-bs-target="#medium" type="button" role="tab">
                            До 5 сотрудников
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="large-tab" data-bs-toggle="pill" data-bs-target="#large" type="button" role="tab">
                            Без лимита
                        </button>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Tabs Content -->
        <div class="tab-content" id="pricingTabsContent">
            <!-- Small Business Tab -->
            <div class="tab-pane fade show active" id="small" role="tabpanel">
                <div class="row justify-content-center">
                    <!-- Месяц -->
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card h-100 border-0 shadow-sm border-success border-2">
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-success" style="font-size: 0.7rem;">Базовый</span>
                            </div>
                            <div class="card-body p-4" style="padding-top: 3rem !important;">
                                <div class="text-center mb-4">
                                    <h3 class="h5 fw-bold text-dark mb-3">Месяц</h3>
                                    <div class="mb-3">
                                        <span class="h2 fw-bold text-dark">490₴</span>
                                        <span class="text-muted">/ месяц</span>
                                    </div>
                                </div>
                                
                                <div class="text-center mb-4">
                                    <div class="bg-success bg-opacity-10 rounded p-3 mb-3">
                                        <i class="fas fa-users text-success fa-2x mb-2"></i>
                                        <h6 class="fw-bold text-success mb-0">До 2 сотрудников</h6>
                                    </div>
                                    <p class="text-muted small">Идеально для небольших салонов</p>
                                </div>
                                
                                <div class="text-center mb-4">
                                    <p class="text-success fw-bold mb-2"><i class="fas fa-check-circle me-2"></i>Все функции включены</p>
                                    <p class="text-muted small">Полный доступ ко всем возможностям CRM Studio</p>
                                </div>
                                
                                <a href="#" class="btn btn-success btn-lg w-100" data-bs-toggle="modal" data-bs-target="#registerModal">
                                    Выбрать план
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 3 месяца -->
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card h-100 position-relative border-success border-3 shadow-lg" style="transform: scale(1.05); z-index: 10;">
                            <div class="position-absolute top-0 start-0 m-3">
                                <span class="badge bg-success px-2 py-1" style="font-size: 0.7rem; font-weight: 700;">
                                    <i class="fas fa-star me-1"></i>ТОП
                                </span>
                            </div>
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-success" style="font-size: 0.7rem;">Экономия 10%</span>
                            </div>
                            <div class="card-body p-4" style="padding-top: 3.5rem !important;">
                                <div class="text-center mb-4">
                                    <h3 class="h5 fw-bold text-dark mb-3">3 месяца</h3>
                                    <div class="mb-3">
                                        <span class="h2 fw-bold text-dark">1320₴</span>
                                        <span class="text-muted">/ 3 месяца</span>
                                    </div>
                                    <p class="text-muted small">440₴ / месяц</p>
                                </div>
                                
                                <div class="text-center mb-4">
                                    <div class="bg-success bg-opacity-10 rounded p-3 mb-3">
                                        <i class="fas fa-users text-success fa-2x mb-2"></i>
                                        <h6 class="fw-bold text-success mb-0">До 2 сотрудников</h6>
                                    </div>
                                    <p class="text-muted small">Идеально для небольших салонов</p>
                                </div>
                                
                                <div class="text-center mb-4">
                                    <p class="text-success fw-bold mb-2"><i class="fas fa-check-circle me-2"></i>Все функции включены</p>
                                    <p class="text-muted small">Полный доступ ко всем возможностям CRM Studio</p>
                                </div>
                                
                                <a href="#" class="btn btn-success btn-lg w-100" data-bs-toggle="modal" data-bs-target="#registerModal">
                                    Выбрать план
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 6 месяцев -->
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card h-100 border-0 shadow-sm border-success border-2">
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-success" style="font-size: 0.7rem;">Экономия 15%</span>
                            </div>
                            <div class="card-body p-4" style="padding-top: 3rem !important;">
                                <div class="text-center mb-4">
                                    <h3 class="h5 fw-bold text-dark mb-3">6 месяцев</h3>
                                    <div class="mb-3">
                                        <span class="h2 fw-bold text-dark">2500₴</span>
                                        <span class="text-muted">/ 6 месяцев</span>
                                    </div>
                                    <p class="text-muted small">417₴ / месяц</p>
                                </div>
                                
                                <div class="text-center mb-4">
                                    <div class="bg-success bg-opacity-10 rounded p-3 mb-3">
                                        <i class="fas fa-users text-success fa-2x mb-2"></i>
                                        <h6 class="fw-bold text-success mb-0">До 2 сотрудников</h6>
                                    </div>
                                    <p class="text-muted small">Идеально для небольших салонов</p>
                                </div>
                                
                                <div class="text-center mb-4">
                                    <p class="text-success fw-bold mb-2"><i class="fas fa-check-circle me-2"></i>Все функции включены</p>
                                    <p class="text-muted small">Полный доступ ко всем возможностям CRM Studio</p>
                                </div>
                                
                                <a href="#" class="btn btn-success btn-lg w-100" data-bs-toggle="modal" data-bs-target="#registerModal">
                                    Выбрать план
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Год -->
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card h-100 border-0 shadow-sm border-success border-2">
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-success" style="font-size: 0.7rem;">Экономия 25%</span>
                            </div>
                            <div class="card-body p-4" style="padding-top: 3rem !important;">
                                <div class="text-center mb-4">
                                    <h3 class="h5 fw-bold text-dark mb-3">Год</h3>
                                    <div class="mb-3">
                                        <span class="h2 fw-bold text-dark">4400₴</span>
                                        <span class="text-muted">/ год</span>
                                    </div>
                                    <p class="text-muted small">367₴ / месяц</p>
                                </div>
                                
                                <div class="text-center mb-4">
                                    <div class="bg-success bg-opacity-10 rounded p-3 mb-3">
                                        <i class="fas fa-users text-success fa-2x mb-2"></i>
                                        <h6 class="fw-bold text-success mb-0">До 2 сотрудников</h6>
                                    </div>
                                    <p class="text-muted small">Идеально для небольших салонов</p>
                                </div>
                                
                                <div class="text-center mb-4">
                                    <p class="text-success fw-bold mb-2"><i class="fas fa-check-circle me-2"></i>Все функции включены</p>
                                    <p class="text-muted small">Полный доступ ко всем возможностям CRM Studio</p>
                                </div>
                                
                                <a href="#" class="btn btn-success btn-lg w-100" data-bs-toggle="modal" data-bs-target="#registerModal">
                                    Выбрать план
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Medium Business Tab -->
            <div class="tab-pane fade" id="medium" role="tabpanel">
                <div class="row justify-content-center">
                    <!-- Месяц -->
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card h-100 border-0 shadow-sm border-primary border-2">
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-primary" style="font-size: 0.7rem;">Базовый</span>
                            </div>
                            <div class="card-body p-4" style="padding-top: 3rem !important;">
                                <div class="text-center mb-4">
                                    <h3 class="h5 fw-bold text-dark mb-3">Месяц</h3>
                                    <div class="mb-3">
                                        <span class="h2 fw-bold text-dark">990₴</span>
                                        <span class="text-muted">/ месяц</span>
                                    </div>
                                </div>
                                
                                <div class="text-center mb-4">
                                    <div class="bg-primary bg-opacity-10 rounded p-3 mb-3">
                                        <i class="fas fa-users text-primary fa-2x mb-2"></i>
                                        <h6 class="fw-bold text-primary mb-0">До 5 сотрудников</h6>
                                    </div>
                                    <p class="text-muted small">Отлично для средних салонов</p>
                                </div>
                                
                                <div class="text-center mb-4">
                                    <p class="text-primary fw-bold mb-2"><i class="fas fa-check-circle me-2"></i>Все функции включены</p>
                                    <p class="text-muted small">Полный доступ ко всем возможностям CRM Studio</p>
                                </div>
                                
                                <a href="#" class="btn btn-primary btn-lg w-100" data-bs-toggle="modal" data-bs-target="#registerModal">
                                    Выбрать план
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 3 месяца -->
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card h-100 position-relative border-primary border-3 shadow-lg" style="transform: scale(1.05); z-index: 10;">
                            <div class="position-absolute top-0 start-0 m-3">
                                <span class="badge bg-primary px-2 py-1" style="font-size: 0.7rem; font-weight: 700;">
                                    <i class="fas fa-star me-1"></i>ТОП
                                </span>
                            </div>
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-primary" style="font-size: 0.7rem;">Экономия 10%</span>
                            </div>
                            <div class="card-body p-4" style="padding-top: 3.5rem !important;">
                                <div class="text-center mb-4">
                                    <h3 class="h5 fw-bold text-dark mb-3">3 месяца</h3>
                                    <div class="mb-3">
                                        <span class="h2 fw-bold text-dark">2670₴</span>
                                        <span class="text-muted">/ 3 месяца</span>
                                    </div>
                                    <p class="text-muted small">890₴ / месяц</p>
                                </div>
                                
                                <div class="text-center mb-4">
                                    <div class="bg-primary bg-opacity-10 rounded p-3 mb-3">
                                        <i class="fas fa-users text-primary fa-2x mb-2"></i>
                                        <h6 class="fw-bold text-primary mb-0">До 5 сотрудников</h6>
                                    </div>
                                    <p class="text-muted small">Отлично для средних салонов</p>
                                </div>
                                
                                <div class="text-center mb-4">
                                    <p class="text-primary fw-bold mb-2"><i class="fas fa-check-circle me-2"></i>Все функции включены</p>
                                    <p class="text-muted small">Полный доступ ко всем возможностям CRM Studio</p>
                                </div>
                                
                                <a href="#" class="btn btn-primary btn-lg w-100" data-bs-toggle="modal" data-bs-target="#registerModal">
                                    Выбрать план
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 6 месяцев -->
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card h-100 border-0 shadow-sm border-primary border-2">
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-primary" style="font-size: 0.7rem;">Экономия 15%</span>
                            </div>
                            <div class="card-body p-4" style="padding-top: 3rem !important;">
                                <div class="text-center mb-4">
                                    <h3 class="h5 fw-bold text-dark mb-3">6 месяцев</h3>
                                    <div class="mb-3">
                                        <span class="h2 fw-bold text-dark">5050₴</span>
                                        <span class="text-muted">/ 6 месяцев</span>
                                    </div>
                                    <p class="text-muted small">842₴ / месяц</p>
                                </div>
                                
                                <div class="text-center mb-4">
                                    <div class="bg-primary bg-opacity-10 rounded p-3 mb-3">
                                        <i class="fas fa-users text-primary fa-2x mb-2"></i>
                                        <h6 class="fw-bold text-primary mb-0">До 5 сотрудников</h6>
                                    </div>
                                    <p class="text-muted small">Отлично для средних салонов</p>
                                </div>
                                
                                <div class="text-center mb-4">
                                    <p class="text-primary fw-bold mb-2"><i class="fas fa-check-circle me-2"></i>Все функции включены</p>
                                    <p class="text-muted small">Полный доступ ко всем возможностям CRM Studio</p>
                                </div>
                                
                                <a href="#" class="btn btn-primary btn-lg w-100" data-bs-toggle="modal" data-bs-target="#registerModal">
                                    Выбрать план
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Год -->
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card h-100 border-0 shadow-sm border-primary border-2">
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-primary" style="font-size: 0.7rem;">Экономия 25%</span>
                            </div>
                            <div class="card-body p-4" style="padding-top: 3rem !important;">
                                <div class="text-center mb-4">
                                    <h3 class="h5 fw-bold text-dark mb-3">Год</h3>
                                    <div class="mb-3">
                                        <span class="h2 fw-bold text-dark">8900₴</span>
                                        <span class="text-muted">/ год</span>
                                    </div>
                                    <p class="text-muted small">742₴ / месяц</p>
                                </div>
                                
                                <div class="text-center mb-4">
                                    <div class="bg-primary bg-opacity-10 rounded p-3 mb-3">
                                        <i class="fas fa-users text-primary fa-2x mb-2"></i>
                                        <h6 class="fw-bold text-primary mb-0">До 5 сотрудников</h6>
                                    </div>
                                    <p class="text-muted small">Отлично для средних салонов</p>
                                </div>
                                
                                <div class="text-center mb-4">
                                    <p class="text-primary fw-bold mb-2"><i class="fas fa-check-circle me-2"></i>Все функции включены</p>
                                    <p class="text-muted small">Полный доступ ко всем возможностям CRM Studio</p>
                                </div>
                                
                                <a href="#" class="btn btn-primary btn-lg w-100" data-bs-toggle="modal" data-bs-target="#registerModal">
                                    Выбрать план
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Large Business Tab -->
            <div class="tab-pane fade" id="large" role="tabpanel">
                <div class="row justify-content-center">
                    <!-- Месяц -->
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card h-100 border-0 shadow-sm border-warning border-2">
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-warning text-dark" style="font-size: 0.7rem;">Базовый</span>
                            </div>
                            <div class="card-body p-4" style="padding-top: 3rem !important;">
                                <div class="text-center mb-4">
                                    <h3 class="h5 fw-bold text-dark mb-3">Месяц</h3>
                                    <div class="mb-3">
                                        <span class="h2 fw-bold text-dark">1990₴</span>
                                        <span class="text-muted">/ месяц</span>
                                    </div>
                                </div>
                                
                                <div class="text-center mb-4">
                                    <div class="bg-warning bg-opacity-10 rounded p-3 mb-3">
                                        <i class="fas fa-infinity text-warning fa-2x mb-2"></i>
                                        <h6 class="fw-bold text-warning mb-0">Без лимита сотрудников</h6>
                                    </div>
                                    <p class="text-muted small">Для крупных салонов и сетей</p>
                                </div>
                                
                                <div class="text-center mb-4">
                                    <p class="text-warning fw-bold mb-2"><i class="fas fa-check-circle me-2"></i>Все функции включены</p>
                                    <p class="text-muted small">Полный доступ ко всем возможностям CRM Studio</p>
                                </div>
                                
                                <a href="#" class="btn btn-warning btn-lg w-100" data-bs-toggle="modal" data-bs-target="#registerModal">
                                    Выбрать план
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 3 месяца -->
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card h-100 position-relative border-warning border-3 shadow-lg" style="transform: scale(1.05); z-index: 10;">
                            <div class="position-absolute top-0 start-0 m-3">
                                <span class="badge bg-warning text-dark px-2 py-1" style="font-size: 0.7rem; font-weight: 700;">
                                    <i class="fas fa-star me-1"></i>ТОП
                                </span>
                            </div>
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-warning text-dark" style="font-size: 0.7rem;">Экономия 10%</span>
                            </div>
                            <div class="card-body p-4" style="padding-top: 3.5rem !important;">
                                <div class="text-center mb-4">
                                    <h3 class="h5 fw-bold text-dark mb-3">3 месяца</h3>
                                    <div class="mb-3">
                                        <span class="h2 fw-bold text-dark">5370₴</span>
                                        <span class="text-muted">/ 3 месяца</span>
                                    </div>
                                    <p class="text-muted small">1790₴ / месяц</p>
                                </div>
                                
                                <div class="text-center mb-4">
                                    <div class="bg-warning bg-opacity-10 rounded p-3 mb-3">
                                        <i class="fas fa-infinity text-warning fa-2x mb-2"></i>
                                        <h6 class="fw-bold text-warning mb-0">Без лимита сотрудников</h6>
                                    </div>
                                    <p class="text-muted small">Для крупных салонов и сетей</p>
                                </div>
                                
                                <div class="text-center mb-4">
                                    <p class="text-warning fw-bold mb-2"><i class="fas fa-check-circle me-2"></i>Все функции включены</p>
                                    <p class="text-muted small">Полный доступ ко всем возможностям CRM Studio</p>
                                </div>
                                
                                <a href="#" class="btn btn-warning btn-lg w-100" data-bs-toggle="modal" data-bs-target="#registerModal">
                                    Выбрать план
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 6 месяцев -->
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card h-100 border-0 shadow-sm border-warning border-2">
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-warning text-dark" style="font-size: 0.7rem;">Экономия 15%</span>
                            </div>
                            <div class="card-body p-4" style="padding-top: 3rem !important;">
                                <div class="text-center mb-4">
                                    <h3 class="h5 fw-bold text-dark mb-3">6 месяцев</h3>
                                    <div class="mb-3">
                                        <span class="h2 fw-bold text-dark">10150₴</span>
                                        <span class="text-muted">/ 6 месяцев</span>
                                    </div>
                                    <p class="text-muted small">1692₴ / месяц</p>
                                </div>
                                
                                <div class="text-center mb-4">
                                    <div class="bg-warning bg-opacity-10 rounded p-3 mb-3">
                                        <i class="fas fa-infinity text-warning fa-2x mb-2"></i>
                                        <h6 class="fw-bold text-warning mb-0">Без лимита сотрудников</h6>
                                    </div>
                                    <p class="text-muted small">Для крупных салонов и сетей</p>
                                </div>
                                
                                <div class="text-center mb-4">
                                    <p class="text-warning fw-bold mb-2"><i class="fas fa-check-circle me-2"></i>Все функции включены</p>
                                    <p class="text-muted small">Полный доступ ко всем возможностям CRM Studio</p>
                                </div>
                                
                                <a href="#" class="btn btn-warning btn-lg w-100" data-bs-toggle="modal" data-bs-target="#registerModal">
                                    Выбрать план
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Год -->
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card h-100 border-0 shadow-sm border-warning border-2">
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-warning text-dark" style="font-size: 0.7rem;">Экономия 25%</span>
                            </div>
                            <div class="card-body p-4" style="padding-top: 3rem !important;">
                                <div class="text-center mb-4">
                                    <h3 class="h5 fw-bold text-dark mb-3">Год</h3>
                                    <div class="mb-3">
                                        <span class="h2 fw-bold text-dark">17900₴</span>
                                        <span class="text-muted">/ год</span>
                                    </div>
                                    <p class="text-muted small">1492₴ / месяц</p>
                                </div>
                                
                                <div class="text-center mb-4">
                                    <div class="bg-warning bg-opacity-10 rounded p-3 mb-3">
                                        <i class="fas fa-infinity text-warning fa-2x mb-2"></i>
                                        <h6 class="fw-bold text-warning mb-0">Без лимита сотрудников</h6>
                                    </div>
                                    <p class="text-muted small">Для крупных салонов и сетей</p>
                                </div>
                                
                                <div class="text-center mb-4">
                                    <p class="text-warning fw-bold mb-2"><i class="fas fa-check-circle me-2"></i>Все функции включены</p>
                                    <p class="text-muted small">Полный доступ ко всем возможностям CRM Studio</p>
                                </div>
                                
                                <a href="#" class="btn btn-warning btn-lg w-100" data-bs-toggle="modal" data-bs-target="#registerModal">
                                    Выбрать план
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-5">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-lg-8 mx-auto">
                <h2 class="h3 fw-bold mb-4 text-dark">Часто задаваемые вопросы</h2>
                <p class="text-muted">Ответы на популярные вопросы о тарифах</p>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item border-0 shadow-sm mb-3">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                Все ли функции доступны во всех тарифах?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Да, все функции CRM Studio доступны во всех тарифах. Разница только в количестве сотрудников, которые могут использовать систему одновременно.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item border-0 shadow-sm mb-3">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                Можно ли изменить тариф в процессе использования?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Да, вы можете изменить тариф в любое время. При переходе на более дорогой план доплата будет рассчитана пропорционально оставшимся дням текущего периода.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item border-0 shadow-sm mb-3">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                Что происходит после окончания пробного периода?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                После окончания 7-дневного пробного периода вам нужно будет выбрать один из платных тарифов для продолжения работы с системой.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item border-0 shadow-sm mb-3">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                Есть ли возможность отменить подписку?
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Да, вы можете отменить подписку в любое время. Доступ к системе сохранится до конца оплаченного периода.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item border-0 shadow-sm mb-3">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                                Какие способы оплаты принимаются?
                            </button>
                        </h2>
                        <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Мы принимаем оплату банковскими картами, электронными платежами и банковскими переводами. Все платежи защищены SSL-шифрованием.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5 bg-dark text-white">
    <div class="container">
        <div class="row text-center">
            <div class="col-lg-8 mx-auto">
                <h2 class="h3 fw-bold mb-4">Готовы начать?</h2>
                <p class="text-muted mb-4">Присоединяйтесь к тысячам салонов красоты, которые уже используют CRM Studio</p>
                <div class="d-flex gap-3 justify-content-center">
                    <a href="#" class="btn btn-light btn-lg animate-pulse" data-bs-toggle="modal" data-bs-target="#registerModal">
                        <i class="fas fa-rocket me-2"></i>Попробовать бесплатно 7 дней
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

@include('landing.components.register-modal')
@endsection

@push('scripts')
<script src="{{ asset('landing/main.js') }}"></script>
@endpush