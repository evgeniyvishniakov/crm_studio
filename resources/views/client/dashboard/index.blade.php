@extends('client.layouts.app')

@section('content')
    <div class="dashboard-container">
        <h1 class="dashboard-title">{{ __('messages.dashboard') }}</h1>

        <div class="stats-grid">
            <!-- Карточка 1: Прибыль -->
            <div class="stat-card profit-card finances-group">
                <div class="stat-icon">
                    <i class="fas fa-coins"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-title">{{ __('messages.profit') }}</h3>
                    <p class="stat-value currency-amount" data-amount="{{ $totalProfit }}">{{ \App\Helpers\CurrencyHelper::format($totalProfit) }}</p>
                    @if($showDynamics)
                        <p class="stat-change positive">
                            <i class="fas fa-arrow-up"></i>
                            15.3% {{ __('messages.from_last_month') }}
                        </p>
                    @endif
                </div>
            </div>
            
            <!-- Карточка 2: Продажи товаров -->
            <div class="stat-card sales-card finances-group active">
                <div class="stat-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-title">{{ __('messages.product_sales') }}</h3>
                    <p class="stat-value currency-amount" data-amount="{{ $productsRevenue }}">{{ \App\Helpers\CurrencyHelper::format($productsRevenue) }}</p>
                    @if($showDynamics)
                        <p class="stat-change positive">
                            <i class="fas fa-arrow-up"></i>
                            8.7% {{ __('messages.from_last_month') }}
                        </p>
                    @endif
                </div>
            </div>
            
            <!-- Карточка 3: Продажи услуг -->
            <div class="stat-card services-card finances-group active">
                <div class="stat-icon">
                    <i class="fas fa-spa"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-title">{{ __('messages.service_sales') }}</h3>
                    <p class="stat-value currency-amount" data-amount="{{ $servicesRevenue }}">{{ \App\Helpers\CurrencyHelper::format($servicesRevenue) }}</p>
                    @if($showDynamics)
                        <p class="stat-change positive">
                            <i class="fas fa-arrow-up"></i>
                            12.5% {{ __('messages.from_last_month') }}
                        </p>
                    @endif
                </div>
            </div>
            
            <!-- Карточка 4: Расходы -->
            <div class="stat-card expenses-card finances-group active">
                <div class="stat-icon">
                    <i class="fas fa-credit-card"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-title">{{ __('messages.expenses') }}</h3>
                    <p class="stat-value currency-amount" data-amount="{{ $totalExpenses }}">{{ \App\Helpers\CurrencyHelper::format($totalExpenses) }}</p>
                    @if($showDynamics)
                        <p class="stat-change negative">
                            <i class="fas fa-arrow-down"></i>
                            5.2% {{ __('messages.from_last_month') }}
                        </p>
                    @endif
                </div>
            </div>
            
            <!-- Карточка 5: Услуги (активность) -->
            <div class="stat-card procedures-card activity-group" style="display: none;">
                <div class="stat-icon">
                    <i class="fas fa-spa"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-title">{{ __('messages.services') }}</h3>
                    <p class="stat-value">{{ $servicesCount }}</p>
                    @if($showDynamics)
                        <p class="stat-change positive">
                            <i class="fas fa-arrow-up"></i>
                            18.2% {{ __('messages.from_last_month') }}
                        </p>
                    @endif
                </div>
            </div>
            
            <!-- Карточка 6: Клиенты (активность) -->
            <div class="stat-card clients-card activity-group" style="display: none;">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-title">{{ __('messages.clients') }}</h3>
                    <p class="stat-value">{{ $clientsCount }}</p>
                    @if($showDynamics)
                        <p class="stat-change positive">
                            <i class="fas fa-arrow-up"></i>
                            12% {{ __('messages.from_last_month') }}
                        </p>
                    @endif
                </div>
            </div>
            
            <!-- Карточка 7: Записи (активность) -->
            <div class="stat-card appointments-card activity-group" style="display: none;">
                <div class="stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-title">{{ __('messages.appointments') }}</h3>
                    <p class="stat-value">{{ $appointmentsCount }}</p>
                    @if($showDynamics)
                        <p class="stat-change positive">
                            <i class="fas fa-arrow-up"></i>
                            5.2% {{ __('messages.from_last_week') }}
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Переключатели -->
        <div class="dashboard-tabs">
            <button class="tab-button active" data-tab="finances">
                <i class="fas fa-chart-pie"></i>
                {{ __('messages.finances') }}
            </button>
            <button class="tab-button" data-tab="activity">
                <i class="fas fa-chart-bar"></i>
                {{ __('messages.activity') }}
            </button>
        </div>

        <div class="dashboard-main-content">
            <div class="chart-container">
            <div class="widget-header-modern">
                <div class="widget-title-container">
                    <i class="fas fa-chart-line chart-icon"></i>
                    <span class="widget-title">{{ __('messages.dynamics') }}</span>
                </div>
            </div>
            <div class="chart-toolbar">
                <!-- Dropdown слева -->
                <div class="dropdown metric-dropdown">
                    <button class="dropdown-toggle metric-toggle" type="button">
                        <i class="fas fa-coins"></i>
                        <span id="selectedMetricLabel">{{ __('messages.profit') }}</span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="dropdown-menu metric-menu">
                        <button class="dropdown-item metric-item" data-type="profit"><i class="fas fa-coins"></i> {{ __('messages.profit') }}</button>
                        <button class="dropdown-item metric-item" data-type="sales"><i class="fas fa-shopping-cart"></i> {{ __('messages.product_sales') }}</button>
                        <button class="dropdown-item metric-item" data-type="services"><i class="fas fa-spa"></i> {{ __('messages.service_sales') }}</button>
                        <button class="dropdown-item metric-item" data-type="expenses"><i class="fas fa-credit-card"></i> {{ __('messages.expenses') }}</button>
                    </div>
                </div>
                <!-- Фильтры справа -->
                <div class="period-filters">
                    <button class="tab-button" data-period="30">{{ __('messages.per_month') }}</button>
                    <button class="tab-button" data-period="90">{{ __('messages.per_3_months') }}</button>
                    <button class="tab-button" data-period="180">{{ __('messages.per_6_months') }}</button>
                    <button class="tab-button" data-period="365">{{ __('messages.per_year') }}</button>
                    <button class="tab-button calendar-button" id="dateRangePicker"><i class="fas fa-calendar"></i></button>
                    <span id="calendarRangeDisplay"></span>
                </div>
            </div>
            <canvas id="universalChart" height="150"></canvas>
            <div id="custom-month-labels"></div>
        </div>
            <div class="dashboard-widgets-grid-2x2">
                <!-- 1. Календарь -->
                <div class="widget-card calendar-widget">
                    <div class="widget-content">
                        <div class="widget-header-modern">
                            <div class="widget-title-container">
                                <i class="fas fa-calendar-alt calendar-icon"></i>
                                <span class="widget-title">{{ __('messages.calendar') }}</span>
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
                        <div class="widget-header-modern">
                            <div class="widget-title-container">
                                <i class="fas fa-rectangle-list list-icon"></i>
                                <span class="widget-title">{{ __('messages.appointments') }}</span>
                            </div>
                            <button class="tab-button active" id="addWidgetAppointmentBtn">{{ __('messages.add_new') }}</button>
                        </div>
                        <div class="appointments-table-block">
                            <table class="table-striped appointments-table">
                                <thead>
                                    <tr>
                                        <th>{{ __('messages.date') }}</th>
                                        <th>{{ __('messages.client') }}</th>
                                        <th>{{ __('messages.service') }}</th>
                                        <th>{{ __('messages.status') }}</th>
                                        <th>{{ __('messages.cost') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        function getStatusInfo($status) {
                                            $map = [
                                                'completed' => ['class' => 'done', 'name' => __('messages.completed')],
                                                'pending' => ['class' => 'pending', 'name' => __('messages.pending')],
                                                'cancelled' => ['class' => 'cancel', 'name' => __('messages.cancelled')],
                                                'rescheduled' => ['class' => 'rescheduled', 'name' => __('messages.rescheduled')],
                                            ];
                                            return $map[$status] ?? ['class' => 'default', 'name' => ucfirst($status)];
                                        }
                                    @endphp
                                    @forelse($upcomingAppointments as $appointment)
                                        @php $statusInfo = getStatusInfo($appointment->status); @endphp
                                        <tr>
                                            <td>
                                                <span class="appt-date">{{ \Carbon\Carbon::parse($appointment->date)->isToday() ? __('messages.today') : __('messages.tomorrow') }}</span><br>
                                                <span class="appt-time">{{ \Carbon\Carbon::parse($appointment->time)->format('H:i') }}</span>
                                            </td>
                                            <td>{{ $appointment->client->name ?? __('messages.client_not_found') }}</td>
                                            <td>{{ $appointment->service->name ?? __('messages.service_not_found') }}</td>
                                            <td><span class="status-badge status-{{ $statusInfo['class'] }}">{{ $statusInfo['name'] }}</span></td>
                                            <td class="currency-amount" data-amount="{{ $appointment->price }}">{{ \App\Helpers\CurrencyHelper::format($appointment->price) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="appointments-table-empty">
                                                {{ __('messages.no_active_appointments') }}
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
                        <div class="widget-header-modern">
                            <div class="widget-title-container">
                                <i class="fas fa-clipboard-list summary-icon"></i>
                                <span class="widget-title">{{ __('messages.daily_summary') }}</span>
                            </div>
                        </div>
                        <div class="daily-summary-grid">
                            <!-- 1. Прибыль Услуги -->
                            <div class="summary-grid-item services-profit">
                                <div class="item-header">
                                    <i class="fas fa-spa item-icon"></i>
                                    <span class="item-label">{{ __('messages.services_profit') }}</span>
                                </div>
                                <div class="item-value currency-amount" data-amount="{{ $todayServicesProfit ?? 2000 }}">{{ \App\Helpers\CurrencyHelper::format($todayServicesProfit ?? 2000) }}</div>
                            </div>
                            <!-- 2. Прибыль Товары -->
                            <div class="summary-grid-item products-profit">
                                <div class="item-header">
                                    <i class="fas fa-boxes-stacked item-icon"></i>
                                    <span class="item-label">{{ __('messages.products_profit') }}</span>
                                </div>
                                <div class="item-value currency-amount" data-amount="{{ $todayProductsProfit ?? 1200 }}">{{ \App\Helpers\CurrencyHelper::format($todayProductsProfit ?? 1200) }}</div>
                            </div>
                            <!-- 3. Услуг оказано -->
                            <div class="summary-grid-item services-count">
                                <div class="item-header">
                                    <i class="fas fa-check-circle item-icon"></i>
                                    <span class="item-label">{{ __('messages.services_completed') }}</span>
                                </div>
                                <div class="item-value">{{ $todayCompletedServices ?? 4 }}</div>
                            </div>
                            <!-- 4. Товаров продано -->
                            <div class="summary-grid-item products-count">
                                <div class="item-header">
                                    <i class="fas fa-shopping-basket item-icon"></i>
                                    <span class="item-label">{{ __('messages.products_sold') }}</span>
                                </div>
                                <div class="item-value">{{ $todaySoldProducts ?? 8 }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- 4. To Do List -->
                <div class="widget-card">
                    <div class="widget-content">
                        <div class="widget-header-modern">
                            <div class="widget-title-container">
                                <i class="fas fa-list-check todo-icon"></i>
                                <span class="widget-title">{{ __('messages.todo_list') }}</span>
                            </div>
                            <div class="todo-add-form">
                                <input type="text" id="newTodoInput" placeholder="{{ __('messages.task_placeholder') }}">
                                <button id="addTodoBtn"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                        <ul class="todo-list-minimal" id="todoListContainer">
                            <!-- Задачи будут добавляться сюда динамически -->
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentMetric = 'profit';
        let currentPeriod = document.querySelector('.period-filters .tab-button.active')?.dataset.period || '30';

        // Функция анимации удалена для совместимости с системой валют

        // Убираем анимацию для совместимости с системой валют
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.stat-value');
            
            // Просто добавляем класс animated без анимации
            cards.forEach((card) => {
                card.classList.add('animated');
            });
            
            // Обновляем карточки сразу
            currentPeriod = document.querySelector('.period-filters .tab-button.active')?.dataset.period || '30';
            updateAllStatCardsByPeriod(currentPeriod, false); // false — без анимации
        });

        // Данные для графика (пример)
        const datasets = {
            profit: {
                label: "{{ __('messages.profit') }}",
                icon: "fa-coins",
                data: {
                    7: [18000, 17500, 17000, 16800, 16500, 16200, 16000],
                    30: [10000, 12000, 15000, 14000, 16000, 18000, 17500, 17000, 16800, 16500, 16200, 16000, 15800, 15500, 15300, 15000, 14800, 14500, 14300, 14000, 13800, 13500, 13300, 13000, 12800, 12500, 12300, 12000, 11800, 11500],
                    90: [10000, 10500, 11000, 11500, 12000, 12500, 13000, 13500, 14000, 14500, 15000, 15500, 16000, 16500, 17000, 17500, 18000, 18500, 19000, 19500, 20000, 20500, 21000, 21500, 22000, 22500, 23000, 23500, 24000, 24500, 25000, 25500, 26000, 26500, 27000, 27500, 28000, 28500, 29000, 29500, 30000, 30500, 31000, 31500, 32000, 32500, 33000, 33500, 34000, 34500, 35000, 35500, 36000, 36500, 37000, 37500, 38000, 38500, 39000, 39500, 40000, 40500, 41000, 41500, 42000, 42500, 43000, 43500, 44000, 44500, 45000, 45500, 46000, 46500, 47000, 47500, 48000, 48500, 49000, 49500]
                }
            },
            sales: {
                label: "{{ __('messages.product_sales') }}",
                icon: "fa-shopping-cart",
                data: {
                    7: [14000, 13500, 13000, 12800, 12500, 12200, 12000],
                    30: [8000, 9000, 11000, 13000, 12500, 14000, 13500, 13000, 12800, 12500, 12200, 12000, 11800, 11500, 11300, 11000, 10800, 10500, 10300, 10000, 9800, 9500, 9300, 9000, 8800, 8500, 8300, 8000, 7800, 7500],
                    90: [8000, 8500, 9000, 9500, 10000, 10500, 11000, 11500, 12000, 12500, 13000, 13500, 14000, 14500, 15000, 15500, 16000, 16500, 17000, 17500, 18000, 18500, 19000, 19500, 20000, 20500, 21000, 21500, 22000, 22500, 23000, 23500, 24000, 24500, 25000, 25500, 26000, 26500, 27000, 27500, 28000, 28500, 29000, 29500, 30000, 30500, 31000, 31500, 32000, 32500, 33000, 33500, 34000, 34500, 35000, 35500, 36000, 36500, 37000, 37500, 38000, 38500, 39000, 39500, 40000, 40500, 41000, 41500, 42000, 42500, 43000, 43500, 44000, 44500, 45000, 45500, 46000, 46500, 47000, 47500]
                }
            },
            services: {
                label: "{{ __('messages.service_sales') }}",
                icon: "fa-spa",
                data: {
                    7: [4500, 4400, 4300, 4280, 4250, 4220, 4200],
                    30: [3000, 3500, 4000, 3800, 4100, 4500, 4400, 4300, 4280, 4250, 4220, 4200, 4180, 4150, 4130, 4100, 4080, 4050, 4030, 4000, 3980, 3950, 3930, 3900, 3880, 3850, 3830, 3800, 3780, 3750],
                    90: [3000, 3200, 3400, 3600, 3800, 4000, 4200, 4400, 4600, 4800, 5000, 5200, 5400, 5600, 5800, 6000, 6200, 6400, 6600, 6800, 7000, 7200, 7400, 7600, 7800, 8000, 8200, 8400, 8600, 8800, 9000, 9200, 9400, 9600, 9800, 10000, 10200, 10400, 10600, 10800, 11000, 11200, 11400, 11600, 11800, 12000, 12200, 12400, 12600, 12800, 13000, 13200, 13400, 13600, 13800, 14000, 14200, 14400, 14600, 14800, 15000, 15200, 15400, 15600, 15800, 16000, 16200, 16400, 16600, 16800, 17000, 17200, 17400, 17600, 17800, 18000, 18200, 18400, 18600, 18800]
                }
            },
            expenses: {
                label: "{{ __('messages.expenses') }}",
                icon: "fa-credit-card",
                data: {
                    7: [2900, 2850, 2800, 2780, 2750, 2720, 2700],
                    30: [2000, 2500, 3000, 2800, 2700, 2900, 2850, 2800, 2780, 2750, 2720, 2700, 2680, 2650, 2630, 2600, 2580, 2550, 2530, 2500, 2480, 2450, 2430, 2400, 2380, 2350, 2330, 2300, 2280, 2250],
                    90: [2000, 2100, 2200, 2300, 2400, 2500, 2600, 2700, 2800, 2900, 3000, 3100, 3200, 3300, 3400, 3500, 3600, 3700, 3800, 3900, 4000, 4100, 4200, 4300, 4400, 4500, 4600, 4700, 4800, 4900, 5000, 5100, 5200, 5300, 5400, 5500, 5600, 5700, 5800, 5900, 6000, 6100, 6200, 6300, 6400, 6500, 6600, 6700, 6800, 6900, 7000, 7100, 7200, 7300, 7400, 7500, 7600, 7700, 7800, 7900, 8000, 8100, 8200, 8300, 8400, 8500, 8600, 8700, 8800, 8900, 9000, 9100, 9200, 9300, 9400, 9500, 9600, 9700, 9800, 9900]
                }
            },
            clients: {
                label: "{{ __('messages.clients') }}",
                icon: "fa-users",
                data: {
                    7: [90, 88, 85, 83, 80, 78, 75],
                    30: [50, 60, 65, 80, 75, 90, 88, 85, 83, 80, 78, 75, 73, 70, 68, 65, 63, 60, 58, 55, 53, 50, 48, 45, 43, 40, 38, 35, 33, 30],
                    90: [50, 52, 54, 56, 58, 60, 62, 64, 66, 68, 70, 72, 74, 76, 78, 80, 82, 84, 86, 88, 90, 92, 94, 96, 98, 100, 102, 104, 106, 108, 110, 112, 114, 116, 118, 120, 122, 124, 126, 128, 130, 132, 134, 136, 138, 140, 142, 144, 146, 148, 150, 152, 154, 156, 158, 160, 162, 164, 166, 168, 170, 172, 174, 176, 178, 180, 182, 184, 186, 188, 190, 192, 194, 196, 198, 200, 202, 204, 206, 208]
                }
            },
            appointments: {
                label: "{{ __('messages.appointments') }}",
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
            label: '{{ __('messages.activity') }}',
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
            if (period === '30') return getMonthStartDates();
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
        let universalChart = null;
        // Функция округления до красивого значения (500 или 1000)
        function getNiceMax(value) {
            return Math.ceil(value / 1000) * 1000;
        }
        function createUniversalChart(type, labels, data, color, labelText) {
            const ctx = document.getElementById('universalChart').getContext('2d');
            if (universalChart) universalChart.destroy();

            // Определяем, что передали: массив datasets или массив чисел
            let datasets;
            if (Array.isArray(data) && data.length > 0 && typeof data[0] === 'object' && data[0].label) {
                datasets = data;
            } else {
                datasets = [{
                    label: labelText,
                    data: data,
                    borderColor: color,
                    backgroundColor: function(ctx) {
                        const chart = ctx.chart;
                        const {ctx:canvasCtx, chartArea} = chart;
                        if (!chartArea) return color + (type === 'bar' ? '33' : '22');
                        if (type === 'bar') {
                            if (labelText === 'Продажи товаров') {
                                const grad = canvasCtx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                                grad.addColorStop(0, 'rgba(59,130,246,0.18)');
                                grad.addColorStop(0.7, 'rgba(59,130,246,0.45)');
                                grad.addColorStop(1, 'rgba(59,130,246,0.85)');
                                return grad;
                            }
                            const grad = canvasCtx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                            grad.addColorStop(0, 'rgba(139,92,246,0.22)');
                            grad.addColorStop(0.7, 'rgba(139,92,246,0.45)');
                            grad.addColorStop(1, 'rgba(139,92,246,0.85)');
                            return grad;
                        } else {
                            const grad = canvasCtx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                            grad.addColorStop(0, color + '33');
                            grad.addColorStop(1, color + '05');
                            return grad;
                        }
                    },
                    tension: 0.4,
                    fill: type !== 'bar',
                    pointRadius: type === 'bar' ? 5 : 5,
                    pointBackgroundColor: color,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointHoverRadius: 8,
                    pointHitRadius: 14,
                    spanGaps: true,
                    borderRadius: type === 'bar' ? 8 : 0
                }];
            }

            universalChart = new Chart(ctx, {
                type: type,
                data: {
                    labels: labels,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    animation: { duration: 900, easing: 'easeOutQuart' },
                    layout: { padding: { top: 32 } },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(59,130,246,0.95)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            borderColor: color,
                            borderWidth: 1.5,
                            cornerRadius: 8,
                            padding: 12,
                            caretSize: 8,
                            displayColors: false
                        },
                        decimation: {
                            enabled: true,
                            algorithm: 'min-max'
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: {
                                color: '#22223b',
                                font: { size: 11, weight: '600' },
                                padding: 8,
                                autoSkip: false,
                                maxRotation: 0,
                                minRotation: 0,
                                stepSize: 1,
                                callback: function(value, index, ticks) {
                                    if (currentPeriod === '90' || currentPeriod === '180' || currentPeriod === '365') {
                                        return '';
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
                            grid: { color: '#e5e7eb', lineWidth: 1.2 },
                            ticks: { color: '#22223b', font: { size: 15, weight: '600' }, padding: 8 },
                            beginAtZero: true,
                            max: undefined
                        }
                    }
                }
            });
            // --- Устанавливаем максимум оси Y на красивое значение ---
            let allData = [];
            if (Array.isArray(datasets)) {
                datasets.forEach(ds => {
                    if (Array.isArray(ds.data)) allData = allData.concat(ds.data);
                });
            }
            const maxValue = Math.max(...allData);
            let niceMax = maxValue > 0 ? getNiceMax(Math.ceil(maxValue * 1.10)) : 5000;
            universalChart.options.scales.y.max = niceMax;
            universalChart.update();
        }
        // По умолчанию активна кнопка месяц
        document.querySelectorAll('.period-filters .tab-button').forEach(btn => {
            btn.classList.remove('active');
            if (btn.getAttribute('data-period') === '30') btn.classList.add('active');
        });

        // Инициализация universalChart только один раз при загрузке страницы
        document.addEventListener('DOMContentLoaded', function() {
            fetch('/api/dashboard/profit-chart?period=30', { credentials: 'same-origin' })
                .then(res => res.json())
                .then(res => {
                    createUniversalChart('line', res.labels, getCumulativeData(res.data), getMetricColor('profit'), 'Прибыль');
                    universalChart.options.scales.y.max = res.maxValue || undefined;
                    universalChart.update();
                    renderCustomMonthLabels(res.labels);
                });
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

                // Если выбран диапазон дат — обновляем по диапазону
                if (selectedRange && selectedRange.start && selectedRange.end) {
                    const formatISO = d => d.toISOString().slice(0, 10);
                    updateChartByRange(formatISO(selectedRange.start), formatISO(selectedRange.end));
                    return;
                }

                // Если нет — по period (старая логика)
                if (type === 'profit') {
                    fetch(`/api/dashboard/profit-chart?period=${currentPeriod}`, { credentials: 'same-origin' })
                        .then(res => res.json())
                        .then(res => {
                            createUniversalChart('line', res.labels, getCumulativeData(res.data), getMetricColor('profit'), 'Прибыль');
                            universalChart.options.scales.y.max = res.maxValue || undefined;
                            universalChart.update();
                            renderCustomMonthLabels(res.labels);
                        });
                    return;
                }
                if (type === 'expenses') {
                    fetch(`/api/dashboard/expenses-chart?period=${currentPeriod}`, { credentials: 'same-origin' })
                        .then(res => res.json())
                        .then(res => {
                            const data = getCumulativeData(res.data);
                            let labels = res.labels;
                            createUniversalChart('line', labels, data, getMetricColor('expenses'), datasets['expenses'].label);
                            renderCustomMonthLabels(labels);
                        });
                    return;
                }
                if (type === 'sales') {
                    fetch(`/api/dashboard/sales-chart?period=${currentPeriod}`, { credentials: 'same-origin' })
                        .then(res => res.json())
                        .then(res => {
                            createUniversalChart('bar', res.labels, res.data, getMetricColor('sales'), 'Продажи товаров');
                            universalChart.update();
                            // Обновляем карточку "Продажи товаров" без анимации
                            const salesCard = document.querySelector('.stat-card.sales-card .stat-value');
                            if (salesCard && Array.isArray(res.data)) {
                                const total = res.data.reduce((sum, v) => sum + (parseFloat(v) || 0), 0);
                                // Используем систему валют
                                salesCard.className = 'stat-value currency-amount';
                                salesCard.setAttribute('data-amount', total);
                                if (window.CurrencyManager) {
                                    salesCard.textContent = window.CurrencyManager.formatAmount(total);
                                } else {
                                    salesCard.textContent = new Intl.NumberFormat('{{ app()->getLocale() }}-{{ strtoupper(app()->getLocale()) }}', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(total) + ' грн';
                                }
                            }
                            renderCustomMonthLabels(res.labels);
                        });
                    return;
                }
                if (type === 'services') {
                    fetch(`/api/dashboard/services-chart?period=${currentPeriod}`, { credentials: 'same-origin' })
                        .then(res => res.json())
                        .then(res => {
                            createUniversalChart('bar', res.labels, res.data, getMetricColor('services'), 'Продажи услуг');
                            universalChart.update();
                            renderCustomMonthLabels(res.labels);
                        });
                    return;
                }
                // Для других метрик по умолчанию line
                fetch(`/api/dashboard/${type}-chart?period=${currentPeriod}`, { credentials: 'same-origin' })
                    .then(res => res.json())
                    .then(res => {
                        createUniversalChart('line', res.labels, res.data, getMetricColor(type), datasets[type].label);
                        universalChart.update();
                        renderCustomMonthLabels(res.labels);
                    });
            });
        });
        // Фильтры периода
        document.querySelectorAll('.period-filters .tab-button').forEach(btn => {
            btn.addEventListener('click', function() {
                if (btn.id === 'dateRangePicker') return;
                document.querySelectorAll('.period-filters .tab-button').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                calendarRangeDisplay.textContent = '';
                selectedRange = null;
                currentPeriod = this.dataset.period;
                // Обновляем данные для текущей метрики
                if (currentMetric === 'profit') {
                    fetch(`/api/dashboard/profit-chart?period=${currentPeriod}`, { credentials: 'same-origin' })
                        .then(res => res.json())
                        .then(res => {
                            createUniversalChart('line', res.labels, getCumulativeData(res.data), getMetricColor('profit'), 'Прибыль');
                            universalChart.options.scales.y.max = res.maxValue || undefined;
                            universalChart.update();
                            renderCustomMonthLabels(res.labels);
                        });
                    return;
                }
                if (currentMetric === 'expenses') {
                    fetch(`/api/dashboard/expenses-chart?period=${currentPeriod}`, { credentials: 'same-origin' })
                        .then(res => res.json())
                        .then(res => {
                            const data = getCumulativeData(res.data);
                            createUniversalChart('line', res.labels, data, getMetricColor('expenses'), 'Расходы');
                            renderCustomMonthLabels(res.labels);
                        });
                    return;
                }
                if (currentMetric === 'sales') {
                    fetch(`/api/dashboard/sales-chart?period=${currentPeriod}`, { credentials: 'same-origin' })
                        .then(res => res.json())
                        .then(res => {
                            createUniversalChart('bar', res.labels, res.data, getMetricColor('sales'), 'Продажи товаров');
                            universalChart.update();
                            renderCustomMonthLabels(res.labels);
                        });
                    return;
                }
                if (currentMetric === 'services') {
                    fetch(`/api/dashboard/services-chart?period=${currentPeriod}`, { credentials: 'same-origin' })
                        .then(res => res.json())
                        .then(res => {
                            createUniversalChart('bar', res.labels, res.data, getMetricColor('services'), 'Продажи услуг');
                            universalChart.update();
                            renderCustomMonthLabels(res.labels);
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
        // Переключение вкладок Финансы/Активность
        document.querySelectorAll('.dashboard-tabs .tab-button').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.dashboard-tabs .tab-button').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                const tab = this.getAttribute('data-tab');
                
                // Скрываем все карточки активности
                document.querySelectorAll('.stat-card.activity-group').forEach(card => {
                    card.style.display = 'none';
                });
                
                // Скрываем карточки финансов (кроме прибыли)
                document.querySelectorAll('.stat-card.finances-group:not(.profit-card)').forEach(card => {
                    card.style.display = 'none';
                });
                
                // Показываем карточки выбранной группы
                if (tab === 'finances') {
                    document.querySelectorAll('.stat-card.finances-group').forEach(card => {
                        card.style.display = 'flex';
                    });
                } else if (tab === 'activity') {
                    document.querySelectorAll('.stat-card.activity-group').forEach(card => {
                        card.style.display = 'flex';
                    });
                    // Прибыль всегда видна
                    document.querySelector('.stat-card.profit-card').style.display = 'flex';
                }
            });
        });

        function getLastNDates(n) {
            const arr = [];
            const now = new Date();
            for (let i = n - 1; i >= 0; i--) {
                const d = new Date(now);
                d.setDate(now.getDate() - i);
                arr.push(d.toLocaleDateString('{{ app()->getLocale() }}-{{ strtoupper(app()->getLocale()) }}', { day: 'numeric', month: 'short' }).replace('.', ''));
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
                arr.push(d.toLocaleDateString('{{ app()->getLocale() }}-{{ strtoupper(app()->getLocale()) }}', { day: 'numeric', month: 'short' }).replace('.', ''));
            }
            return arr;
        }

        // Возвращает массив дат от первого числа месяца до сегодня в формате 'дд МММ'
        function getMonthStartDates() {
            const arr = [];
            const now = new Date();
            const year = now.getFullYear();
            const month = now.getMonth();
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            for (let i = 1; i <= now.getDate(); i++) {
                const d = new Date(year, month, i);
                arr.push(d.toLocaleDateString('{{ app()->getLocale() }}-{{ strtoupper(app()->getLocale()) }}', { day: 'numeric', month: 'short' }).replace('.', ''));
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

        // Добавляю функцию для генерации подписей месяцев
        function getMonthLabels(n) {
            const arr = [];
            const now = new Date();
            let prevMonth = null;
            for (let i = n - 1; i >= 0; i--) {
                const d = new Date(now);
                d.setDate(now.getDate() - i);
                const month = d.toLocaleDateString('{{ app()->getLocale() }}-{{ strtoupper(app()->getLocale()) }}', { month: 'short' });
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

        // --- Функция для обновления значений в карточках ---
        function updateStatCardValue(type, value) {
            let selector = '';
            if (type === 'profit') selector = '.stat-card.profit-card .stat-value';
            if (type === 'expenses') selector = '.stat-card.expenses-card .stat-value';
            if (type === 'sales') selector = '.stat-card.sales-card .stat-value';
            if (type === 'services') selector = '.stat-card.services-card .stat-value';
            if (!selector) return;
            const card = document.querySelector(selector);
            if (card) {
                card.classList.remove('animated');
                // Используем систему валют
                card.className = 'stat-value currency-amount';
                card.setAttribute('data-amount', value);
                if (window.CurrencyManager) {
                    card.textContent = window.CurrencyManager.formatAmount(value);
                } else {
                    // Fallback если CurrencyManager не загружен
                    card.textContent = new Intl.NumberFormat('{{ app()->getLocale() }}-{{ strtoupper(app()->getLocale()) }}', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(value) + ' грн';
                }
                void card.offsetWidth;
                card.classList.add('animated');
            }
        }

        // --- Универсальная функция для обновления всех карточек по данным с сервера ---
        function updateAllStatCardsByPeriod(period, animate = false, startDate = null, endDate = null) {
            // Прибыль
            let profitUrl = startDate && endDate
                ? `/api/dashboard/profit-chart?start_date=${startDate}&end_date=${endDate}`
                : `/api/dashboard/profit-chart?period=${period}`;
            fetch(profitUrl, { credentials: 'same-origin' })
                .then(res => res.json())
                .then(res => {
                    const value = res.data.reduce((sum, v) => sum + (parseFloat(v) || 0), 0);
                    const card = document.querySelector('.stat-card.profit-card .stat-value');
                    updateStatCardValue('profit', value);
                });
            // Расходы
            let expensesUrl = startDate && endDate
                ? `/api/dashboard/expenses-chart?start_date=${startDate}&end_date=${endDate}`
                : `/api/dashboard/expenses-chart?period=${period}`;
            fetch(expensesUrl, { credentials: 'same-origin' })
                .then(res => res.json())
                .then(res => {
                    const value = res.data.reduce((sum, v) => sum + (parseFloat(v) || 0), 0);
                    const card = document.querySelector('.stat-card.expenses-card .stat-value');
                    updateStatCardValue('expenses', value);
                });
            // Продажи товаров
            let salesUrl = startDate && endDate
                ? `/api/dashboard/sales-chart?start_date=${startDate}&end_date=${endDate}`
                : `/api/dashboard/sales-chart?period=${period}`;
            fetch(salesUrl, { credentials: 'same-origin' })
                .then(res => res.json())
                .then(res => {
                    const value = res.data.reduce((sum, v) => sum + (parseFloat(v) || 0), 0);
                    const card = document.querySelector('.stat-card.sales-card .stat-value');
                    updateStatCardValue('sales', value);
                });
            // Продажи услуг
            let servicesUrl = startDate && endDate
                ? `/api/dashboard/services-chart?start_date=${startDate}&end_date=${endDate}`
                : `/api/dashboard/services-chart?period=${period}`;
            fetch(servicesUrl, { credentials: 'same-origin' })
                .then(res => res.json())
                .then(res => {
                    const value = res.data.reduce((sum, v) => sum + (parseFloat(v) || 0), 0);
                    const card = document.querySelector('.stat-card.services-card .stat-value');
                    updateStatCardValue('services', value);
                });
        }

        // --- Вызов при загрузке страницы (по умолчанию месяц) ---
        document.addEventListener('DOMContentLoaded', function() {
            // Синхронизируем currentPeriod с активной кнопкой
            currentPeriod = document.querySelector('.period-filters .tab-button.active')?.dataset.period || '30';
            updateAllStatCardsByPeriod(currentPeriod, false); // false — без анимации
        });

        // --- Везде, где меняется период или диапазон, вызываем обновление карточек ---
        // В обработчике period-фильтра:
        document.querySelectorAll('.period-filters .tab-button').forEach(btn => {
            btn.addEventListener('click', function() {
                if (btn.id === 'dateRangePicker') return;
                document.querySelectorAll('.period-filters .tab-button').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                calendarRangeDisplay.textContent = '';
                selectedRange = null;
                currentPeriod = this.dataset.period;
                updateAllStatCardsByPeriod(currentPeriod, false);
                // ... остальной код ...
            });
        });
        // В updateChartByRange:
        function updateChartByRange(startDate, endDate) {
            updateAllStatCardsByPeriod(null, false, startDate, endDate);
            // ... остальной код ...
        }

        // ... существующий код ...
        // Вставляю функцию в начало основного <script> блока, до всех fetch/then:
        function renderCustomMonthLabels(labels) {
            const container = document.getElementById('custom-month-labels');
            if (!container) {
    
                return;
            }
            container.innerHTML = '';
            if (currentPeriod === '30' || !window.universalChart) {
                container.style.display = 'none';
    
                return;
            }

            
            // Устанавливаем стили контейнера
            container.style.display = 'flex';
            container.style.justifyContent = 'space-between';
            container.style.alignItems = 'center';
            container.style.height = '20px';
            container.style.marginTop = '-15px';
            container.style.fontSize = '12px';
            container.style.fontWeight = '600';
            container.style.color = '#22223b';
            container.style.position = 'relative';
            container.style.width = '80%';
            container.style.marginLeft = '120px';
            
            // Получаем координаты точек
            const meta = universalChart.getDatasetMeta(0);
            if (!meta || !meta.data) {
    
                return;
            }
            
            // Для столбчатых графиков используем центр столбца, для линейных - точку
            const points = meta.data.map(point => {
                if (universalChart.config.type === 'bar') {
                    return point.x + (point.width / 2);
                }
                return point.x;
            });
            

            
            // Создаем метки каждые 3 недели
            labels.forEach((label, i) => {
                // Показываем только каждую третью метку (каждые 3 недели)
                if (i % 3 !== 0) return;
                
                let text = label;
                if (currentPeriod === '365') {
                    const monthNum = label.split('.')[0];
                    const months = ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'];
                    const idx = parseInt(monthNum, 10) - 1;
                    text = months[idx] ?? label;
                } else {
                    // Для периодов 90/180 дней показываем только дату начала недели
                    // Убираем диапазон и оставляем только первую дату
                    text = label.split(' - ')[0];
                }
                
                const span = document.createElement('span');
                span.textContent = text;
                span.style.position = 'absolute';
                
                // Используем процентное позиционирование вместо абсолютных координат
                const percentage = (i / (labels.length - 1)) * 100;
                span.style.left = percentage + '%';
                span.style.transform = 'translateX(-50%)';
                
                span.style.fontSize = '11px';
                span.style.color = '#22223b';
                span.style.pointerEvents = 'none';
                span.style.zIndex = '1000';
                span.style.backgroundColor = 'rgba(255, 255, 255, 0.9)';
                span.style.padding = '1px 3px';
                span.style.borderRadius = '2px';
                span.style.fontWeight = '600';
                span.style.whiteSpace = 'nowrap';
                container.appendChild(span);

            });
        }
        // ... существующий код ...
    </script>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ru.js"></script>
<script>
    // === FullCalendar: минималистичный календарь с точками по статусу ===
    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('dashboardCalendar')) {
            const calendarEl = document.getElementById('dashboardCalendar');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: '{{ app()->getLocale() }}',
                height: 'auto',
                firstDay: 1,
                headerToolbar: false,
                events: '/appointments/calendar-events',

                eventDidMount: function(info) {
                    // Получаем дату текущего события
                    const currentEventDate = info.event.start ? info.event.start.toISOString().slice(0,10) : null;
                    
                    // Получаем все события на этот день
                    const dayEvents = calendar.getEvents().filter(ev => {
                        const evDate = ev.start ? ev.start.toISOString().slice(0,10) : null;
                        return evDate === currentEventDate;
                    });
                    
                    // Отладочная информация
    
                    
                    // Создаем индикатор для всех дней с записями
                    if (dayEvents.length > 0) {
                        const dayEl = info.el.closest('.fc-daygrid-day');
                        if (dayEl) {
                            // Проверяем, есть ли уже индикатор
                            let existingIndicator = dayEl.querySelector('.appointment-count-indicator');
                            
                            if (!existingIndicator) {
                
                                
                                // Создаем новый индикатор
                                const indicator = document.createElement('div');
                                indicator.className = 'appointment-count-indicator';
                                indicator.textContent = dayEvents.length;
                                dayEl.style.position = 'relative';
                                dayEl.appendChild(indicator);
                
                            }
                        }
                    }
                    
                    // Скрываем все точки
                    const dotEl = info.el.querySelector('.fc-daygrid-event-dot');
                    if (dotEl) {
                        dotEl.style.display = 'none';
                    }
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

            // Обработчик для кнопки "Добавить новую" в виджете "Записи"
            const addWidgetBtn = document.getElementById('addWidgetAppointmentBtn');
            if (addWidgetBtn) {
                addWidgetBtn.addEventListener('click', function() {
                    const today = new Date();
                    const year = today.getFullYear();
                    const month = String(today.getMonth() + 1).padStart(2, '0');
                    const day = String(today.getDate()).padStart(2, '0');
                    const todayDateStr = `${year}-${month}-${day}`;
                    window.location.href = '/appointments?action=create&date=' + todayDateStr;
                });
            }
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
        const addBtn = document.getElementById('modalAddAppointmentBtn');
        const closeBtn = document.getElementById('closeDayModalBtn');
        // Форматируем дату
        const d = new Date(dateStr);
        title.textContent = '{{ __('messages.appointments_for_day') }} ' + d.toLocaleDateString('{{ app()->getLocale() }}-{{ strtoupper(app()->getLocale()) }}');
        // Фильтруем события по дате
        const events = allEvents.filter(ev => {
            const evDate = ev.extendedProps.date || (ev.start ? ev.start.toISOString().slice(0,10) : null);
            return evDate === dateStr;
        });
        if (events.length === 0) {
            eventsBlock.innerHTML = '<div class="calendar-modal-empty">{{ __('messages.no_appointments_for_day') }}</div>';
        } else {
            eventsBlock.innerHTML = events.map(ev => {
                const time = ev.extendedProps.time ? ev.extendedProps.time.slice(0, 5) : (ev.start ? new Date(ev.start).toLocaleTimeString('{{ app()->getLocale() }}-{{ strtoupper(app()->getLocale()) }}', { hour: '2-digit', minute: '2-digit' }) : '');
                // Получаем статус
                const status = ev.extendedProps.status || ev.status || 'pending';
                let statusName = '';
                let statusClass = '';
                switch (status) {
                    case 'completed': statusName = '{{ __('messages.completed') }}'; statusClass = 'done'; break;
                    case 'pending': statusName = '{{ __('messages.pending') }}'; statusClass = 'pending'; break;
                    case 'cancelled': statusName = '{{ __('messages.cancelled') }}'; statusClass = 'cancel'; break;
                    case 'rescheduled': statusName = '{{ __('messages.rescheduled') }}'; statusClass = 'rescheduled'; break;
                    default: statusName = status; statusClass = 'default'; break;
                }
                return `<div class="calendar-modal-event-item">
                    <span class='fc-dot' style='background:${getStatusColor(status)}'></span>
                    <span><b>${time}</b> ${ev.extendedProps.client || ''} <span class="calendar-modal-service-name">(${ev.extendedProps.service || ''})</span></span>
                    <span class="status-badge status-${statusClass} calendar-modal-status-badge">${statusName}</span>
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

    // === Календарь выбора диапазона дат для графика ===
    const calendarBtn = document.getElementById('dateRangePicker');
    const calendarRangeDisplay = document.getElementById('calendarRangeDisplay');
    let calendarInstance = null;
    let selectedRange = null;
    calendarBtn.addEventListener('click', function (e) {
        if (!calendarInstance) {
            calendarInstance = flatpickr(calendarBtn, {
                mode: 'range',
                dateFormat: 'Y-m-d',
                locale: '{{ app()->getLocale() }}',
                onClose: function (selectedDates, dateStr) {
                    if (selectedDates.length === 2) {
                        document.querySelectorAll('.period-filters .tab-button').forEach(btn => btn.classList.remove('active'));
                        calendarBtn.classList.add('active');
                        const format = d => d.toLocaleDateString('{{ app()->getLocale() }}-{{ strtoupper(app()->getLocale()) }}', {day: '2-digit', month: '2-digit'});
                        calendarRangeDisplay.textContent = `${format(selectedDates[0])} — ${format(selectedDates[1])}`;
                        selectedRange = {start: selectedDates[0], end: selectedDates[1]};
                        // Форматируем для запроса
                        const formatISO = d => d.toISOString().slice(0, 10);
                        // Обновляем график для текущей метрики
                        updateChartByRange(formatISO(selectedDates[0]), formatISO(selectedDates[1]));
                    }
                }
            });
        }
        calendarInstance.open();
    });

    function updateChartByRange(startDate, endDate) {
        updateAllStatCardsByPeriod(null, true, startDate, endDate);
        // Определяем текущую метрику
        if (currentMetric === 'profit') {
            fetch(`/api/dashboard/profit-chart?start_date=${startDate}&end_date=${endDate}`, { credentials: 'same-origin' })
                .then(res => res.json())
                .then(res => {
                    createUniversalChart('line', res.labels, getCumulativeData(res.data), getMetricColor('profit'), 'Прибыль');
                    universalChart.options.scales.y.max = res.maxValue || undefined;
                    universalChart.update();
                    renderCustomMonthLabels(res.labels);
                });
            return;
        }
        if (currentMetric === 'expenses') {
            fetch(`/api/dashboard/expenses-chart?start_date=${startDate}&end_date=${endDate}`, { credentials: 'same-origin' })
                .then(res => res.json())
                .then(res => {
                    const data = getCumulativeData(res.data);
                    createUniversalChart('line', res.labels, data, getMetricColor('expenses'), 'Расходы');
                    renderCustomMonthLabels(res.labels);
                });
            return;
        }
        if (currentMetric === 'sales') {
            fetch(`/api/dashboard/sales-chart?start_date=${startDate}&end_date=${endDate}`, { credentials: 'same-origin' })
                .then(res => res.json())
                .then(res => {
                    createUniversalChart('bar', res.labels, res.data, getMetricColor('sales'), 'Продажи товаров');
                    universalChart.update();
                    renderCustomMonthLabels(res.labels);
                });
            return;
        }
        if (currentMetric === 'services') {
            fetch(`/api/dashboard/services-chart?start_date=${startDate}&end_date=${endDate}`, { credentials: 'same-origin' })
                .then(res => res.json())
                .then(res => {
                    createUniversalChart('bar', res.labels, res.data, getMetricColor('services'), 'Продажи услуг');
                    universalChart.update();
                    renderCustomMonthLabels(res.labels);
                });
            return;
        }
        // Для других метрик по умолчанию line
        fetch(`/api/dashboard/${currentMetric}-chart?start_date=${startDate}&end_date=${endDate}`, { credentials: 'same-origin' })
            .then(res => res.json())
            .then(res => {
                createUniversalChart('line', res.labels, res.data, getMetricColor(currentMetric), datasets[currentMetric].label);
                universalChart.update();
                renderCustomMonthLabels(res.labels);
            });
    }
</script>

<!-- Модальное окно для записей дня -->
<div id="calendarDayModal">
      <div class="calendar-modal-content">
          <button id="closeDayModalBtn">&times;</button>
          <h3 id="modalDayTitle">{{ __('messages.appointments_for_day') }}</h3>
    <div id="modalDayEvents"></div>
                      <button id="modalAddAppointmentBtn">{{ __('messages.add_new') }}</button>
  </div>
</div>

<script>
// === ToDo List Logic ===
document.addEventListener('DOMContentLoaded', function() {
    const todoListContainer = document.getElementById('todoListContainer');
    const newTodoInput = document.getElementById('newTodoInput');
    const addTodoBtn = document.getElementById('addTodoBtn');

    if (!todoListContainer || !newTodoInput || !addTodoBtn) return;

    let todos = JSON.parse(localStorage.getItem('dashboard_todos')) || [];

    function saveTodos() {
        localStorage.setItem('dashboard_todos', JSON.stringify(todos));
    }

    function renderTodos() {
        todoListContainer.innerHTML = '';
        if (todos.length === 0) {
            todoListContainer.innerHTML = `<li class="todo-empty-state">{{ __('messages.no_tasks_yet') }}</li>`;
            return;
        }
        todos.forEach((todo, index) => {
            const li = document.createElement('li');
            li.className = todo.done ? 'done' : '';
            li.dataset.index = index;
            li.innerHTML = `
                <span class="todo-drag"><i class="fas fa-grip-lines"></i></span>
                <input type="checkbox" id="todo-${index}" ${todo.done ? 'checked' : ''}>
                <label for="todo-${index}">${todo.text}</label>
                <span class="todo-actions">
                    <i class="fas fa-trash delete-btn"></i>
                </span>
            `;
            todoListContainer.appendChild(li);
        });
    }

    function addTodo() {
        const text = newTodoInput.value.trim();
        if (text) {
            todos.push({ text: text, done: false });
            newTodoInput.value = '';
            saveTodos();
            renderTodos();
        }
    }

    addTodoBtn.addEventListener('click', addTodo);
    newTodoInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            addTodo();
        }
    });

    todoListContainer.addEventListener('click', function(e) {
        const target = e.target;
        const li = target.closest('li');
        if (!li || li.classList.contains('todo-empty-state')) return;
        const index = li.dataset.index;

        // Toggle done
        if (target.type === 'checkbox') {
            todos[index].done = target.checked;
            saveTodos();
            renderTodos();
        }

        // Delete todo
        if (target.classList.contains('delete-btn')) {
            todos.splice(index, 1);
            saveTodos();
            renderTodos();
        }
    });

    renderTodos(); // Initial render

    // Инициализация SortableJS для перетаскивания
    new Sortable(todoListContainer, {
        animation: 150,
        handle: '.todo-drag', // Указываем, за какой элемент можно перетаскивать
        onEnd: function (evt) {
            // Обновляем массив `todos` в соответствии с новым порядком
            const movedItem = todos.splice(evt.oldIndex, 1)[0];
            todos.splice(evt.newIndex, 0, movedItem);

            // Сохраняем новый порядок и перерисовываем список
            saveTodos();
            renderTodos();
        }
    });
});
</script>

<script>
// === Современный стиль для universalChart ===
document.addEventListener('DOMContentLoaded', function() {
    // --- Современный стиль для universalChart ---
    if (window.Chart && Chart.defaults && Chart.defaults.scales) {
        Chart.defaults.scales.x = Chart.defaults.scales.x || {};
        Chart.defaults.scales.x.grid = Chart.defaults.scales.x.grid || {};
        Chart.defaults.scales.x.grid.display = false;
        Chart.defaults.scales.y = Chart.defaults.scales.y || {};
        Chart.defaults.scales.y.grid = Chart.defaults.scales.y.grid || {};
        Chart.defaults.scales.y.grid.color = '#e5e7eb';
        Chart.defaults.scales.y.grid.lineWidth = 1.2;
        Chart.defaults.scales.x.ticks = Chart.defaults.scales.x.ticks || {};
        Chart.defaults.scales.x.ticks.padding = 8;
        Chart.defaults.scales.y.ticks = Chart.defaults.scales.y.ticks || {};
        Chart.defaults.scales.y.ticks.padding = 8;
        Chart.defaults.font = Chart.defaults.font || {};
        Chart.defaults.font.family = 'Inter, Arial, sans-serif';
        Chart.defaults.font.size = 15;
        Chart.defaults.color = '#22223b';
        Chart.defaults.plugins = Chart.defaults.plugins || {};
        Chart.defaults.plugins.legend = Chart.defaults.plugins.legend || {};
        Chart.defaults.plugins.legend.display = false;
        Chart.defaults.plugins.tooltip = Chart.defaults.plugins.tooltip || {};
        Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(59,130,246,0.95)';
        Chart.defaults.plugins.tooltip.titleColor = '#fff';
        Chart.defaults.plugins.tooltip.bodyColor = '#fff';
        Chart.defaults.plugins.tooltip.borderColor = '#3b82f6';
        Chart.defaults.plugins.tooltip.borderWidth = 1.5;
        Chart.defaults.plugins.tooltip.cornerRadius = 8;
        Chart.defaults.plugins.tooltip.padding = 12;
        Chart.defaults.plugins.tooltip.caretSize = 8;
        Chart.defaults.plugins.tooltip.displayColors = false;
        Chart.defaults.elements = Chart.defaults.elements || {};
        Chart.defaults.elements.line = Chart.defaults.elements.line || {};
        Chart.defaults.elements.line.tension = 0.4;
        Chart.defaults.elements.line.borderWidth = 3;
        Chart.defaults.elements.line.borderColor = 'rgba(59,130,246,1)';
        Chart.defaults.elements.line.backgroundColor = function(ctx) {
            const chart = ctx.chart;
            const {ctx:canvasCtx, chartArea} = chart;
            if (!chartArea) return 'rgba(59,130,246,0.12)';
            const grad = canvasCtx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
            grad.addColorStop(0, 'rgba(59,130,246,0.18)');
            grad.addColorStop(1, 'rgba(59,130,246,0.01)');
            return grad;
        };
        Chart.defaults.elements.point = Chart.defaults.elements.point || {};
        Chart.defaults.elements.point.radius = 5;
        Chart.defaults.elements.point.backgroundColor = '#3b82f6';
        Chart.defaults.elements.point.borderColor = '#fff';
        Chart.defaults.elements.point.borderWidth = 2;
        Chart.defaults.elements.point.hoverRadius = 8;
        Chart.defaults.elements.bar = Chart.defaults.elements.bar || {};
        Chart.defaults.elements.bar.borderRadius = 8;
        Chart.defaults.elements.bar.backgroundColor = function(ctx) {
            const chart = ctx.chart;
            const {ctx:canvasCtx, chartArea} = chart;
            if (!chartArea) return 'rgba(139,92,246,0.18)';
            const grad = canvasCtx.createLinearGradient(chartArea.left, 0, chartArea.right, 0);
            grad.addColorStop(0, 'rgba(139,92,246,0.18)');
            grad.addColorStop(0.5, 'rgba(139,92,246,0.35)');
            grad.addColorStop(1, 'rgba(139,92,246,0.7)');
            return grad;
        };
        Chart.defaults.animation = Chart.defaults.animation || {};
        Chart.defaults.animation.duration = 900;
        Chart.defaults.animation.easing = 'easeOutQuart';
    }
});
</script>
@endpush

