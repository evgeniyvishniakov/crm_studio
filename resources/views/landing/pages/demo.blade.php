@extends('landing.layouts.app')
@section('title', 'Демо - Trimora')
@section('description', 'Посмотрите, как работает Trimora в действии. Интерактивное демо основных функций системы.')
@section('content')

<section class="bg-light py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold mb-4 gradient-text section-title">Демо Trimora</h1>
                <p class="lead text-muted typing-effect" data-text="Посмотрите, как работает система управления салоном красоты в действии">Посмотрите, как работает система управления салоном красоты в действии</p>
            </div>
        </div>
    </div>
</section>

<!-- Demo Navigation -->
<section class="py-5">
    <div class="container">
        <div class="demo-tabs">
            <ul class="nav nav-tabs" id="demoTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active magnetic" id="dashboard-tab" data-bs-toggle="tab" data-bs-target="#dashboard" type="button" role="tab">
                        <i class="fas fa-chart-line me-2"></i>Панель управления
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link magnetic" id="clients-tab" data-bs-toggle="tab" data-bs-target="#clients" type="button" role="tab">
                        <i class="fas fa-users me-2"></i>Клиенты
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link magnetic" id="appointments-tab" data-bs-toggle="tab" data-bs-target="#appointments" type="button" role="tab">
                        <i class="fas fa-calendar-alt me-2"></i>Записи
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link magnetic" id="inventory-tab" data-bs-toggle="tab" data-bs-target="#inventory" type="button" role="tab">
                        <i class="fas fa-boxes me-2"></i>Склад
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link magnetic" id="analytics-tab" data-bs-toggle="tab" data-bs-target="#analytics" type="button" role="tab">
                        <i class="fas fa-chart-bar me-2"></i>Аналитика
                    </button>
                </li>
            </ul>
            
            <div class="tab-content" id="demoTabsContent">
                <!-- Dashboard Tab -->
                <div class="tab-pane fade show active" id="dashboard" role="tabpanel">
                    <div class="row g-4">
                        <div class="col-12">
                            <h4 class="mb-4 section-title">Панель управления</h4>
                            <div class="row g-3">
                                <div class="col-lg-3 col-md-6 feature-item">
                                    <div class="stat-card bg-success-light interactive-element card-3d">
                                        <div class="stat-icon bg-flat-color-1">
                                            <i class="fas fa-coins"></i>
                                        </div>
                                        <div class="stat-content">
                                            <h3 class="stat-title">Прибыль</h3>
                                            <p class="stat-value counter" data-target="125400">₽125,400</p>
                                            <small class="text-success animate-pulse"><i class="fas fa-arrow-up"></i> +15.3%</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 feature-item">
                                    <div class="stat-card bg-info-light interactive-element card-3d">
                                        <div class="stat-icon bg-flat-color-3">
                                            <i class="fas fa-shopping-cart"></i>
                                        </div>
                                        <div class="stat-content">
                                            <h3 class="stat-title">Продажи товаров</h3>
                                            <p class="stat-value counter" data-target="45200">₽45,200</p>
                                            <small class="text-success animate-pulse"><i class="fas fa-arrow-up"></i> +8.7%</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 feature-item">
                                    <div class="stat-card bg-warning-light interactive-element card-3d">
                                        <div class="stat-icon bg-flat-color-4">
                                            <i class="fas fa-spa"></i>
                                        </div>
                                        <div class="stat-content">
                                            <h3 class="stat-title">Продажи услуг</h3>
                                            <p class="stat-value counter" data-target="80200">₽80,200</p>
                                            <small class="text-success animate-pulse"><i class="fas fa-arrow-up"></i> +12.5%</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 feature-item">
                                    <div class="stat-card bg-danger-light interactive-element card-3d">
                                        <div class="stat-icon bg-flat-color-4">
                                            <i class="fas fa-credit-card"></i>
                                        </div>
                                        <div class="stat-content">
                                            <h3 class="stat-title">Расходы</h3>
                                            <p class="stat-value counter" data-target="32800">₽32,800</p>
                                            <small class="text-danger animate-pulse"><i class="fas fa-arrow-down"></i> -5.2%</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-8 feature-item">
                            <div class="chart-container card-3d">
                                <h5 class="chart-title">Динамика показателей</h5>
                                <canvas id="dashboardChart" width="400" height="200"></canvas>
                            </div>
                        </div>
                        
                        <div class="col-lg-4 feature-item">
                            <div class="card card-3d">
                                <div class="card-header bg-flat-color-1 text-white">
                                    <h6 class="mb-0"><i class="fas fa-calendar me-2"></i>Календарь</h6>
                                </div>
                                <div class="card-body">
                                    <div class="demo-calendar">
                                        <div class="row g-2">
                                            <div class="col-3">
                                                <div class="calendar-day has-appointment interactive-element">
                                                    <small>15</small>
                                                    <div class="badge bg-success animate-pulse">3</div>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="calendar-day interactive-element">
                                                    <small>16</small>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="calendar-day has-appointment interactive-element">
                                                    <small>17</small>
                                                    <div class="badge bg-success animate-pulse">5</div>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="calendar-day interactive-element">
                                                    <small>18</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Clients Tab -->
                <div class="tab-pane fade" id="clients" role="tabpanel">
                    <div class="row">
                        <div class="col-12">
                            <h4 class="mb-4 section-title">Управление клиентами</h4>
                            <div class="card card-3d">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">Список клиентов</h6>
                                    <button class="btn btn-primary btn-sm animate-glow">
                                        <i class="fas fa-plus me-2"></i>Добавить клиента
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Клиент</th>
                                                    <th>Телефон</th>
                                                    <th>Тип</th>
                                                    <th>Последний визит</th>
                                                    <th>Сумма покупок</th>
                                                    <th>Действия</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="interactive-element">
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar bg-flat-color-1 text-white rounded-circle me-3 magnetic" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                                                АК
                                                            </div>
                                                            <div>
                                                                <strong>Анна Ковалева</strong>
                                                                <br><small class="text-muted">anna@email.com</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>+7 (999) 123-45-67</td>
                                                    <td><span class="badge bg-success animate-pulse">Постоянный</span></td>
                                                    <td>15.01.2024</td>
                                                    <td>₽12,500</td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-primary magnetic">Просмотр</button>
                                                    </td>
                                                </tr>
                                                <tr class="interactive-element">
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar bg-flat-color-2 text-white rounded-circle me-3 magnetic" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                                                МП
                                                            </div>
                                                            <div>
                                                                <strong>Мария Петрова</strong>
                                                                <br><small class="text-muted">maria@email.com</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>+7 (999) 234-56-78</td>
                                                    <td><span class="badge bg-warning animate-pulse">Новый</span></td>
                                                    <td>10.01.2024</td>
                                                    <td>₽3,200</td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-primary magnetic">Просмотр</button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Appointments Tab -->
                <div class="tab-pane fade" id="appointments" role="tabpanel">
                    <div class="row">
                        <div class="col-12">
                            <h4 class="mb-4 section-title">Управление записями</h4>
                            <div class="row g-4">
                                <div class="col-lg-8 feature-item">
                                    <div class="card card-3d">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">Календарь записей</h6>
                                            <div class="btn-group">
                                                <button class="btn btn-outline-primary btn-sm magnetic">Месяц</button>
                                                <button class="btn btn-outline-primary btn-sm magnetic">Неделя</button>
                                                <button class="btn btn-outline-primary btn-sm magnetic">День</button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="demo-calendar">
                                                <div class="row g-2">
                                                    <div class="col-3">
                                                        <div class="calendar-day has-appointment interactive-element">
                                                            <small>15</small>
                                                            <div class="badge bg-success animate-pulse">3 записи</div>
                                                        </div>
                                                    </div>
                                                    <div class="col-3">
                                                        <div class="calendar-day interactive-element">
                                                            <small>16</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-3">
                                                        <div class="calendar-day has-appointment interactive-element">
                                                            <small>17</small>
                                                            <div class="badge bg-success animate-pulse">5 записей</div>
                                                        </div>
                                                    </div>
                                                    <div class="col-3">
                                                        <div class="calendar-day interactive-element">
                                                            <small>18</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-lg-4 feature-item">
                                    <div class="card card-3d">
                                        <div class="card-header">
                                            <h6 class="mb-0">Ближайшие записи</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="appointment-item border-bottom pb-3 mb-3 interactive-element">
                                                <div class="d-flex justify-content-between">
                                                    <div>
                                                        <strong>Анна Ковалева</strong>
                                                        <br><small class="text-muted">Стрижка + окрашивание</small>
                                                    </div>
                                                    <div class="text-end">
                                                        <small class="text-muted">15.01.2024</small>
                                                        <br><strong>14:00</strong>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="appointment-item border-bottom pb-3 mb-3 interactive-element">
                                                <div class="d-flex justify-content-between">
                                                    <div>
                                                        <strong>Мария Петрова</strong>
                                                        <br><small class="text-muted">Маникюр</small>
                                                    </div>
                                                    <div class="text-end">
                                                        <small class="text-muted">15.01.2024</small>
                                                        <br><strong>16:30</strong>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="appointment-item interactive-element">
                                                <div class="d-flex justify-content-between">
                                                    <div>
                                                        <strong>Елена Сидорова</strong>
                                                        <br><small class="text-muted">Массаж</small>
                                                    </div>
                                                    <div class="text-end">
                                                        <small class="text-muted">17.01.2024</small>
                                                        <br><strong>10:00</strong>
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
                
                <!-- Inventory Tab -->
                <div class="tab-pane fade" id="inventory" role="tabpanel">
                    <div class="row">
                        <div class="col-12">
                            <h4 class="mb-4 section-title">Управление складом</h4>
                            <div class="card card-3d">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">Товары на складе</h6>
                                    <button class="btn btn-primary btn-sm animate-glow">
                                        <i class="fas fa-plus me-2"></i>Добавить товар
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Товар</th>
                                                    <th>Категория</th>
                                                    <th>Остаток</th>
                                                    <th>Закупочная цена</th>
                                                    <th>Розничная цена</th>
                                                    <th>Действия</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="interactive-element">
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="product-image bg-light rounded me-3 magnetic" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                                                <i class="fas fa-image text-muted"></i>
                                                            </div>
                                                            <div>
                                                                <strong>Шампунь для волос</strong>
                                                                <br><small class="text-muted">Professional Care</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>Уход за волосами</td>
                                                    <td><span class="badge bg-success animate-pulse">45 шт.</span></td>
                                                    <td>₽800</td>
                                                    <td>₽1,200</td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-primary magnetic">Редактировать</button>
                                                    </td>
                                                </tr>
                                                <tr class="interactive-element">
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="product-image bg-light rounded me-3 magnetic" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                                                <i class="fas fa-image text-muted"></i>
                                                            </div>
                                                            <div>
                                                                <strong>Крем для лица</strong>
                                                                <br><small class="text-muted">Anti-Age</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>Уход за лицом</td>
                                                    <td><span class="badge bg-warning animate-pulse">12 шт.</span></td>
                                                    <td>₽1,200</td>
                                                    <td>₽1,800</td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-primary magnetic">Редактировать</button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Analytics Tab -->
                <div class="tab-pane fade" id="analytics" role="tabpanel">
                    <div class="row g-4">
                        <div class="col-lg-6 feature-item">
                            <div class="chart-container card-3d">
                                <h5 class="chart-title">Популярные услуги</h5>
                                <canvas id="servicesChart" width="400" height="200"></canvas>
                            </div>
                        </div>
                        
                        <div class="col-lg-6 feature-item">
                            <div class="chart-container card-3d">
                                <h5 class="chart-title">Динамика клиентов</h5>
                                <canvas id="clientsChart" width="400" height="200"></canvas>
                            </div>
                        </div>
                        
                        <div class="col-12 feature-item">
                            <div class="card card-3d">
                                <div class="card-header">
                                    <h6 class="mb-0">Топ-5 клиентов по выручке</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center mb-3 interactive-element">
                                                <div class="avatar bg-flat-color-1 text-white rounded-circle me-3 magnetic" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                                    1
                                                </div>
                                                <div class="flex-grow-1">
                                                    <strong>Анна Ковалева</strong>
                                                    <br><small class="text-muted">12,500 ₽</small>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center mb-3 interactive-element">
                                                <div class="avatar bg-flat-color-2 text-white rounded-circle me-3 magnetic" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                                    2
                                                </div>
                                                <div class="flex-grow-1">
                                                    <strong>Мария Петрова</strong>
                                                    <br><small class="text-muted">8,900 ₽</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center mb-3 interactive-element">
                                                <div class="avatar bg-flat-color-3 text-white rounded-circle me-3 magnetic" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                                    3
                                                </div>
                                                <div class="flex-grow-1">
                                                    <strong>Елена Сидорова</strong>
                                                    <br><small class="text-muted">7,200 ₽</small>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center mb-3 interactive-element">
                                                <div class="avatar bg-flat-color-4 text-white rounded-circle me-3 magnetic" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                                    4
                                                </div>
                                                <div class="flex-grow-1">
                                                    <strong>Ольга Иванова</strong>
                                                    <br><small class="text-muted">6,800 ₽</small>
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
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="bg-gradient-primary text-white py-5">
    <div class="container text-center">
        <h2 class="display-6 fw-bold mb-4">Понравилось демо?</h2>
                        <p class="lead mb-4">Начните использовать Trimora уже сегодня</p>
        <a href="#" class="btn btn-light btn-lg animate-glow" data-bs-toggle="modal" data-bs-target="#registerModal">
            <i class="fas fa-rocket me-2"></i>Попробовать бесплатно
        </a>
    </div>
