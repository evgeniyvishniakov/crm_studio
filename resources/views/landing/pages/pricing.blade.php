@extends('landing.layouts.app')

@section('title', 'Тарифы - CRM Studio')
@section('description', 'Выберите подходящий тариф для вашего салона красоты. Начните с бесплатного пробного периода на 7 дней.')

@section('content')
<!-- Hero Section -->
<section class="bg-light py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold mb-4 gradient-text section-title">Простые и прозрачные тарифы</h1>
                <p class="lead text-muted typing-effect" data-text="Выберите план, который подходит именно вашему бизнесу. Начните бесплатно на 7 дней.">Выберите план, который подходит именно вашему бизнесу. Начните бесплатно на 7 дней.</p>
            </div>
        </div>
    </div>
</section>

<!-- Pricing Section -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <!-- Бесплатный тариф -->
            <div class="col-lg-4 col-md-6 mb-4 feature-item">
                <div class="card h-100 border-0 shadow-sm pricing-card card-3d interactive-element">
                    <div class="card-body p-4 text-center">
                        <div class="pricing-header mb-4">
                            <div class="stat-icon bg-flat-color-1 mx-auto mb-3 feature-icon">
                                <i class="fas fa-gift"></i>
                            </div>
                            <h3 class="card-title gradient-text">Пробный период</h3>
                            <div class="pricing-price">
                                <span class="display-4 fw-bold gradient-text counter" data-target="0">0₽</span>
                                <span class="text-muted">/ 7 дней</span>
                            </div>
                            <p class="text-muted">Полный доступ ко всем функциям</p>
                        </div>
                        
                        <ul class="list-unstyled mb-4">
                            <li class="mb-3 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Все функции CRM</li>
                            <li class="mb-3 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Управление клиентами</li>
                            <li class="mb-3 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Записи и календарь</li>
                            <li class="mb-3 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Базовая аналитика</li>
                            <li class="mb-3 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Email уведомления</li>
                            <li class="mb-3 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Поддержка</li>
                        </ul>
                        
                        <a href="#" class="btn btn-outline-primary btn-lg w-100 animate-glow" data-bs-toggle="modal" data-bs-target="#registerModal">
                            <i class="fas fa-rocket me-2"></i>Начать бесплатно
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Базовый тариф -->
            <div class="col-lg-4 col-md-6 mb-4 feature-item">
                <div class="card h-100 border-0 shadow pricing-card popular card-3d interactive-element">
                    <div class="card-body p-4 text-center">
                        <div class="popular-badge">
                            <span class="badge bg-gradient-primary animate-pulse">Популярный</span>
                        </div>
                        <div class="pricing-header mb-4">
                            <div class="stat-icon bg-flat-color-3 mx-auto mb-3 feature-icon">
                                <i class="fas fa-star"></i>
                            </div>
                            <h3 class="card-title gradient-text">Базовый</h3>
                            <div class="pricing-price">
                                <span class="display-4 fw-bold gradient-text counter" data-target="990">990₽</span>
                                <span class="text-muted">/ месяц</span>
                            </div>
                            <p class="text-muted">Для небольших салонов</p>
                        </div>
                        
                        <ul class="list-unstyled mb-4">
                            <li class="mb-3 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Все функции пробного периода</li>
                            <li class="mb-3 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>До 5 пользователей</li>
                            <li class="mb-3 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Расширенная аналитика</li>
                            <li class="mb-3 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Telegram уведомления</li>
                            <li class="mb-3 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Виджет для сайта</li>
                            <li class="mb-3 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Приоритетная поддержка</li>
                        </ul>
                        
                        <a href="#" class="btn btn-primary btn-lg w-100 animate-glow" data-bs-toggle="modal" data-bs-target="#registerModal">
                            <i class="fas fa-check me-2"></i>Выбрать план
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Премиум тариф -->
            <div class="col-lg-4 col-md-6 mb-4 feature-item">
                <div class="card h-100 border-0 shadow-sm pricing-card card-3d interactive-element">
                    <div class="card-body p-4 text-center">
                        <div class="pricing-header mb-4">
                            <div class="stat-icon bg-flat-color-5 mx-auto mb-3 feature-icon">
                                <i class="fas fa-crown"></i>
                            </div>
                            <h3 class="card-title gradient-text">Премиум</h3>
                            <div class="pricing-price">
                                <span class="display-4 fw-bold gradient-text counter" data-target="1990">1990₽</span>
                                <span class="text-muted">/ месяц</span>
                            </div>
                            <p class="text-muted">Для крупных салонов</p>
                        </div>
                        
                        <ul class="list-unstyled mb-4">
                            <li class="mb-3 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Все функции базового плана</li>
                            <li class="mb-3 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Неограниченное количество пользователей</li>
                            <li class="mb-3 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Полная ERP система</li>
                            <li class="mb-3 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Управление складом</li>
                            <li class="mb-3 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Закупки и продажи</li>
                            <li class="mb-3 feature-item"><i class="fas fa-check text-success me-2 feature-icon"></i>Персональный менеджер</li>
                        </ul>
                        
                        <a href="#" class="btn btn-outline-primary btn-lg w-100 animate-glow" data-bs-toggle="modal" data-bs-target="#registerModal">
                            <i class="fas fa-crown me-2"></i>Выбрать план
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Comparison -->
<section class="bg-light py-5">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-lg-8 mx-auto">
                <h2 class="display-5 fw-bold mb-4 section-title">Сравнение функций</h2>
                <p class="lead text-muted">Подробное сравнение возможностей каждого тарифа</p>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-lg card-3d">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-gradient-primary text-white">
                                    <tr>
                                        <th class="border-0">Функция</th>
                                        <th class="border-0 text-center">Пробный</th>
                                        <th class="border-0 text-center">Базовый</th>
                                        <th class="border-0 text-center">Премиум</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="interactive-element">
                                        <td><strong>Управление клиентами</strong></td>
                                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                    </tr>
                                    <tr class="interactive-element">
                                        <td><strong>Записи и календарь</strong></td>
                                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                    </tr>
                                    <tr class="interactive-element">
                                        <td><strong>Базовая аналитика</strong></td>
                                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                    </tr>
                                    <tr class="interactive-element">
                                        <td><strong>Расширенная аналитика</strong></td>
                                        <td class="text-center"><i class="fas fa-times text-muted"></i></td>
                                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                    </tr>
                                    <tr class="interactive-element">
                                        <td><strong>Telegram уведомления</strong></td>
                                        <td class="text-center"><i class="fas fa-times text-muted"></i></td>
                                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                    </tr>
                                    <tr class="interactive-element">
                                        <td><strong>Виджет для сайта</strong></td>
                                        <td class="text-center"><i class="fas fa-times text-muted"></i></td>
                                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                    </tr>
                                    <tr class="interactive-element">
                                        <td><strong>Управление складом</strong></td>
                                        <td class="text-center"><i class="fas fa-times text-muted"></i></td>
                                        <td class="text-center"><i class="fas fa-times text-muted"></i></td>
                                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                    </tr>
                                    <tr class="interactive-element">
                                        <td><strong>Закупки и продажи</strong></td>
                                        <td class="text-center"><i class="fas fa-times text-muted"></i></td>
                                        <td class="text-center"><i class="fas fa-times text-muted"></i></td>
                                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                    </tr>
                                    <tr class="interactive-element">
                                        <td><strong>Количество пользователей</strong></td>
                                        <td class="text-center">1</td>
                                        <td class="text-center">До 5</td>
                                        <td class="text-center">Неограниченно</td>
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

