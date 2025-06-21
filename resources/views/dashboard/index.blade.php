@extends('layouts.app')

@section('content')
<style>
/* Dashboard Statistics Cards - Inline Styles */
body {
    background: #f8f9fa !important;
    margin: 0 !important;
    padding: 0 !important;
}

.dashboard-container {
    padding: 24px !important;
    background: #fff !important;
    border-radius: 16px !important;
    box-shadow: 0 4px 24px rgba(0,0,0,0.08) !important;
    border: 1px solid #e5e7eb !important;
    min-height: 100vh !important;
    max-width: 1400px !important;
    margin: 32px auto 0 auto !important;
    width: 100% !important;
    box-sizing: border-box !important;
    visibility: visible !important;
    opacity: 1 !important;
}

.dashboard-title {
    font-size: 2rem !important;
    font-weight: 700 !important;
    color: #2d3748 !important;
    margin-bottom: 1.5rem !important;
    text-align: center !important;
}

.stats-grid {
    display: grid !important;
    grid-template-columns: repeat(4, 1fr) !important;
    gap: 1.5rem !important;
    margin-bottom: 2rem !important;
    align-items: stretch !important;
    max-width: 100% !important;
    width: 100% !important;
    visibility: visible !important;
    opacity: 1 !important;
}

.stat-card {
    height: 150px !important;
    min-width: 0 !important;
    max-width: 100% !important;
    width: 100% !important;
    display: flex !important;
    flex-direction: column !important;
    justify-content: center !important;
    align-items: flex-start !important;
    padding: 1.2rem 1.1rem !important;
    box-sizing: border-box !important;
    background: white !important;
    border-radius: 12px !important;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
    transition: all 0.3s ease !important;
    position: relative !important;
    overflow: hidden !important;
    flex-shrink: 0 !important;
}

.stat-card .stat-content {
    flex: 1 1 auto !important;
    display: flex !important;
    flex-direction: column !important;
    justify-content: center !important;
}

.stat-icon {
    width: 50px !important;
    height: 50px !important;
    border-radius: 10px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    background: linear-gradient(135deg, var(--card-color), var(--card-color-light)) !important;
    color: white !important;
    font-size: 1.3rem !important;
    flex-shrink: 0 !important;
}

.stat-title {
    font-size: 0.75rem !important;
    font-weight: 600 !important;
    color: #718096 !important;
    margin: 0 0 0.3rem 0 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.05em !important;
}