</section>

@include('landing.components.register-modal')
@endsection

@push('styles')
<style>
.bg-danger-light {
    background: linear-gradient(135deg, rgba(251, 150, 120, 0.1) 0%, rgba(251, 150, 120, 0.05) 100%);
    color: var(--warning-color);
}

.avatar {
    font-weight: 600;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.avatar:hover {
    transform: scale(1.1) rotate(5deg);
}

.appointment-item:last-child {
    border-bottom: none !important;
}

.appointment-item {
    transition: all 0.3s ease;
    padding: 0.5rem;
    border-radius: 8px;
}

.appointment-item:hover {
    background: rgba(0, 194, 146, 0.05);
    transform: translateX(5px);
}

.product-image {
    background: var(--light-bg);
    transition: all 0.3s ease;
}

.product-image:hover {
    background: var(--gradient-primary);
    color: white;
}

/* Анимации для вкладок */
.nav-tabs .nav-link {
    transition: all 0.3s ease;
}

.nav-tabs .nav-link:hover {
    transform: translateY(-2px);
}

/* Эффекты для таблиц */
.table tbody tr {
    transition: all 0.3s ease;
}

.table tbody tr:hover {
    background: rgba(0, 194, 146, 0.05);
    transform: scale(1.01);
}

/* Анимации для бейджей */
.badge.animate-pulse {
    animation: pulse 2s infinite;
}

/* Эффекты для кнопок */
.btn.animate-glow {
    animation: glow 2s ease-in-out infinite alternate;
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

.feature-item {
    opacity: 0;
    transform: translateY(30px);
    transition: all 0.8s ease;
}

.feature-item.revealed {
    opacity: 1;
    transform: translateY(0);
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('landing/main.js') }}"></script>
<script>
// Dashboard Chart
const dashboardCtx = document.getElementById('dashboardChart').getContext('2d');
new Chart(dashboardCtx, {
    type: 'line',
    data: {
        labels: ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн'],
        datasets: [{
            label: 'Прибыль',
            data: [65000, 72000, 85000, 92000, 105000, 125400],
            borderColor: '#00c292',
            backgroundColor: 'rgba(0, 194, 146, 0.1)',
            tension: 0.4,
            borderWidth: 3
        }, {
            label: 'Продажи товаров',
            data: [25000, 28000, 32000, 35000, 40000, 45200],
            borderColor: '#03a9f3',
            backgroundColor: 'rgba(3, 169, 243, 0.1)',
            tension: 0.4,
            borderWidth: 3
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        },
        animation: {
            duration: 2000,
            easing: 'easeInOutQuart'
        }
    }
});

// Services Chart
const servicesCtx = document.getElementById('servicesChart').getContext('2d');
new Chart(servicesCtx, {
    type: 'doughnut',
    data: {
        labels: ['Стрижка', 'Окрашивание', 'Маникюр', 'Массаж', 'Макияж'],
        datasets: [{
            data: [30, 25, 20, 15, 10],
            backgroundColor: [
                '#00c292',
                '#ab8ce4',
                '#03a9f3',
                '#fb9678',
                '#66bb6a'
            ],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
            }
        },
        animation: {
            duration: 2000,
            easing: 'easeInOutQuart'
        }
    }
});

// Clients Chart
const clientsCtx = document.getElementById('clientsChart').getContext('2d');
new Chart(clientsCtx, {
    type: 'bar',
    data: {
        labels: ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн'],
        datasets: [{
            label: 'Новые клиенты',
            data: [45, 52, 48, 61, 55, 67],
            backgroundColor: '#00c292',
            borderRadius: 8
        }, {
            label: 'Постоянные клиенты',
            data: [120, 135, 142, 158, 165, 180],
            backgroundColor: '#ab8ce4',
            borderRadius: 8
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        },
        animation: {
            duration: 2000,
            easing: 'easeInOutQuart'
        }
    }
});

// Дополнительные интерактивные эффекты для демо
document.addEventListener('DOMContentLoaded', function() {
    // Эффект для вкладок
    const tabButtons = document.querySelectorAll('.nav-tabs .nav-link');
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Анимация переключения вкладок
            const targetTab = document.querySelector(this.getAttribute('data-bs-target'));
            if (targetTab) {
                targetTab.style.opacity = '0';
                targetTab.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    targetTab.style.transition = 'all 0.5s ease';
                    targetTab.style.opacity = '1';
                    targetTab.style.transform = 'translateY(0)';
                }, 100);
            }
        });
    });
    
    // Эффект для календарных дней
    const calendarDays = document.querySelectorAll('.calendar-day');
    calendarDays.forEach(day => {
        day.addEventListener('click', function() {
            this.style.transform = 'scale(1.1)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 200);
        });
    });
    
    // Эффект для строк таблицы
    const tableRows = document.querySelectorAll('.table tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.01) translateX(5px)';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1) translateX(0)';
        });
    });
});
</script>
@endpush