<!-- FAQ Section -->
<section class="py-5">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-lg-8 mx-auto">
                <h2 class="display-5 fw-bold mb-4 section-title">Часто задаваемые вопросы</h2>
                <p class="lead text-muted">Ответы на популярные вопросы о тарифах</p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-6 feature-item">
                <div class="card border-0 shadow-sm card-3d">
                    <div class="card-body p-4">
                        <h5 class="card-title">
                            <i class="fas fa-question-circle text-primary me-2"></i>
                            Можно ли изменить тариф?
                        </h5>
                        <p class="card-text text-muted">Да, вы можете изменить тариф в любое время. При переходе на более дорогой план вы платите пропорционально оставшимся дням.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6 feature-item">
                <div class="card border-0 shadow-sm card-3d">
                    <div class="card-body p-4">
                        <h5 class="card-title">
                            <i class="fas fa-question-circle text-primary me-2"></i>
                            Что происходит после пробного периода?
                        </h5>
                        <p class="card-text text-muted">После окончания пробного периода система автоматически переведет вас на базовый тариф. Вы можете отменить подписку в любое время.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6 feature-item">
                <div class="card border-0 shadow-sm card-3d">
                    <div class="card-body p-4">
                        <h5 class="card-title">
                            <i class="fas fa-question-circle text-primary me-2"></i>
                            Есть ли скидки при оплате за год?
                        </h5>
                        <p class="card-text text-muted">Да, при оплате за год мы предоставляем скидку 20%. Это выгодное предложение для стабильного бизнеса.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6 feature-item">
                <div class="card border-0 shadow-sm card-3d">
                    <div class="card-body p-4">
                        <h5 class="card-title">
                            <i class="fas fa-question-circle text-primary me-2"></i>
                            Можно ли отменить подписку?
                        </h5>
                        <p class="card-text text-muted">Да, вы можете отменить подписку в любое время. Доступ к системе сохранится до конца оплаченного периода.</p>
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
        <p class="lead mb-4">Выберите подходящий тариф и начните использовать CRM Studio уже сегодня</p>
        <a href="#" class="btn btn-light btn-lg animate-glow" data-bs-toggle="modal" data-bs-target="#registerModal">
            <i class="fas fa-rocket me-2"></i>Начать бесплатно
        </a>
    </div>
</section>

@include('landing.components.register-modal')
@endsection

@push('styles')
<style>
/* Специальные стили для страницы цен */
.pricing-card {
    transition: all 0.3s ease;
    border-radius: 20px;
    overflow: hidden;
}

.pricing-card:hover {
    transform: translateY(-10px);
}

.pricing-card.popular {
    border: 2px solid var(--primary-color);
    position: relative;
}

.popular-badge {
    position: absolute;
    top: -10px;
    left: 50%;
    transform: translateX(-50%);
}

.pricing-price {
    margin: 1rem 0;
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

/* Эффекты для таблицы */
.table tbody tr {
    transition: all 0.3s ease;
}

.table tbody tr:hover {
    background: rgba(0, 194, 146, 0.05);
    transform: scale(1.01);
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