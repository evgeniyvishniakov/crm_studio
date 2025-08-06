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
                <p class="lead mb-4 text-muted">Выберите план, который подходит именно вашему бизнесу</p>
            </div>
        </div>
    </div>
</section>

<!-- Pricing Section -->
<section id="pricing" class="py-5">
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
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body p-4">
                                <div class="text-center mb-4">
                                    <h3 class="h5 fw-bold text-dark mb-3">Месяц</h3>
                                    <div class="mb-3">
                                        <span class="h2 fw-bold text-dark">490₴</span>
                                        <span class="text-muted">/ месяц</span>
                                    </div>
                                    <p class="text-muted small">До 2 сотрудников</p>
                                </div>
                                
                                <ul class="list-unstyled mb-4">
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Управление клиентами</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Записи и календарь</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Базовая аналитика</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Email уведомления</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Поддержка</li>
                                </ul>
                                
                                <a href="#" class="btn btn-outline-dark btn-lg w-100" data-bs-toggle="modal" data-bs-target="#registerModal">
                                    Выбрать план
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 3 месяца -->
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card h-100 border-0 shadow-sm position-relative">
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-success">Экономия 10%</span>
                            </div>
                            <div class="card-body p-4">
                                <div class="text-center mb-4">
                                    <h3 class="h5 fw-bold text-dark mb-3">3 месяца</h3>
                                    <div class="mb-3">
                                        <span class="h2 fw-bold text-dark">1320₴</span>
                                        <span class="text-muted">/ 3 месяца</span>
                                    </div>
                                    <p class="text-muted small">440₴ / месяц</p>
                                </div>
                                
                                <ul class="list-unstyled mb-4">
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Управление клиентами</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Записи и календарь</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Базовая аналитика</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Email уведомления</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Поддержка</li>
                                </ul>
                                
                                <a href="#" class="btn btn-success btn-lg w-100" data-bs-toggle="modal" data-bs-target="#registerModal">
                                    Выбрать план
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 6 месяцев -->
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card h-100 border-0 shadow-sm position-relative">
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-warning">Экономия 15%</span>
                            </div>
                            <div class="card-body p-4">
                                <div class="text-center mb-4">
                                    <h3 class="h5 fw-bold text-dark mb-3">6 месяцев</h3>
                                    <div class="mb-3">
                                        <span class="h2 fw-bold text-dark">2500₴</span>
                                        <span class="text-muted">/ 6 месяцев</span>
                                    </div>
                                    <p class="text-muted small">417₴ / месяц</p>
                                </div>
                                
                                <ul class="list-unstyled mb-4">
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Управление клиентами</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Записи и календарь</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Базовая аналитика</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Email уведомления</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Поддержка</li>
                                </ul>
                                
                                <a href="#" class="btn btn-warning btn-lg w-100" data-bs-toggle="modal" data-bs-target="#registerModal">
                                    Выбрать план
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Год -->
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card h-100 border-0 shadow-sm position-relative">
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-danger">Экономия 25%</span>
                            </div>
                            <div class="card-body p-4">
                                <div class="text-center mb-4">
                                    <h3 class="h5 fw-bold text-dark mb-3">Год</h3>
                                    <div class="mb-3">
                                        <span class="h2 fw-bold text-dark">4400₴</span>
                                        <span class="text-muted">/ год</span>
                                    </div>
                                    <p class="text-muted small">367₴ / месяц</p>
                                </div>
                                
                                <ul class="list-unstyled mb-4">
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Управление клиентами</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Записи и календарь</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Базовая аналитика</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Email уведомления</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Поддержка</li>
                                </ul>
                                
                                <a href="#" class="btn btn-danger btn-lg w-100" data-bs-toggle="modal" data-bs-target="#registerModal">
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
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body p-4">
                                <div class="text-center mb-4">
                                    <h3 class="h5 fw-bold text-dark mb-3">Месяц</h3>
                                    <div class="mb-3">
                                        <span class="h2 fw-bold text-dark">990₴</span>
                                        <span class="text-muted">/ месяц</span>
                                    </div>
                                    <p class="text-muted small">До 5 сотрудников</p>
                                </div>
                                
                                <ul class="list-unstyled mb-4">
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Управление клиентами</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Записи и календарь</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Базовая аналитика</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Email уведомления</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Поддержка</li>
                                </ul>
                                
                                <a href="#" class="btn btn-outline-dark btn-lg w-100" data-bs-toggle="modal" data-bs-target="#registerModal">
                                    Выбрать план
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 3 месяца -->
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card h-100 border-0 shadow-sm position-relative">
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-success">Экономия 10%</span>
                            </div>
                            <div class="card-body p-4">
                                <div class="text-center mb-4">
                                    <h3 class="h5 fw-bold text-dark mb-3">3 месяца</h3>
                                    <div class="mb-3">
                                        <span class="h2 fw-bold text-dark">2670₴</span>
                                        <span class="text-muted">/ 3 месяца</span>
                                    </div>
                                    <p class="text-muted small">890₴ / месяц</p>
                                </div>
                                
                                <ul class="list-unstyled mb-4">
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Управление клиентами</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Записи и календарь</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Базовая аналитика</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Email уведомления</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Поддержка</li>
                                </ul>
                                
                                <a href="#" class="btn btn-success btn-lg w-100" data-bs-toggle="modal" data-bs-target="#registerModal">
                                    Выбрать план
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 6 месяцев -->
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card h-100 border-0 shadow-sm position-relative">
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-warning">Экономия 15%</span>
                            </div>
                            <div class="card-body p-4">
                                <div class="text-center mb-4">
                                    <h3 class="h5 fw-bold text-dark mb-3">6 месяцев</h3>
                                    <div class="mb-3">
                                        <span class="h2 fw-bold text-dark">5050₴</span>
                                        <span class="text-muted">/ 6 месяцев</span>
                                    </div>
                                    <p class="text-muted small">842₴ / месяц</p>
                                </div>
                                
                                <ul class="list-unstyled mb-4">
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Управление клиентами</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Записи и календарь</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Базовая аналитика</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Email уведомления</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Поддержка</li>
                                </ul>
                                
                                <a href="#" class="btn btn-warning btn-lg w-100" data-bs-toggle="modal" data-bs-target="#registerModal">
                                    Выбрать план
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Год -->
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card h-100 border-0 shadow-sm position-relative">
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-danger">Экономия 25%</span>
                            </div>
                            <div class="card-body p-4">
                                <div class="text-center mb-4">
                                    <h3 class="h5 fw-bold text-dark mb-3">Год</h3>
                                    <div class="mb-3">
                                        <span class="h2 fw-bold text-dark">8900₴</span>
                                        <span class="text-muted">/ год</span>
                                    </div>
                                    <p class="text-muted small">742₴ / месяц</p>
                                </div>
                                
                                <ul class="list-unstyled mb-4">
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Управление клиентами</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Записи и календарь</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Базовая аналитика</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Email уведомления</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Поддержка</li>
                                </ul>
                                
                                <a href="#" class="btn btn-danger btn-lg w-100" data-bs-toggle="modal" data-bs-target="#registerModal">
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
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body p-4">
                                <div class="text-center mb-4">
                                    <h3 class="h5 fw-bold text-dark mb-3">Месяц</h3>
                                    <div class="mb-3">
                                        <span class="h2 fw-bold text-dark">1990₴</span>
                                        <span class="text-muted">/ месяц</span>
                                    </div>
                                    <p class="text-muted small">Без лимита сотрудников</p>
                                </div>
                                
                                <ul class="list-unstyled mb-4">
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Управление клиентами</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Записи и календарь</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Базовая аналитика</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Email уведомления</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Поддержка</li>
                                </ul>
                                
                                <a href="#" class="btn btn-outline-dark btn-lg w-100" data-bs-toggle="modal" data-bs-target="#registerModal">
                                    Выбрать план
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 3 месяца -->
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card h-100 border-0 shadow-sm position-relative">
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-success">Экономия 10%</span>
                            </div>
                            <div class="card-body p-4">
                                <div class="text-center mb-4">
                                    <h3 class="h5 fw-bold text-dark mb-3">3 месяца</h3>
                                    <div class="mb-3">
                                        <span class="h2 fw-bold text-dark">5370₴</span>
                                        <span class="text-muted">/ 3 месяца</span>
                                    </div>
                                    <p class="text-muted small">1790₴ / месяц</p>
                                </div>
                                
                                <ul class="list-unstyled mb-4">
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Управление клиентами</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Записи и календарь</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Базовая аналитика</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Email уведомления</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Поддержка</li>
                                </ul>
                                
                                <a href="#" class="btn btn-success btn-lg w-100" data-bs-toggle="modal" data-bs-target="#registerModal">
                                    Выбрать план
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 6 месяцев -->
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card h-100 border-0 shadow-sm position-relative">
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-warning">Экономия 15%</span>
                            </div>
                            <div class="card-body p-4">
                                <div class="text-center mb-4">
                                    <h3 class="h5 fw-bold text-dark mb-3">6 месяцев</h3>
                                    <div class="mb-3">
                                        <span class="h2 fw-bold text-dark">10150₴</span>
                                        <span class="text-muted">/ 6 месяцев</span>
                                    </div>
                                    <p class="text-muted small">1692₴ / месяц</p>
                                </div>
                                
                                <ul class="list-unstyled mb-4">
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Управление клиентами</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Записи и календарь</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Базовая аналитика</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Email уведомления</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Поддержка</li>
                                </ul>
                                
                                <a href="#" class="btn btn-warning btn-lg w-100" data-bs-toggle="modal" data-bs-target="#registerModal">
                                    Выбрать план
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Год -->
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card h-100 border-0 shadow-sm position-relative">
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-danger">Экономия 25%</span>
                            </div>
                            <div class="card-body p-4">
                                <div class="text-center mb-4">
                                    <h3 class="h5 fw-bold text-dark mb-3">Год</h3>
                                    <div class="mb-3">
                                        <span class="h2 fw-bold text-dark">17900₴</span>
                                        <span class="text-muted">/ год</span>
                                    </div>
                                    <p class="text-muted small">1492₴ / месяц</p>
                                </div>
                                
                                <ul class="list-unstyled mb-4">
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Управление клиентами</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Записи и календарь</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Базовая аналитика</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Email уведомления</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Поддержка</li>
                                </ul>
                                
                                <a href="#" class="btn btn-danger btn-lg w-100" data-bs-toggle="modal" data-bs-target="#registerModal">
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
                                Можно ли изменить тариф в процессе использования?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Да, вы можете изменить тариф в любое время. При переходе на более дорогой план доплата будет рассчитана пропорционально оставшимся дням текущего периода.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item border-0 shadow-sm mb-3">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                Что происходит после окончания пробного периода?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                После окончания 7-дневного пробного периода вам нужно будет выбрать один из платных тарифов для продолжения работы с системой.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item border-0 shadow-sm mb-3">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                Есть ли возможность отменить подписку?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Да, вы можете отменить подписку в любое время. Доступ к системе сохранится до конца оплаченного периода.
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
                    <a href="#" class="btn btn-light btn-lg" data-bs-toggle="modal" data-bs-target="#registerModal">
                        Начать бесплатно
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