.stat-value {
    font-size: 1.4rem !important;
    font-weight: 700 !important;
    color: #2d3748 !important;
    margin: 0 0 0.2rem 0 !important;
    line-height: 1.2 !important;
    opacity: 0 !important;
    transition: opacity 0.3s ease !important;
    min-width: 80px !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

.stat-value.animated {
    opacity: 1 !important;
}

.stat-change {
    font-size: 0.7rem !important;
    font-weight: 500 !important;
    margin: 0 !important;
    display: flex !important;
    align-items: center !important;
    gap: 0.2rem !important;
}

.profit-card {
    --card-color: #10b981 !important;
    --card-color-light: #34d399 !important;
}

.sales-card {
    --card-color: #3b82f6 !important;
    --card-color-light: #60a5fa !important;
}

.clients-card {
    --card-color: #8b5cf6 !important;
    --card-color-light: #a78bfa !important;
}

.appointments-card {
    --card-color: #f59e0b !important;
    --card-color-light: #fbbf24 !important;
}

@media (max-width: 1100px) {
    .stats-grid {
        grid-template-columns: 1fr !important;
        gap: 1rem !important;
    }
    .stat-card.profit-card {
        max-width: 100%;
        margin-bottom: 1rem;
    }
    .stat-cards-group {
        grid-template-columns: 1fr !important;
        gap: 1rem !important;
        position: static !important;
        opacity: 1 !important;
        transform: none !important;
        pointer-events: all !important;
        height: auto;
    }
    .stat-cards-container {
        min-height: unset !important;
        display: block !important;
    }
}

/* Переключатели */
.dashboard-tabs {
    display: flex !important;
    justify-content: center !important;
    gap: 1rem !important;
    margin-bottom: 2rem !important;
}

.tab-button {
    background: white !important;
    border: 2px solid #e2e8f0 !important;
    border-radius: 12px !important;
    padding: 0.75rem 1.5rem !important;
    font-size: 0.9rem !important;
    font-weight: 600 !important;
    color: #64748b !important;
    cursor: pointer !important;
    transition: all 0.3s ease !important;
    display: flex !important;
    align-items: center !important;
    gap: 0.5rem !important;
}

.tab-button:hover {
    border-color: #3b82f6 !important;
    color: #3b82f6 !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15) !important;
}

.tab-button.active {
    background: linear-gradient(135deg, #3b82f6, #60a5fa) !important;
    border-color: #3b82f6 !important;
    color: white !important;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3) !important;
}

.tab-button i {
    font-size: 1rem !important;
}

/* Контейнер для переключаемых карточек */
.stat-cards-container {
    display: contents !important;
}

/* Группы карточек */
.stat-cards-group {
    display: contents !important;
    position: static !important;
    opacity: 1 !important;
    transform: none !important;
    pointer-events: all !important;
    height: auto !important;
    transition: none !important;
}

.stat-cards-group:not(.active) .stat-card {
    display: none !important;
}

/* Дополнительные цвета для новых карточек */
.services-card {
    --card-color: #8b5cf6 !important;
    --card-color-light: #a78bfa !important;
}

.expenses-card {
    --card-color: #ef4444 !important;
    --card-color-light: #f87171 !important;
}

.procedures-card {
    --card-color: #06b6d4 !important;
    --card-color-light: #22d3ee !important;
}

/* Анимация при переключении */
.stat-cards-group.finances-group {
    transform: translateX(-20px) !important;
}

.stat-cards-group.activity-group {
    transform: translateX(20px) !important;
}

.stat-cards-group.active.finances-group,
.stat-cards-group.active.activity-group {
    transform: translateX(0) !important;
}

/* Стили для period-фильтров */
.period-filters {
    display: flex;
    gap: 0.5rem;
}
.period-btn {
    background: #fff;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 0.5rem 1.2rem;
    font-size: 0.95rem;
    font-weight: 600;
    color: #64748b;
    cursor: pointer;
    transition: all 0.3s;
    outline: none;
}
.period-btn:hover {
    border-color: #3b82f6;
    color: #3b82f6;
    box-shadow: 0 2px 8px rgba(59,130,246,0.10);
}
.period-btn.active {
    background: linear-gradient(135deg, #3b82f6, #60a5fa);
    border-color: #3b82f6;
    color: #fff;
    box-shadow: 0 4px 12px rgba(59,130,246,0.15);
}

/* Удаляю все ::after для .metric-toggle, если есть */
.metric-toggle::after {
    display: none !important;
    content: none !important;
}

.metric-dropdown {
    position: relative;
}
.metric-toggle {
    background: #fff;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    padding: 0.6rem 1.2rem;
    font-size: 1rem;
    font-weight: 600;
    color: #374151;
    box-shadow: 0 2px 8px rgba(59,130,246,0.04);
    transition: border 0.2s, box-shadow 0.2s;
}
.metric-toggle:focus, .metric-toggle:hover {
    border-color: #3b82f6;
    box-shadow: 0 4px 16px rgba(59,130,246,0.10);
}
.metric-menu {
    display: none;
    position: absolute;
    left: 0;
    top: 110%;
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 8px 32px rgba(59,130,246,0.13), 0 1.5px 6px rgba(0,0,0,0.04);
    min-width: 190px;
    padding: 0.4rem 0;
    z-index: 10;
    border: none;
    opacity: 0;
    transform: translateY(10px) scale(0.98);
    pointer-events: none;
    transition: opacity 0.22s cubic-bezier(.4,0,.2,1), transform 0.22s cubic-bezier(.4,0,.2,1);
}
.metric-dropdown.open .metric-menu {
    display: block;
    opacity: 1;
    transform: translateY(0) scale(1);
    pointer-events: auto;
}
.metric-item {
    width: 100%;
    background: none;
    border: none;
    outline: none;
    text-align: left;
    padding: 0.7rem 1.2rem;
    font-size: 1rem;
    color: #374151;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.18s, color 0.18s;
    border-radius: 8px;
    display: flex;
    align-items: center;
    gap: 0.7rem;
}
.metric-item:hover, .metric-item:focus {
    background: linear-gradient(90deg, #e0e7ff 0%, #f0f9ff 100%);
    color: #3b82f6;
}

/* Подсветка сегодняшней даты в календаре дашборда */
.calendar-today {
    background: #fffbe6 !important;
    border: 2px solid #ffe066 !important;
}

.fc-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    margin: 2px 2px 0 2px;
    display: inline-block;
}
.fc-dot-more {
    width: 18px;
    height: 8px;
    border-radius: 8px;
    background: #cbd5e1;
    color: #374151;
    font-size: 9px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-left: 2px;
}
.fc-daygrid-day.fc-day-today {
    background: #fffbe6 !important;
    border: none !important;
    box-shadow: 0 2px 8px rgba(255,224,102,0.13);
}

/* Ряд точек под числом дня */
.fc-daygrid-day-events {
    display: flex !important;
    flex-direction: row !important;
    justify-content: center;
    align-items: center;
    gap: 2px;
    margin-top: -4px !important; /* Пробуем сдвинуть повыше */
    min-height: 0 !important; /* Убираем минимальную высоту, чтобы не мешала */
    height: 5px !important; /* Задаем небольшую высоту */
}
.fc-dot {
    width: 5px;
    height: 5px;
    border-radius: 50%;
    display: inline-block;
    margin: 0 1px; /* Скорректировал отступы */
}
/* Убираю зелёную заливку у сегодняшнего дня, только рамка */
.fc-daygrid-day.fc-day-today {
    background: #fff !important;
    border: 2px solid #ffe066 !important;
}

/* ... предыдущие стили ... */
.fc-event-title, .fc-event-time {
    display: none !important;
}
.fc-event {
    background: none !important;
    border: none !important;
    box-shadow: none !important;
    padding: 0 !important;
    margin: 0 !important;
    min-width: 0 !important;
}

/* ... предыдущие стили ... */
.fc-daygrid-day {
    cursor: pointer !important;
}
.fc-daygrid-day.fc-day-other {
    cursor: default !important;
}
/* ... остальные стили ... */
.fc-daygrid-day:hover:not(.fc-day-other) {
    background: #f3f6fd !important;
    box-shadow: 0 2px 8px rgba(59,130,246,0.07);
    transition: background 0.18s, box-shadow 0.18s;
}
</style>

    <div class="dashboard-container">
        <h1 class="dashboard-title">CRM Analytics Dashboard</h1>

        <div class="stats-grid">
            <!-- Левая колонка: прибыль -->
            <div class="stat-card profit-card">
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-title">Прибыль</h3>
                    <p class="stat-value">{{ number_format($totalProfit, 2, '.', ' ') }} грн</p>
                    @if($showDynamics)
                        <p class="stat-change positive">
                            <i class="fas fa-arrow-up"></i>
                            15.3% с прошлого месяца
                        </p>
                    @endif
                </div>
            </div>
            <!-- Правая колонка: переключаемые карточки -->
            <div class="stat-cards-container">
                <!-- Финансы -->
                <div class="stat-cards-group finances-group active">
                    <div class="stat-card sales-card">
                        <div class="stat-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-title">Продажи товаров</h3>
                            <p class="stat-value">{{ number_format($productsRevenue, 2, '.', ' ') }} грн</p>
                            @if($showDynamics)
                                <p class="stat-change positive">
                                    <i class="fas fa-arrow-up"></i>
                                    8.7% с прошлого месяца
                                </p>
                            @endif
                        </div>
                    </div>
                    <div class="stat-card services-card">
                        <div class="stat-icon">
                            <i class="fas fa-spa"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-title">Продажи услуг</h3>
                            <p class="stat-value">{{ number_format($servicesRevenue, 2, '.', ' ') }} грн</p>
                            @if($showDynamics)
                                <p class="stat-change positive">
                                    <i class="fas fa-arrow-up"></i>
                                    12.5% с прошлого месяца
                                </p>
                            @endif
                        </div>
                    </div>
                    <div class="stat-card expenses-card">
                        <div class="stat-icon">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-title">Расходы</h3>
                            <p class="stat-value">{{ number_format($totalExpenses, 2, '.', ' ') }} грн</p>
                            @if($showDynamics)
                                <p class="stat-change negative">
                                    <i class="fas fa-arrow-down"></i>
                                    5.2% с прошлого месяца
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
                <!-- Активность -->
                <div class="stat-cards-group activity-group">
                    <div class="stat-card procedures-card">
                        <div class="stat-icon">
                            <i class="fas fa-spa"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-title">Услуги</h3>
                            <p class="stat-value">{{ $servicesCount }}</p>
                            @if($showDynamics)
                                <p class="stat-change positive">
                                    <i class="fas fa-arrow-up"></i>
                                    18.2% с прошлого месяца
                                </p>
                            @endif
                        </div>
                    </div>
                    <div class="stat-card clients-card">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-title">Клиенты</h3>
                            <p class="stat-value">{{ $clientsCount }}</p>
                            @if($showDynamics)
                                <p class="stat-change positive">
                                    <i class="fas fa-arrow-up"></i>
                                    12% с прошлого месяца
                                </p>
                            @endif
                        </div>
                    </div>
                    <div class="stat-card appointments-card">
                        <div class="stat-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-title">Записи</h3>
                            <p class="stat-value">{{ $appointmentsCount }}</p>
                            @if($showDynamics)
                                <p class="stat-change positive">
                                    <i class="fas fa-arrow-up"></i>
                                    5.2% с прошлой недели
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Переключатели -->
        <div class="dashboard-tabs">
            <button class="tab-button active" data-tab="finances">
                <i class="fas fa-chart-pie"></i>
                Финансы
            </button>
            <button class="tab-button" data-tab="activity">
                <i class="fas fa-chart-bar"></i>
                Активность
            </button>
        </div>

        <div class="dashboard-main-content" style="max-width: 1400px; margin: 0 auto; padding: 0 24px;">
            <div class="chart-container" style="width: 100%; max-width: 100%; padding: 10px; box-sizing: border-box;">
            <h3 class="chart-title">Динамика показателей</h3>
            <div class="chart-toolbar" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
                <!-- Dropdown слева -->
                <div class="dropdown metric-dropdown" style="position: relative;">
                    <button class="dropdown-toggle metric-toggle" type="button" style="display: flex; align-items: center; gap: 0.5rem; background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.5rem 1rem; font-weight: 600; cursor: pointer; min-width: 140px;">
                        <i class="fas fa-chart-line"></i>
                        <span id="selectedMetricLabel">Прибыль</span>
                        <i class="fas fa-chevron-down" style="margin-left: 0.5rem;"></i>
                    </button>
                    <div class="dropdown-menu metric-menu">
                        <button class="dropdown-item metric-item" data-type="profit"><i class="fas fa-chart-line"></i> Прибыль</button>
                        <button class="dropdown-item metric-item" data-type="sales"><i class="fas fa-shopping-cart"></i> Продажи товаров</button>
                        <button class="dropdown-item metric-item" data-type="services"><i class="fas fa-spa"></i> Продажи услуг</button>
                        <button class="dropdown-item metric-item" data-type="expenses"><i class="fas fa-credit-card"></i> Расходы</button>
                        <button class="dropdown-item metric-item" data-type="activity"><i class="fas fa-bolt"></i> Активность</button>
                    </div>
                </div>
                <!-- Фильтры справа -->
                <div class="period-filters" style="display: flex; gap: 0.5rem;">
                    <button class="period-btn" data-period="30">За месяц</button>
                    <button class="period-btn" data-period="90">За 3 месяца</button>
                    <button class="period-btn" data-period="180">За 6 месяцев</button>
                    <button class="period-btn" data-period="365">За год</button>
                </div>
            </div>
            <canvas id="universalChart" height="150"></canvas>
        </div>
            <div class="dashboard-widgets-grid-2x2" style="display: grid; grid-template-columns: 34% 64%; gap: 1.6rem; margin: 32px 0 0 0; align-items: stretch;">
                <!-- 1. Календарь -->
                <div class="widget-card calendar-widget">
                    <div class="widget-content">
                        <div class="calendar-header-modern">
                            <div class="calendar-title-container">
                                <i class="fas fa-calendar-alt calendar-icon"></i>
                                <span class="calendar-title">Календарь</span>
                            </div>
                            <div class="calendar-nav">
                                <span id="calendarMonthYearTitle" class="calendar-month-title"></span>
                                <div class="calendar-nav-group">
                                    <button id="calendarPrevBtn" class="calendar-nav-btn"><i class="fas fa-chevron-left"></i></button>
                                    <button id="calendarNextBtn" class="calendar-nav-btn"><i class="fas fa-chevron-right"></i></button>
                                </div>
                            </div>
                        </div>
                        <div id="dashboardCalendar"></div>
                    </div>
                </div>
                <!-- 2. Записи -->
                <div class="widget-card appointments-widget">
                    <div class="widget-content">
                        <h3 class="widget-title">Записи</h3>
                        <div class="appointments-table-block">
                            <table class="table-striped appointments-table">
                                <thead>
                                    <tr>
                                        <th>ДАТА</th>
                                        <th>КЛИЕНТ</th>
                                        <th>ПРОЦЕДУРА</th>
                                        <th>СТАТУС</th>
                                        <th>СТОИМОСТЬ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        function getStatusInfo($status) {
                                            $map = [
                                                'completed' => ['class' => 'done', 'name' => 'Завершено'],
                                                'pending' => ['class' => 'pending', 'name' => 'Ожидается'],
                                                'cancelled' => ['class' => 'cancel', 'name' => 'Отменено'],
                                                'rescheduled' => ['class' => 'rescheduled', 'name' => 'Перенесено'],
                                            ];
                                            return $map[$status] ?? ['class' => 'default', 'name' => ucfirst($status)];
                                        }
                                    @endphp
                                    @forelse($upcomingAppointments as $appointment)
                                        @php $statusInfo = getStatusInfo($appointment->status); @endphp
                                        <tr>
                                            <td>
                                                <span class="appt-date">{{ \Carbon\Carbon::parse($appointment->date)->isToday() ? 'Сегодня' : 'Завтра' }}</span><br>
                                                <span class="appt-time">{{ \Carbon\Carbon::parse($appointment->time)->format('H:i') }}</span>
                                            </td>
                                            <td>{{ $appointment->client->name ?? 'Клиент не найден' }}</td>
                                            <td>{{ $appointment->service->name ?? 'Услуга не найдена' }}</td>
                                            <td><span class="status-badge status-{{ $statusInfo['class'] }}">{{ $statusInfo['name'] }}</span></td>
                                            <td>{{ number_format($appointment->price, 0, '.', ' ') }} грн</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" style="text-align: center; padding: 20px; color: #888;">
                                                На сегодня и завтра активных записей нет.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- 3. Краткий отчёт за сегодня -->
                <div class="widget-card">
                    <div class="widget-content">
                        <h3 class="widget-title">Краткий отчёт за сегодня</h3>
                        <div style="display: flex; gap: 1.5rem; margin-top: 1.2rem; align-items: flex-end; justify-content: center; min-width: 0;">
                            <div style="display: flex; flex-direction: column; align-items: center; min-width: 90px;">
                                <span style="font-size: 2.1rem; font-weight: 700; color: #3b82f6; line-height: 1;">5</span>
                                <span style="font-size: 1rem; color: #64748b;">Клиентов сегодня</span>
                            </div>
                            <div style="display: flex; flex-direction: column; align-items: center; min-width: 120px;">
                                <span style="font-size: 2.1rem; font-weight: 700; color: #10b981; line-height: 1;">3 200 грн</span>
                                <span style="font-size: 1rem; color: #64748b;">Прибыль за день</span>
                            </div>
                            <div style="display: flex; flex-direction: column; align-items: center; min-width: 90px;">
                                <span style="font-size: 2.1rem; font-weight: 700; color: #8b5cf6; line-height: 1;">4</span>
                                <span style="font-size: 1rem; color: #64748b;">Завершено процедур</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- 4. To Do List -->
                <div class="widget-card">
                    <div class="widget-content">
                        <h3 class="widget-title" style="margin-bottom:1rem;">To Do List</h3>
                        <ul class="todo-list-minimal">
                            <li>
                                <span class="todo-drag"><i class="fas fa-grip-lines"></i></span>
                                <input type="checkbox" id="todo1-minimal">
                                <label for="todo1-minimal">Conveniently fabricate interactive technology for ....</label>
                                <span class="todo-actions">
                                    <i class="fas fa-check"></i>
                                    <i class="fas fa-edit"></i>
                                    <i class="fas fa-trash"></i>
                                </span>
                            </li>
                            <li>
                                <span class="todo-drag"><i class="fas fa-grip-lines"></i></span>
                                <input type="checkbox" id="todo2-minimal">
                                <label for="todo2-minimal">Creating component page</label>
                                <span class="todo-actions">
                                    <i class="fas fa-check"></i>
                                    <i class="fas fa-edit"></i>
                                    <i class="fas fa-trash"></i>
                                </span>
                            </li>
                            <li class="done">
                                <span class="todo-drag"><i class="fas fa-grip-lines"></i></span>
                                <input type="checkbox" id="todo3-minimal" checked>
                                <label for="todo3-minimal">Follow back those who follow you</label>
                                <span class="todo-actions">
                                    <i class="fas fa-check"></i>
                                    <i class="fas fa-edit"></i>
                                    <i class="fas fa-trash"></i>
                                </span>
                            </li>
                            <li class="done">
                                <span class="todo-drag"><i class="fas fa-grip-lines"></i></span>
                                <input type="checkbox" id="todo4-minimal" checked>
                                <label for="todo4-minimal">Design One page theme</label>
                                <span class="todo-actions">
                                    <i class="fas fa-check"></i>
                                    <i class="fas fa-edit"></i>
                                    <i class="fas fa-trash"></i>
                                </span>
                            </li>
                            <li>
                                <span class="todo-drag"><i class="fas fa-grip-lines"></i></span>
                                <input type="checkbox" id="todo5-minimal">
                                <label for="todo5-minimal">Creating component page</label>
                                <span class="todo-actions">
                                    <i class="fas fa-check"></i>
                                    <i class="fas fa-edit"></i>
                                    <i class="fas fa-trash"></i>
                                </span>
                            </li>
                    </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Подключаем Font Awesome для иконок -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Подключаем Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        let currentMetric = 'profit';
        let currentPeriod = '30';

        // Анимация счетчика для карточек
        function animateCounter(element, start, end, duration = 1500) {
            const startTime = performance.now();
            const originalText = element.textContent;
            const isCurrency = originalText.includes('грн');

            function updateCounter(currentTime) {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);

                // Плавная анимация с easeOutQuart
                const easeProgress = 1 - Math.pow(1 - progress, 4);
                const current = start + (end - start) * easeProgress;

                if (isCurrency) {
                    element.textContent = new Intl.NumberFormat('ru-RU', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }).format(current) + ' грн';
                } else {
                    element.textContent = Math.floor(current).toLocaleString('ru-RU');
                }

                if (progress < 1) {
                    requestAnimationFrame(updateCounter);
                }
            }

            requestAnimationFrame(updateCounter);
        }

        // Запускаем анимацию для всех карточек
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.stat-value');

            cards.forEach((card, index) => {
                const finalValue = card.textContent;
                const numericValue = parseFloat(finalValue.replace(/[^\d.-]/g, ''));
                const isCurrency = finalValue.includes('грн');

                if (!isNaN(numericValue)) {
                    // Сразу устанавливаем 0 и показываем элемент
                    card.textContent = '0' + (isCurrency ? ' грн' : '');
                    card.classList.add('animated');

                    // Запускаем анимацию с небольшой задержкой для каждой карточки
                    setTimeout(() => {
                        animateCounter(card, 0, numericValue, 1500);
                    }, index * 200);
                } else {
                    // Для нечисловых значений просто показываем
                    card.classList.add('animated');
                }
            });
        });

        // Данные для графика (пример)
        const datasets = {
            profit: {
                label: "Прибыль",
                icon: "fa-chart-line",
                data: {
                    7: [18000, 17500, 17000, 16800, 16500, 16200, 16000],
                    30: [10000, 12000, 15000, 14000, 16000, 18000, 17500, 17000, 16800, 16500, 16200, 16000, 15800, 15500, 15300, 15000, 14800, 14500, 14300, 14000, 13800, 13500, 13300, 13000, 12800, 12500, 12300, 12000, 11800, 11500],
                    90: [10000, 10500, 11000, 11500, 12000, 12500, 13000, 13500, 14000, 14500, 15000, 15500, 16000, 16500, 17000, 17500, 18000, 18500, 19000, 19500, 20000, 20500, 21000, 21500, 22000, 22500, 23000, 23500, 24000, 24500, 25000, 25500, 26000, 26500, 27000, 27500, 28000, 28500, 29000, 29500, 30000, 30500, 31000, 31500, 32000, 32500, 33000, 33500, 34000, 34500, 35000, 35500, 36000, 36500, 37000, 37500, 38000, 38500, 39000, 39500, 40000, 40500, 41000, 41500, 42000, 42500, 43000, 43500, 44000, 44500, 45000, 45500, 46000, 46500, 47000, 47500, 48000, 48500, 49000, 49500]
                }
            },
            sales: {
                label: "Продажи товаров",
                icon: "fa-shopping-cart",
                data: {
                    7: [14000, 13500, 13000, 12800, 12500, 12200, 12000],
                    30: [8000, 9000, 11000, 13000, 12500, 14000, 13500, 13000, 12800, 12500, 12200, 12000, 11800, 11500, 11300, 11000, 10800, 10500, 10300, 10000, 9800, 9500, 9300, 9000, 8800, 8500, 8300, 8000, 7800, 7500],
                    90: [8000, 8500, 9000, 9500, 10000, 10500, 11000, 11500, 12000, 12500, 13000, 13500, 14000, 14500, 15000, 15500, 16000, 16500, 17000, 17500, 18000, 18500, 19000, 19500, 20000, 20500, 21000, 21500, 22000, 22500, 23000, 23500, 24000, 24500, 25000, 25500, 26000, 26500, 27000, 27500, 28000, 28500, 29000, 29500, 30000, 30500, 31000, 31500, 32000, 32500, 33000, 33500, 34000, 34500, 35000, 35500, 36000, 36500, 37000, 37500, 38000, 38500, 39000, 39500, 40000, 40500, 41000, 41500, 42000, 42500, 43000, 43500, 44000, 44500, 45000, 45500, 46000, 46500, 47000, 47500]
                }
            },
            services: {
                label: "Продажи услуг",
                icon: "fa-spa",
                data: {
                    7: [4500, 4400, 4300, 4280, 4250, 4220, 4200],
                    30: [3000, 3500, 4000, 3800, 4100, 4500, 4400, 4300, 4280, 4250, 4220, 4200, 4180, 4150, 4130, 4100, 4080, 4050, 4030, 4000, 3980, 3950, 3930, 3900, 3880, 3850, 3830, 3800, 3780, 3750],
                    90: [3000, 3200, 3400, 3600, 3800, 4000, 4200, 4400, 4600, 4800, 5000, 5200, 5400, 5600, 5800, 6000, 6200, 6400, 6600, 6800, 7000, 7200, 7400, 7600, 7800, 8000, 8200, 8400, 8600, 8800, 9000, 9200, 9400, 9600, 9800, 10000, 10200, 10400, 10600, 10800, 11000, 11200, 11400, 11600, 11800, 12000, 12200, 12400, 12600, 12800, 13000, 13200, 13400, 13600, 13800, 14000, 14200, 14400, 14600, 14800, 15000, 15200, 15400, 15600, 15800, 16000, 16200, 16400, 16600, 16800, 17000, 17200, 17400, 17600, 17800, 18000, 18200, 18400, 18600, 18800]
                }
            },
            expenses: {
                label: "Расходы",
                icon: "fa-credit-card",
                data: {
                    7: [2900, 2850, 2800, 2780, 2750, 2720, 2700],
                    30: [2000, 2500, 3000, 2800, 2700, 2900, 2850, 2800, 2780, 2750, 2720, 2700, 2680, 2650, 2630, 2600, 2580, 2550, 2530, 2500, 2480, 2450, 2430, 2400, 2380, 2350, 2330, 2300, 2280, 2250],
                    90: [2000, 2100, 2200, 2300, 2400, 2500, 2600, 2700, 2800, 2900, 3000, 3100, 3200, 3300, 3400, 3500, 3600, 3700, 3800, 3900, 4000, 4100, 4200, 4300, 4400, 4500, 4600, 4700, 4800, 4900, 5000, 5100, 5200, 5300, 5400, 5500, 5600, 5700, 5800, 5900, 6000, 6100, 6200, 6300, 6400, 6500, 6600, 6700, 6800, 6900, 7000, 7100, 7200, 7300, 7400, 7500, 7600, 7700, 7800, 7900, 8000, 8100, 8200, 8300, 8400, 8500, 8600, 8700, 8800, 8900, 9000, 9100, 9200, 9300, 9400, 9500, 9600, 9700, 9800, 9900]
                }
            },
            clients: {
                label: "Клиенты",
                icon: "fa-users",
                data: {
                    7: [90, 88, 85, 83, 80, 78, 75],
                    30: [50, 60, 65, 80, 75, 90, 88, 85, 83, 80, 78, 75, 73, 70, 68, 65, 63, 60, 58, 55, 53, 50, 48, 45, 43, 40, 38, 35, 33, 30],
                    90: [50, 52, 54, 56, 58, 60, 62, 64, 66, 68, 70, 72, 74, 76, 78, 80, 82, 84, 86, 88, 90, 92, 94, 96, 98, 100, 102, 104, 106, 108, 110, 112, 114, 116, 118, 120, 122, 124, 126, 128, 130, 132, 134, 136, 138, 140, 142, 144, 146, 148, 150, 152, 154, 156, 158, 160, 162, 164, 166, 168, 170, 172, 174, 176, 178, 180, 182, 184, 186, 188, 190, 192, 194, 196, 198, 200, 202, 204, 206, 208]
                }
            },
            appointments: {
                label: "Записи",
                icon: "fa-calendar-check",
                data: {
                    7: [35, 34, 33, 32, 31, 30, 28],
                    30: [20, 25, 22, 30, 28, 35, 34, 33, 32, 31, 30, 28, 27, 26, 25, 24, 23, 22, 21, 20, 19, 18, 17, 16, 15, 14, 13, 12, 11, 10],
                    90: [20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99]
                }
            }
        };
        // Формируем activity только после определения всех метрик
        datasets.activity = {
            label: 'Активность',
            icon: 'fa-bolt',
            data: {
                clients: datasets.clients ? datasets.clients.data : {},
                appointments: datasets.appointments ? datasets.appointments.data : {},
                services: datasets.services ? datasets.services.data : {}
            }
        };
        // Лейблы для разных периодов
        const periodLabels = {
            7: ['6 дн', '5 дн', '4 дн', '3 дн', '2 дн', 'Вчера', 'Сегодня'],
            30: Array.from({length: 30}, (_, i) => `${30-i} дн назад`).reverse(),
            90: Array.from({length: 90}, (_, i) => `${90-i} дн назад`).reverse()
        };
        function getChartLabels(period) {
            if (period === '7') return getLastNDates(7);
            if (period === '30') return getWeekStartDates(4);
            if (period === '90') return getWeekStartDates(13);
            return [];
        }
        function getActivityDatasets(period) {
            return [
                {
                    label: 'Клиенты',
                    data: datasets.activity.data.clients[period],
                    borderColor: '#8b5cf6',
                    backgroundColor: '#8b5cf6' + '33',
                    tension: 0.4,
                    fill: false,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    pointHitRadius: 12,
                    spanGaps: true
                },
                {
                    label: 'Записи',
                    data: datasets.activity.data.appointments[period],
                    borderColor: '#f59e0b',
                    backgroundColor: '#f59e0b' + '33',
                    tension: 0.4,
                    fill: false,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    pointHitRadius: 12,
                    spanGaps: true
                },
                {
                    label: 'Продажи услуг',
                    data: datasets.activity.data.services[period],
                    borderColor: '#8b5cf6',
                    backgroundColor: '#8b5cf6' + '33',
                    tension: 0.4,
                    fill: false,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    pointHitRadius: 12,
                    spanGaps: true
                }
            ];
        }
        const ctx = document.getElementById('universalChart').getContext('2d');
        let universalChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: getLastNDates(7),
                datasets: [{
                    label: datasets[currentMetric].label,
                    data: currentMetric === 'profit' ? [] : datasets[currentMetric].data['7'],
                    borderColor: getMetricColor(currentMetric),
                    backgroundColor: getMetricColor(currentMetric) + '33',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    pointHitRadius: 12,
                    spanGaps: true
                }]
            },
            options: {
                responsive: true,
                animation: true,
                plugins: {
                    legend: { display: true },
                    tooltip: { mode: 'index', intersect: false },
                    decimation: {
                        enabled: true,
                        algorithm: 'min-max'
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            callback: function(value, index, ticks) {
                                if (currentPeriod === '180' || currentPeriod === '365') {
                                    return getMonthLabels(this.getLabels().length)[index];
                                }
                                if (currentPeriod === '7') return this.getLabelForValue(this.getLabels()[index]);
                                const date = new Date();
                                date.setDate(date.getDate() - (this.getLabels().length - 1 - index));
                                if (date.getDay() === 1) {
                                    return this.getLabelForValue(this.getLabels()[index]);
                                }
                                return '';
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        max: undefined
                    }
                }
            }
        });
        // После инициализации universalChart
        // Автоматически загружаем данные для прибыли за месяц при загрузке страницы
        fetch('/api/dashboard/profit-chart?period=30')
            .then(res => res.json())
            .then(res => {
                universalChart.data.labels = res.labels;
                universalChart.data.datasets = [{
                    label: 'Прибыль',
                    data: getCumulativeData(res.data),
                    borderColor: getMetricColor('profit'),
                    backgroundColor: getMetricColor('profit') + '33',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    pointHitRadius: 12,
                    spanGaps: true
                }];
                const maxValue = Math.max(...getCumulativeData(res.data));
                universalChart.options.scales.y.max = maxValue > 0 ? Math.ceil(maxValue * 1.15) : undefined;
                universalChart.update();
            });
        // Dropdown логика
        const metricToggle = document.querySelector('.metric-toggle');
        const metricMenu = document.querySelector('.metric-menu');
        const metricDropdown = document.querySelector('.metric-dropdown');
        const selectedMetricLabel = document.getElementById('selectedMetricLabel');
        metricToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            metricDropdown.classList.toggle('open');
        });
        document.addEventListener('click', function() {
            metricDropdown.classList.remove('open');
        });
        document.querySelectorAll('.metric-item').forEach(item => {
            item.addEventListener('click', function() {
                const type = this.dataset.type;
                currentMetric = type;
                selectedMetricLabel.textContent = datasets[type].label;
                metricToggle.querySelector('i').className = 'fas ' + datasets[type].icon;

                if (type === 'profit') {
                    fetch(`/api/dashboard/profit-chart?period=${currentPeriod}`)
                        .then(res => res.json())
                        .then(res => {
                            universalChart.data.labels = res.labels;
                            universalChart.data.datasets = [{
                                label: 'Прибыль',
                                data: getCumulativeData(res.data),
                                borderColor: getMetricColor('profit'),
                                backgroundColor: getMetricColor('profit') + '33',
                                tension: 0.4,
                                fill: true,
                                pointRadius: 0,
                                pointHoverRadius: 6,
                                pointHitRadius: 12,
                                spanGaps: true
                            }];
                            const maxValue = Math.max(...getCumulativeData(res.data));
                            universalChart.options.scales.y.max = maxValue > 0 ? Math.ceil(maxValue * 1.15) : undefined;
                            universalChart.update();
                        });
                    return;
                }
                if (type === 'expenses') {
                    // Получаем реальные расходы по API
                    fetch(`/api/dashboard/expenses-chart?period=${currentPeriod}`)
                        .then(res => res.json())
                        .then(res => {
                            // Делаем накопительную линию
                            const data = getCumulativeData(res.data);
                            let labels = res.labels;
                            universalChart.data.labels = labels;
                            universalChart.data.datasets = [{
                                label: datasets['expenses'].label,
                                data: data,
                                borderColor: getMetricColor('expenses'),
                                backgroundColor: getMetricColor('expenses') + '33',
                                tension: 0.4,
                                fill: true,
                                pointRadius: 0,
                                pointHoverRadius: 6,
                                pointHitRadius: 12,
                                spanGaps: true
                            }];
                            const maxValue = Math.max(...data);
                            universalChart.options.scales.y.max = maxValue > 0 ? Math.ceil(maxValue * 1.15) : undefined;
                            universalChart.update();
                        });
                    return;
                }
                if (type === 'sales') {
                    // Обработка "Продажи товаров" по образцу "Продажи услуг"
                    fetch(`/api/dashboard/sales-chart?period=${currentPeriod}`)
                        .then(res => res.json())
                        .then(res => {
                            if (currentPeriod === '7') {
                                universalChart.data.labels = res.labels;
                                universalChart.data.datasets = [{
                                    label: 'Продажи товаров',
                                    data: res.data,
                                    borderColor: getMetricColor('sales'),
                                    backgroundColor: getMetricColor('sales') + '33',
                                    tension: 0.4,
                                    fill: true,
                                    pointRadius: 0,
                                    pointHoverRadius: 6,
                                    pointHitRadius: 12,
                                    spanGaps: true
                                }];
                                universalChart.options.scales.x.ticks.callback = function(value, index, ticks) {
                                    return this.getLabelForValue(this.getLabels()[index]);
                                };
                            } else if (currentPeriod === '30' || currentPeriod === '90') {
                                universalChart.data.labels = res.labels;
                                universalChart.data.datasets = [{
                                    label: 'Продажи товаров',
                                    data: res.data,
                                    borderColor: getMetricColor('sales'),
                                    backgroundColor: getMetricColor('sales') + '33',
                                    tension: 0.4,
                                    fill: true,
                                    pointRadius: 0,
                                    pointHoverRadius: 6,
                                    pointHitRadius: 12,
                                    spanGaps: true
                                }];
                                universalChart.options.scales.x.ticks.callback = function(value, index, ticks) {
                                    const label = this.getLabelForValue(this.getLabels()[index]);
                                    const parts = label.split(' ');
                                    if (parts.length === 2) {
                                        const day = parseInt(parts[0]);
                                        const month = parts[1];
                                        const date = new Date();
                                        date.setDate(day);
                                        const now = new Date();
                                        const d = new Date(now);
                                        d.setDate(now.getDate() - (this.getLabels().length - 1 - index));
                                        if (d.getDay() === 1) {
                                            return label;
                                        }
                                    }
                                    return '';
                                };
                            } else {
                                universalChart.data.labels = res.labels;
                                universalChart.data.datasets = [{
                                    label: 'Продажи товаров',
                                    data: res.data,
                                    borderColor: getMetricColor('sales'),
                                    backgroundColor: getMetricColor('sales') + '33',
                                    tension: 0.4,
                                    fill: true,
                                    pointRadius: 0,
                                    pointHoverRadius: 6,
                                    pointHitRadius: 12,
                                    spanGaps: true
                                }];
                                universalChart.options.scales.x.ticks.callback = function(value, index, ticks) {
                                    return '';
                                };
                            }
                            // Устанавливаем максимум оси Y на 15% выше максимального значения
                            const maxValue = Math.max(...res.data);
                            universalChart.options.scales.y.max = maxValue > 0 ? Math.ceil(maxValue * 1.15) : undefined;
                            universalChart.update();
                            // Обновление и анимация для карточки "Продажи товаров" при смене периода
                            const salesCard = document.querySelector('.stat-card.sales-card .stat-value');
                            if (salesCard && Array.isArray(res.data)) {
                                const total = res.data.reduce((sum, v) => sum + (parseFloat(v) || 0), 0);
                                salesCard.classList.remove('animated');
                                salesCard.textContent = '0 грн';
                                void salesCard.offsetWidth; // reflow для сброса transition
                                salesCard.classList.add('animated');
                                setTimeout(() => {
                                    animateCounter(salesCard, 0, total, 1500);
                                }, 100);
                            }
                        });
                    return;
                }
                if (type === 'services') {
                    fetch(`/api/dashboard/services-chart?period=${currentPeriod}`)
                        .then(res => res.json())
                        .then(res => {
                            if (currentPeriod === '7') {
                                universalChart.data.labels = res.labels;
                                universalChart.data.datasets = [{
                                    label: datasets['services'].label,
                                    data: res.data,
                                    borderColor: getMetricColor('services'),
                                    backgroundColor: getMetricColor('services') + '33',
                                    tension: 0.4,
                                    fill: true,
                                    pointRadius: 0,
                                    pointHoverRadius: 6,
                                    pointHitRadius: 12,
                                    spanGaps: true
                                }];
                                universalChart.options.scales.x.ticks.callback = function(value, index, ticks) {
                                    return this.getLabelForValue(this.getLabels()[index]);
                                };
                            } else if (currentPeriod === '30' || currentPeriod === '90') {
                                universalChart.data.labels = res.labels;
                                universalChart.data.datasets = [{
                                    label: datasets['services'].label,
                                    data: res.data,
                                    borderColor: getMetricColor('services'),
                                    backgroundColor: getMetricColor('services') + '33',
                                    tension: 0.4,
                                    fill: true,
                                    pointRadius: 0,
                                    pointHoverRadius: 6,
                                    pointHitRadius: 12,
                                    spanGaps: true
                                }];
                                universalChart.options.scales.x.ticks.callback = function(value, index, ticks) {
                                    const label = this.getLabelForValue(this.getLabels()[index]);
                                    const parts = label.split(' ');
                                    if (parts.length === 2) {
                                        const day = parseInt(parts[0]);
                                        const month = parts[1];
                                        const date = new Date();
                                        date.setDate(day);
                                        const now = new Date();
                                        const d = new Date(now);
                                        d.setDate(now.getDate() - (this.getLabels().length - 1 - index));
                                        if (d.getDay() === 1) {
                                            return label;
                                        }
                                    }
                                    return '';
                                };
                            }
                            // Устанавливаем максимум оси Y на 15% выше максимального значения
                            const maxValue = Math.max(...res.data);
                            universalChart.options.scales.y.max = maxValue > 0 ? Math.ceil(maxValue * 1.15) : undefined;
                            universalChart.update();
                        });
                    return;
                }
                if (type === 'activity') {
                    fetch(`/api/dashboard/activity-chart?period=${currentPeriod}`)
                        .then(res => res.json())
                        .then(res => {
                            universalChart.data.labels = res.labels;
                            universalChart.data.datasets = [
                                {
                                    label: 'Услуги',
                                    data: res.services,
                                    borderColor: '#8b5cf6',
                                    backgroundColor: '#8b5cf6' + '33',
                                    tension: 0.4,
                                    fill: false,
                                    pointRadius: 0,
                                    pointHoverRadius: 6,
                                    pointHitRadius: 12,
                                    spanGaps: true
                                },
                                {
                                    label: 'Клиенты',
                                    data: res.clients,
                                    borderColor: '#3b82f6',
                                    backgroundColor: '#3b82f6' + '33',
                                    tension: 0.4,
                                    fill: false,
                                    pointRadius: 0,
                                    pointHoverRadius: 6,
                                    pointHitRadius: 12,
                                    spanGaps: true
                                },
                                {
                                    label: 'Записи',
                                    data: res.appointments,
                                    borderColor: '#f59e0b',
                                    backgroundColor: '#f59e0b' + '33',
                                    tension: 0.4,
                                    fill: false,
                                    pointRadius: 0,
                                    pointHoverRadius: 6,
                                    pointHitRadius: 12,
                                    spanGaps: true
                                }
                            ];
                            // Y max по максимальному из всех
                            const maxValue = Math.max(...res.services, ...res.clients, ...res.appointments);
                            universalChart.options.scales.y.max = maxValue > 0 ? Math.ceil(maxValue * 1.15) : undefined;
                            universalChart.update();
                        });
                    return;
                }
            });
        });
        // Фильтры периода
        // В обработчике смены периода (period-btn) замените текущий код на этот:
        // В обработчике смены периода (period-btn) замените текущий код на этот:
        document.querySelectorAll('.period-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.period-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                currentPeriod = this.dataset.period;

                // Обновляем данные для текущей метрики
                if (currentMetric === 'profit') {
                    fetch(`/api/dashboard/profit-chart?period=${currentPeriod}`)
                        .then(res => res.json())
                        .then(res => {
                            universalChart.data.labels = res.labels;
                            universalChart.data.datasets = [{
                                label: 'Прибыль',
                                data: getCumulativeData(res.data),
                                borderColor: getMetricColor('profit'),
                                backgroundColor: getMetricColor('profit') + '33',
                                tension: 0.4,
                                fill: true,
                                pointRadius: 0,
                                pointHoverRadius: 6,
                                pointHitRadius: 12,
                                spanGaps: true
                            }];
                            const maxValue = Math.max(...getCumulativeData(res.data));
                            universalChart.options.scales.y.max = maxValue > 0 ? Math.ceil(maxValue * 1.15) : undefined;
                            universalChart.update();

                            // Анимация для карточки "Прибыль"
                            const profitCard = document.querySelector('.stat-card.profit-card .stat-value');
                            if (profitCard && Array.isArray(res.data)) {
                                const total = res.data.reduce((sum, v) => sum + (parseFloat(v) || 0), 0);
                                profitCard.classList.remove('animated');
                                profitCard.textContent = '0 грн';
                                void profitCard.offsetWidth;
                                profitCard.classList.add('animated');
                                setTimeout(() => {
                                    animateCounter(profitCard, 0, total, 1500);
                                }, 100);
                            }
                        });
                    return;
                }

                if (currentMetric === 'expenses') {
                    fetch(`/api/dashboard/expenses-chart?period=${currentPeriod}`)
                        .then(res => res.json())
                        .then(res => {
                            const data = getCumulativeData(res.data);
                            universalChart.data.labels = res.labels;
                            universalChart.data.datasets = [{
                                label: datasets['expenses'].label,
                                data: data,
                                borderColor: getMetricColor('expenses'),
                                backgroundColor: getMetricColor('expenses') + '33',
                                tension: 0.4,
                                fill: true,
                                pointRadius: 0,
                                pointHoverRadius: 6,
                                pointHitRadius: 12,
                                spanGaps: true
                            }];
                            const maxValue = Math.max(...data);
                            universalChart.options.scales.y.max = maxValue > 0 ? Math.ceil(maxValue * 1.15) : undefined;
                            universalChart.update();

                            // Анимация для карточки "Расходы"
                            const expensesCard = document.querySelector('.stat-card.expenses-card .stat-value');
                            if (expensesCard && Array.isArray(res.data)) {
                                const total = res.data.reduce((sum, v) => sum + (parseFloat(v) || 0), 0);
                                expensesCard.classList.remove('animated');
                                expensesCard.textContent = '0 грн';
                                void expensesCard.offsetWidth;
                                expensesCard.classList.add('animated');
                                setTimeout(() => {
                                    animateCounter(expensesCard, 0, total, 1500);
                                }, 100);
                            }
                        });
                    return;
                }

                if (currentMetric === 'sales') {
                    fetch(`/api/dashboard/sales-chart?period=${currentPeriod}`)
                        .then(res => res.json())
                        .then(res => {
                            if (currentPeriod === '7') {
                                universalChart.data.labels = res.labels;
                                universalChart.data.datasets = [{
                                    label: 'Продажи товаров',
                                    data: res.data,
                                    borderColor: getMetricColor('sales'),
                                    backgroundColor: getMetricColor('sales') + '33',
                                    tension: 0.4,
                                    fill: true,
                                    pointRadius: 0,
                                    pointHoverRadius: 6,
                                    pointHitRadius: 12,
                                    spanGaps: true
                                }];
                                universalChart.options.scales.x.ticks.callback = function(value, index, ticks) {
                                    return this.getLabelForValue(this.getLabels()[index]);
                                };
                            } else if (currentPeriod === '30' || currentPeriod === '90') {
                                universalChart.data.labels = res.labels;
                                universalChart.data.datasets = [{
                                    label: 'Продажи товаров',
                                    data: res.data,
                                    borderColor: getMetricColor('sales'),
                                    backgroundColor: getMetricColor('sales') + '33',
                                    tension: 0.4,
                                    fill: true,
                                    pointRadius: 0,
                                    pointHoverRadius: 6,
                                    pointHitRadius: 12,
                                    spanGaps: true
                                }];
                                universalChart.options.scales.x.ticks.callback = function(value, index, ticks) {
                                    const label = this.getLabelForValue(this.getLabels()[index]);
                                    const parts = label.split(' ');
                                    if (parts.length === 2) {
                                        const day = parseInt(parts[0]);
                                        const month = parts[1];
                                        const date = new Date();
                                        date.setDate(day);
                                        const now = new Date();
                                        const d = new Date(now);
                                        d.setDate(now.getDate() - (this.getLabels().length - 1 - index));
                                        if (d.getDay() === 1) {
                                            return label;
                                        }
                                    }
                                    return '';
                                };
                            } else {
                                // Обработка для 6 месяцев и года
                                universalChart.data.labels = res.labels;
                                universalChart.data.datasets = [{
                                    label: 'Продажи товаров',
                                    data: res.data,
                                    borderColor: getMetricColor('sales'),
                                    backgroundColor: getMetricColor('sales') + '33',
                                    tension: 0.4,
                                    fill: true,
                                    pointRadius: 0,
                                    pointHoverRadius: 6,
                                    pointHitRadius: 12,
                                    spanGaps: true
                                }];
                                universalChart.options.scales.x.ticks.callback = function(value, index, ticks) {
                                    const label = this.getLabelForValue(this.getLabels()[index]);
                                    const parts = label.split(' ');
                                    if (parts.length === 2) {
                                        const day = parseInt(parts[0]);
                                        const month = parts[1];
                                        const date = new Date();
                                        date.setDate(day);
                                        const now = new Date();
                                        const d = new Date(now);
                                        d.setDate(now.getDate() - (this.getLabels().length - 1 - index));
                                        if (d.getDate() === 1) {
                                            return month;
                                        }
                                    }
                                    return '';
                                };
                            }
                            const maxValue = Math.max(...res.data);
                            universalChart.options.scales.y.max = maxValue > 0 ? Math.ceil(maxValue * 1.15) : undefined;
                            universalChart.update();

                            // Анимация для карточки "Продажи товаров"
                            const salesCard = document.querySelector('.stat-card.sales-card .stat-value');
                            if (salesCard && Array.isArray(res.data)) {
                                const total = res.data.reduce((sum, v) => sum + (parseFloat(v) || 0), 0);
                                salesCard.classList.remove('animated');
                                salesCard.textContent = '0 грн';
                                void salesCard.offsetWidth;
                                salesCard.classList.add('animated');
                                setTimeout(() => {
                                    animateCounter(salesCard, 0, total, 1500);
                                }, 100);
                            }
                        });
                    return;
                }

                if (currentMetric === 'services') {
                    fetch(`/api/dashboard/services-chart?period=${currentPeriod}`)
                        .then(res => res.json())
                        .then(res => {
                            if (currentPeriod === '7') {
                                universalChart.data.labels = res.labels;
                                universalChart.data.datasets = [{
                                    label: datasets['services'].label,
                                    data: res.data,
                                    borderColor: getMetricColor('services'),
                                    backgroundColor: getMetricColor('services') + '33',
                                    tension: 0.4,
                                    fill: true,
                                    pointRadius: 0,
                                    pointHoverRadius: 6,
                                    pointHitRadius: 12,
                                    spanGaps: true
                                }];
                                universalChart.options.scales.x.ticks.callback = function(value, index, ticks) {
                                    return this.getLabelForValue(this.getLabels()[index]);
                                };
                            } else if (currentPeriod === '30' || currentPeriod === '90') {
                                universalChart.data.labels = res.labels;
                                universalChart.data.datasets = [{
                                    label: datasets['services'].label,
                                    data: res.data,
                                    borderColor: getMetricColor('services'),
                                    backgroundColor: getMetricColor('services') + '33',
                                    tension: 0.4,
                                    fill: true,
                                    pointRadius: 0,
                                    pointHoverRadius: 6,
                                    pointHitRadius: 12,
                                    spanGaps: true
                                }];
                                universalChart.options.scales.x.ticks.callback = function(value, index, ticks) {
                                    const label = this.getLabelForValue(this.getLabels()[index]);
                                    const parts = label.split(' ');
                                    if (parts.length === 2) {
                                        const day = parseInt(parts[0]);
                                        const month = parts[1];
                                        const date = new Date();
                                        date.setDate(day);
                                        const now = new Date();
                                        const d = new Date(now);
                                        d.setDate(now.getDate() - (this.getLabels().length - 1 - index));
                                        if (d.getDate() === 1) {
                                            return month;
                                        }
                                    }
                                    return '';
                                };
                            } else {
                                // Обработка для 6 месяцев и года
                                universalChart.data.labels = res.labels;
                                universalChart.data.datasets = [{
                                    label: datasets['services'].label,
                                    data: res.data,
                                    borderColor: getMetricColor('services'),
                                    backgroundColor: getMetricColor('services') + '33',
                                    tension: 0.4,
                                    fill: true,
                                    pointRadius: 0,
                                    pointHoverRadius: 6,
                                    pointHitRadius: 12,
                                    spanGaps: true
                                }];
                                universalChart.options.scales.x.ticks.callback = function(value, index, ticks) {
                                    const label = this.getLabelForValue(this.getLabels()[index]);
                                    const parts = label.split(' ');
                                    if (parts.length === 2) {
                                        const day = parseInt(parts[0]);
                                        const month = parts[1];
                                        const date = new Date();
                                        date.setDate(day);
                                        const now = new Date();
                                        const d = new Date(now);
                                        d.setDate(now.getDate() - (this.getLabels().length - 1 - index));
                                        if (d.getDate() === 1) {
                                            return month;
                                        }
                                    }
                                    return '';
                                };
                            }
                            const maxValue = Math.max(...res.data);
                            universalChart.options.scales.y.max = maxValue > 0 ? Math.ceil(maxValue * 1.15) : undefined;
                            universalChart.update();

                            // Анимация для карточки "Продажи услуг"
                            const servicesCard = document.querySelector('.stat-card.services-card .stat-value');
                            if (servicesCard && Array.isArray(res.data)) {
                                const total = res.data.reduce((sum, v) => sum + (parseFloat(v) || 0), 0);
                                servicesCard.classList.remove('animated');
                                servicesCard.textContent = '0 грн';
                                void servicesCard.offsetWidth;
                                servicesCard.classList.add('animated');
                                setTimeout(() => {
                                    animateCounter(servicesCard, 0, total, 1500);
                                }, 100);
                            }
                        });
                    return;
                }

                if (currentMetric === 'activity') {
                    fetch(`/api/dashboard/activity-chart?period=${currentPeriod}`)
                        .then(res => res.json())
                        .then(res => {
                            universalChart.data.labels = res.labels;
                            universalChart.data.datasets = [
                                {
                                    label: 'Услуги',
                                    data: res.services,
                                    borderColor: '#8b5cf6',
                                    backgroundColor: '#8b5cf6' + '33',
                                    tension: 0.4,
                                    fill: false,
                                    pointRadius: 0,
                                    pointHoverRadius: 6,
                                    pointHitRadius: 12,
                                    spanGaps: true
                                },
                                {
                                    label: 'Клиенты',
                                    data: res.clients,
                                    borderColor: '#3b82f6',
                                    backgroundColor: '#3b82f6' + '33',
                                    tension: 0.4,
                                    fill: false,
                                    pointRadius: 0,
                                    pointHoverRadius: 6,
                                    pointHitRadius: 12,
                                    spanGaps: true
                                },
                                {
                                    label: 'Записи',
                                    data: res.appointments,
                                    borderColor: '#f59e0b',
                                    backgroundColor: '#f59e0b' + '33',
                                    tension: 0.4,
                                    fill: false,
                                    pointRadius: 0,
                                    pointHoverRadius: 6,
                                    pointHitRadius: 12,
                                    spanGaps: true
                                }
                            ];
                            const maxValue = Math.max(...res.services, ...res.clients, ...res.appointments);
                            universalChart.options.scales.y.max = maxValue > 0 ? Math.ceil(maxValue * 1.15) : undefined;
                            universalChart.update();

                            // Анимация для карточек активности
                            const clientsCard = document.querySelector('.stat-card.clients-card .stat-value');
                            const appointmentsCard = document.querySelector('.stat-card.appointments-card .stat-value');
                            const proceduresCard = document.querySelector('.stat-card.procedures-card .stat-value');

                            // Клиенты
                            if (clientsCard && Array.isArray(res.clients)) {
                                const total = res.clients.reduce((sum, v) => sum + (parseFloat(v) || 0), 0);
                                clientsCard.classList.remove('animated');
                                clientsCard.textContent = '0';
                                void clientsCard.offsetWidth;
                                clientsCard.classList.add('animated');
                                setTimeout(() => {
                                    animateCounter(clientsCard, 0, total, 1500);
                                }, 100);
                            }

                            // Записи
                            if (appointmentsCard && Array.isArray(res.appointments)) {
                                const total = res.appointments.reduce((sum, v) => sum + (parseFloat(v) || 0), 0);
                                appointmentsCard.classList.remove('animated');
                                appointmentsCard.textContent = '0';
                                void appointmentsCard.offsetWidth;
                                appointmentsCard.classList.add('animated');
                                setTimeout(() => {
                                    animateCounter(appointmentsCard, 0, total, 1500);
                                }, 100);
                            }

                            // Услуги (для активности)
                            if (proceduresCard && Array.isArray(res.services)) {
                                const total = res.services.reduce((sum, v) => sum + (parseFloat(v) || 0), 0);
                                proceduresCard.classList.remove('animated');
                                proceduresCard.textContent = '0';
                                void proceduresCard.offsetWidth;
                                proceduresCard.classList.add('animated');
                                setTimeout(() => {
                                    animateCounter(proceduresCard, 0, total, 1500);
                                }, 100);
                            }
                        });
                    return;
                }
            });
        });
        // Цвета для метрик
        function getMetricColor(type) {
            const colors = {
                profit: '#10b981', // зелёный
                sales: '#3b82f6', // синий
                services: '#8b5cf6', // фиолетовый
                expenses: '#ef4444', // красный
                clients: '#8b5cf6', // фиолетовый
                appointments: '#f59e0b' // оранжевый
            };
            return colors[type] || '#10b981';
        }
        // ВОССТАНАВЛИВАЮ переключение вкладок Финансы/Активность
        document.querySelectorAll('.dashboard-tabs .tab-button').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.dashboard-tabs .tab-button').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                const tab = this.getAttribute('data-tab');
                document.querySelectorAll('.stat-cards-group').forEach(group => group.classList.remove('active'));
                const target = document.querySelector(`.stat-cards-group.${tab}-group`);
                if(target) target.classList.add('active');
            });
        });

        function getLastNDates(n) {
            const arr = [];
            const now = new Date();
            for (let i = n - 1; i >= 0; i--) {
                const d = new Date(now);
                d.setDate(now.getDate() - i);
                arr.push(d.toLocaleDateString('ru-RU', { day: 'numeric', month: 'short' }).replace('.', ''));
            }
            return arr;
        }

        function getWeekStartDates(weeks) {
            const arr = [];
            const now = new Date();
            let monday = new Date(now);
            monday.setDate(now.getDate() - ((now.getDay() + 6) % 7));
            for (let i = weeks - 1; i >= 0; i--) {
                const d = new Date(monday);
                d.setDate(monday.getDate() - i * 7);
                arr.push(d.toLocaleDateString('ru-RU', { day: 'numeric', month: 'short' }).replace('.', ''));
            }
            return arr;
        }

        // Функция для получения накопительных данных
        function getCumulativeData(arr) {
            let result = [];
            let sum = 0;
            for (let i = 0; i < arr.length; i++) {
                sum += arr[i];
                result.push(Number(sum.toFixed(2)));
            }
            return result;
        }

        // По умолчанию активна кнопка 'За месяц'
        document.querySelectorAll('.period-btn').forEach(btn => {
            btn.classList.remove('active');
            if (btn.getAttribute('data-period') === '30') btn.classList.add('active');
        });

        // Добавляю функцию для генерации подписей месяцев
        function getMonthLabels(n) {
            const arr = [];
            const now = new Date();
            let prevMonth = null;
            for (let i = n - 1; i >= 0; i--) {
                const d = new Date(now);
                d.setDate(now.getDate() - i);
                const month = d.toLocaleDateString('ru-RU', { month: 'short' });
                if (prevMonth !== month) {
                    arr.push(month.charAt(0).toUpperCase() + month.slice(1));
                    prevMonth = month;
                } else {
                    arr.push('');
                }
            }
            return arr;
        }

        // === Календарь дашборда: подсветка сегодняшней даты, количество записей, popover и модалка ===
        // [УДАЛЕНО: старая реализация кастомного календаря на .calendar-day]
    </script>

    <style>
        /* Основные стили */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f7fa;
            color: #333;
        }

        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .dashboard-title {
            font-size: 28px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 30px;
        }

        /* Сетка статистических карточек */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .stat-title {
            font-size: 16px;
            font-weight: 600;
            color: #4a5568;
            margin: 0 0 10px 0;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 700;
            margin: 0 0 5px 0;
        }

        .stat-value.blue {
            color: #3b82f6;
        }

        .stat-value.purple {
            color: #8b5cf6;
        }

        .stat-value.green {
            color: #10b981;
        }

        .stat-change {
            font-size: 14px;
            margin: 0;
        }

        .stat-change.positive {
            color: #10b981;
        }

        .stat-change.negative {
            color: #ef4444;
        }

        /* Сетка графиков */
        .chart-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0px 0px 10px 1px rgba(0, 0, 0, 0.3);
            padding: 20px;
        }

        .chart-title {
            font-size: 20px;
            font-weight: 600;
            color: #2d3748;
            margin: 0 0 20px 0;
        }

        /* Адаптивность */
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .chart-container {
                padding: 10px;
            }

            .dashboard-title {
                font-size: 24px;
            }
        }
    </style>

    <style>
    .dashboard-widgets-grid-2x2 {
        display: grid;
        grid-template-columns: 34% 64%;
        gap: 1.6rem;
        margin: 32px 0 0 0;
    }
    .widget-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.3);
        padding: 1.1rem 1.1rem 1.1rem 1.1rem;
        display: flex;
        flex-direction: column;
        min-width: 0;
        min-height: 260px;
        position: relative;
        transition: none;
    }
    /* Убираем hover-эффекты */
    .widget-card:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,0.3);
        background: #fff;
        transform: none;
    }
    /* Внутренние блоки таблицы выравниваем */
    .appointments-table-block {
        background: transparent;
        border-radius: 10px;
        box-shadow: none;
        padding: 0;
        margin-top: 0;
        overflow-x: auto;
    }
    .appointments-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 1rem;
        color: #374151;
        background: transparent;
    }
    .widget-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.7rem;
        margin-bottom: 0.7rem;
        color: #fff;
    }
    .widget-icon.calendar { background: linear-gradient(135deg, #3b82f6, #60a5fa); }
    .widget-icon.todo { background: linear-gradient(135deg, #f59e0b, #fbbf24); }
    .widget-icon.rating { background: linear-gradient(135deg, #10b981, #34d399); }
    .widget-icon.notifications { background: linear-gradient(135deg, #ef4444, #f87171); }
    .widget-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 0.7rem;
    }
    .widget-content {
        flex: 1 1 auto;
        display: flex;
        flex-direction: column;
    }
    /* Аватарки */
    .avatar {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: #e0e7ff;
        color: #3b82f6;
        font-weight: 700;
        font-size: 0.95rem;
        margin-right: 0.5rem;
        box-shadow: 0 2px 8px rgba(59,130,246,0.07);
    }
    /* Бейджи */
    .badge {
        display: inline-block;
        padding: 0.15em 0.7em;
        border-radius: 8px;
        font-size: 0.85em;
        font-weight: 600;
        margin-left: 0.5em;
        vertical-align: middle;
        color: #fff;
    }
    .badge-warning { background: #f59e0b; }
    .badge-info { background: #3b82f6; }
    .badge-success { background: #10b981; }
    .badge-danger { background: #ef4444; }
    .badge-default { background: #64748b; }
    /* ToDo и уведомления */
    .todo-list, .notifications-list {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 0.7rem;
    }
    .todo-list li, .notifications-list li {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        font-size: 1rem;
        color: #374151;
        background: none;
        border-radius: 8px;
        padding: 0.2rem 0.2rem;
        transition: background 0.18s;
    }
    .todo-list li:hover, .notifications-list li:hover {
        background: #f3f4f6;
    }
    .todo-date {
        background: #f3f4f6;
        color: #64748b;
        border-radius: 6px;
        padding: 0.1rem 0.5rem;
        font-size: 0.85rem;
        margin-left: 0.7rem;
    }
    .notif-time {
        color: #a0aec0;
        font-size: 0.85em;
        margin-left: auto;
        font-weight: 500;
    }
    /* Прогресс-бары и тренды */
    .rating-section {
        margin-top: 0.7rem;
    }
    .rating-title {
        font-size: 0.95rem;
        font-weight: 600;
        color: #64748b;
        margin-top: 0.5rem;
        margin-bottom: 0.2rem;
    }
    .rating-list {
        display: flex;
        flex-direction: column;
        gap: 0.4rem;
    }
    .rating-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.98rem;
    }
    .progress-bar {
        flex: 1;
        height: 0.6rem;
        background: #f3f4f6;
        border-radius: 6px;
        overflow: hidden;
        margin: 0 0.5rem;
        position: relative;
    }
    .progress-bar > div {
        height: 100%;
        background: linear-gradient(90deg, #3b82f6, #60a5fa);
        border-radius: 6px;
        transition: width 0.7s cubic-bezier(.4,0,.2,1);
    }
    .rating-value {
        font-weight: 600;
        color: #3b82f6;
        min-width: 2.2rem;
        text-align: right;
    }
    .trend-up {
        color: #10b981;
        font-size: 0.95em;
        margin-left: 0.3em;
    }
    .trend-down {
        color: #ef4444;
        font-size: 0.95em;
        margin-left: 0.3em;
    }
    /* Календарь: точки событий */
    .calendar-date.event {
        position: relative;
    }
    .calendar-date .event-dot {
        display: inline-block;
        width: 7px;
        height: 7px;
        background: #3b82f6;
        border-radius: 50%;
        position: absolute;
        bottom: 3px;
        right: 6px;
    }
    .calendar-date.today {
        background: linear-gradient(135deg, #3b82f6, #60a5fa);
        color: #fff;
        font-weight: 700;
    }
    /* Пустые состояния */
    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #a0aec0;
        font-size: 1.1em;
        padding: 2em 0;
    }
    .empty-state img {
        width: 60px;
        margin-bottom: 1em;
        opacity: 0.7;
    }
    @media (max-width: 1100px) {
        .dashboard-widgets-grid-2x2 {
            grid-template-columns: 1fr;
        }
    }
    @media (max-width: 768px) {
        .widget-card {
            min-height: 180px;
            padding: 1rem 0.7rem;
        }
        .widget-title {
            font-size: 1rem;
        }
    }
    </style>

    <style>
    .todo-list-minimal {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 0.7rem;
    }
    .todo-list-minimal li {
        display: flex;
        align-items: center;
        background: #f9fafb;
        border-radius: 10px;
        padding: 0.7rem 1rem;
        box-shadow: 0 1px 4px rgba(59,130,246,0.04);
        transition: background 0.18s, box-shadow 0.18s;
        font-size: 1rem;
        color: #374151;
        position: relative;
    }
    .todo-list-minimal li:hover {
        background: #f3f4f6;
        box-shadow: 0 2px 8px rgba(59,130,246,0.07);
    }
    .todo-drag {
        color: #cbd5e1;
        font-size: 1.2em;
        margin-right: 1rem;
        cursor: grab;
        flex-shrink: 0;
    }
    .todo-list-minimal input[type="checkbox"] {
        width: 1.1rem;
        height: 1.1rem;
        accent-color: #3b82f6;
        margin-right: 1rem;
        flex-shrink: 0;
    }
    .todo-list-minimal label {
        flex: 1 1 auto;
        cursor: pointer;
        margin: 0;
        transition: color 0.18s;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .todo-list-minimal li.done label {
        text-decoration: line-through;
        color: #a0aec0;
    }
    .todo-actions {
        display: flex;
        gap: 0.7rem;
        margin-left: 1rem;
        color: #cbd5e1;
        font-size: 1.1em;
    }
    .todo-actions i {
        cursor: pointer;
        transition: color 0.18s;
    }
    .todo-actions i:hover {
        color: #3b82f6;
    }
    </style>

    <style>
    .calendar-widget {
        min-width: 0;
        /* Было padding: 0, возвращаем стандартный отступ виджета */
        padding: 1.1rem;
    }
    .calendar-header-modern {
        display: flex;
        align-items: center;
        justify-content: space-between;
        /* Убираем лишние отступы, так как они теперь есть у родителя */
        padding: 0.5rem 0 0.8rem 0;
    }
    .calendar-nav {
        display: flex;
        align-items: center;
        gap: 0.8rem;
    }
    .calendar-month-title {
        font-size: 1.05rem;
        font-weight: 600;
        color: #374151;
    }
    .calendar-title {
        font-size: 1.15rem;
        font-weight: 700;
        color: #2d3748;
    }
    .calendar-title-container {
        display: flex;
        align-items: center;
        gap: 0.6rem;
    }
    .calendar-icon {
        font-size: 1.2rem;
        color: #3b82f6;
    }
    .calendar-nav-group {
        display: flex;
        gap: 0.3rem;
    }
    .calendar-nav-btn {
        background: #f3f4f6;
        border: none;
        border-radius: 6px;
        padding: 0.3rem 0.6rem;
        cursor: pointer;
        color: #64748b;
        font-size: 1rem;
        transition: background 0.18s;
    }
    .calendar-nav-btn:hover {
        background: #e0e7ff;
        color: #3b82f6;
    }
    .calendar-grid {
        display: flex;
        flex-direction: column;
        gap: 0.9rem;
        padding: 0 1.1rem 1.1rem 1.1rem;
    }
    .calendar-row {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 0.1rem;
    }
    .calendar-days-row div {
        font-size: 0.95em;
        color: #a0aec0;
        font-weight: 600;
        text-align: center;
        padding: 0.3em 0 0.3em 0;
    }
    .calendar-day {
        min-height: 2.1em;
        text-align: center;
        font-size: 1.05em;
        color: #374151;
        background: #fff;
        border-radius: 7px;
        position: relative;
        transition: background 0.18s, color 0.18s;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 500;
        cursor: pointer;
        flex-direction: column;
        padding: 0.2em 0 0.2em 0;
    }
    .calendar-day.muted {
        color: #cbd5e1;
        background: #f8fafc;
        cursor: default;
    }
    .calendar-day:hover:not(.muted) {
        background: #f3f6fd;
        color: #3b82f6;
    }
    .calendar-badge-mini {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        position: absolute;
        right: 7px;
        bottom: 6px;
        min-width: 16px;
        height: 16px;
        background: #3b82f6;
        color: #fff;
        font-size: 0.85em;
        font-weight: 700;
        border-radius: 50%;
        box-shadow: none;
        z-index: 2;
        padding: 0 0.1em;
        border: 1.5px solid #fff;
        line-height: 1;
    }
    .calendar-has-badge {
        position: relative;
    }
    </style>

    <style>
    .calendar-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        position: absolute;
        right: 7px;
        bottom: 7px;
        min-width: 22px;
        height: 22px;
        background: #2563eb;
        color: #fff;
        font-size: 0.95em;
        font-weight: 700;
        border-radius: 50%;
        box-shadow: 0 1px 4px rgba(59,130,246,0.07);
        z-index: 2;
        padding: 0 0.2em;
        border: 2px solid #fff;
    }
    .calendar-day {
        position: relative;
    }
    </style>

    <style>
    .appointments-table-block {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 1px 4px rgba(59,130,246,0.04);
    }

    .status-badge {
        display: inline-block;
        padding: 0.25em 1.1em;
        border-radius: 7px;
        font-size: 0.98em;
        font-weight: 600;
        color: #fff;
        background: #10b981;
        white-space: nowrap;
    }
    .status-done { background: #10b981; }
    .status-pending { background: #f59e0b; }
    .status-cancel { background: #ef4444; }
    .status-rescheduled { background: #3b82f6; } /* Добавил цвет для перенесенных */
    </style>

    <style>
    .appt-date { font-weight: 600; }
    .appt-time { color: #3b82f6; font-size: 0.98em; font-weight: 500; }
    </style>

    <!-- Подключаем FullCalendar -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>

    <style>
    /* Фикс для сегодняшнего дня: убираем рамку, добавляем фон */
    .fc-day-today, .fc-daygrid-day.fc-day-today {
        background: #fffbe6 !important;
        border: none !important;
        box-shadow: 0 2px 8px rgba(255,224,102,0.13) !important;
    }
    </style>

    <script>
        // === FullCalendar: минималистичный календарь с точками по статусу ===
        document.addEventListener('DOMContentLoaded', function() {
            if (document.getElementById('dashboardCalendar')) {
                const calendarEl = document.getElementById('dashboardCalendar');
                const calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    locale: 'ru',
                    height: 'auto',
                    firstDay: 1,
                    headerToolbar: false,
                    events: '/appointments/calendar-events',
                    eventDisplay: 'block',
                    eventContent: function(arg) {
                        const status = arg.event.extendedProps.status || arg.event.status;
                        const color = getStatusColor(status);
                        return { html: `<span class='fc-dot' style='background:${color}'></span>` };
                    },
                    dateClick: function(info) {
                        // Открыть модалку с событиями на этот день
                        showDayModal(info.dateStr, calendar.getEvents());
                    },
                    eventClick: function(info) {
                        info.jsEvent.preventDefault(); // Предотвращаем стандартное поведение
                        const dateStr = info.event.startStr.slice(0, 10);
                        showDayModal(dateStr, calendar.getEvents());
                    },
                    datesSet: function() {
                        updateCalendarTitle(this); // `this` is the calendar instance
                    }
                });

                function updateCalendarTitle(calInstance) {
                    const titleEl = document.getElementById('calendarMonthYearTitle');
                    if (titleEl) {
                        let title = calInstance.view.title;
                        titleEl.textContent = title.charAt(0).toUpperCase() + title.slice(1);
                    }
                }

                calendar.render();
                updateCalendarTitle(calendar); // Set initial title

                document.getElementById('calendarPrevBtn').addEventListener('click', function() {
                    calendar.prev();
                });

                document.getElementById('calendarNextBtn').addEventListener('click', function() {
                    calendar.next();
                });
            }
        });

        // Цвет точки по статусу
        function getStatusColor(status) {
            switch (status) {
                case 'done':
                case 'completed': return '#10b981';      // зелёный
                case 'pending': return '#f59e0b';        // оранжевый
                case 'cancelled': return '#ef4444';      // красный
                case 'rescheduled': return '#3b82f6';    // синий
                default: return '#cbd5e1';               // серый
            }
        }

        // Модалка для событий дня
        function showDayModal(dateStr, allEvents) {
            const modal = document.getElementById('calendarDayModal');
            const title = document.getElementById('modalDayTitle');
            const eventsBlock = document.getElementById('modalDayEvents');
            const addBtn = document.getElementById('addNewEventBtn');
            const closeBtn = document.getElementById('closeDayModalBtn');
            // Форматируем дату
            const d = new Date(dateStr);
            title.textContent = 'Записи на ' + d.toLocaleDateString('ru-RU');
            // Фильтруем события по дате
            const events = allEvents.filter(ev => {
                const evDate = ev.extendedProps.date || (ev.start ? ev.start.toISOString().slice(0,10) : null);
                return evDate === dateStr;
            });
            if (events.length === 0) {
                eventsBlock.innerHTML = '<div style="color:#888;">Нет записей на этот день</div>';
            } else {
                eventsBlock.innerHTML = events.map(ev => {
                    const time = ev.extendedProps.time ? ev.extendedProps.time.slice(0, 5) : (ev.start ? new Date(ev.start).toLocaleTimeString('ru-RU', { hour: '2-digit', minute: '2-digit' }) : '');
                    return `<div style='margin-bottom:0.7em; display:flex; align-items:center; gap:0.5em;'>
                        <span class='fc-dot' style='background:${getStatusColor(ev.extendedProps.status || ev.status)}'></span>
                        <span><b>${time}</b> ${ev.extendedProps.client || ''} <span style='color:#888;'>(${ev.extendedProps.service || ''})</span></span>
                    </div>`
                }).join('');
            }
            modal.style.display = 'flex';
            // Кнопка "Добавить новую"
            addBtn.onclick = function() {
                window.location.href = '/appointments?action=create&date=' + dateStr;
            };
            closeBtn.onclick = function() {
                modal.style.display = 'none';
            };
            // Закрытие по клику вне окна
            modal.onclick = function(e) {
                if (e.target === modal) modal.style.display = 'none';
            };
        }
    </script>

    <!-- Модальное окно для записей дня -->
    <div id="calendarDayModal" style="display:none; position:fixed; left:0; top:0; width:100vw; height:100vh; background:rgba(0,0,0,0.25); z-index:9999; align-items:center; justify-content:center;">
      <div style="background:#fff; border-radius:12px; max-width:400px; width:90vw; padding:24px 18px 18px 18px; box-shadow:0 8px 32px rgba(0,0,0,0.18); position:relative;">
        <button id="closeDayModalBtn" style="position:absolute; right:12px; top:10px; background:none; border:none; font-size:1.5em; color:#aaa; cursor:pointer;">&times;</button>
        <h3 id="modalDayTitle" style="margin-bottom:1em; font-size:1.1em;">Записи на день</h3>
        <div id="modalDayEvents"></div>
        <button id="addNewEventBtn" style="margin-top:1.2em; background:#3b82f6; color:#fff; border:none; border-radius:8px; padding:0.6em 1.2em; font-weight:600; cursor:pointer;">Добавить новую</button>
      </div>
    </div>
@endsection
