@extends('client.layouts.app')

@section('content')
<style>
.period-filters {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    width: 100%;
    margin: 0 auto;
}
.filter-section {
    width: 100%;
}
#calendarRangeDisplay {
    text-align: center;
    vertical-align: middle;
    font-family: inherit;
    font-size: 14px;
    font-weight: 600;
    color: #64748b;
}
#calendarRangeDisplay:empty {
    display: none;
}
.period-tooltip {
    cursor: pointer;
    position: relative;
    display: inline-block;
}
.period-tooltip i {
    color: #64748b;
    font-size: 18px;
    vertical-align: middle;
}
.period-tooltip-text {
    display: none;
    position: absolute;
    left: 120%;
    top: 50%;
    transform: translateY(-50%);
    background: #fff;
    color: #222;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    padding: 14px 18px;
    font-size: 14px;
    min-width: 320px;
    z-index: 1000;
    white-space: pre-line;
}
.period-tooltip:hover .period-tooltip-text,
.period-tooltip:focus .period-tooltip-text {
    display: block;
}
</style>
<div class="dashboard-container">
    <h1 class="dashboard-title">Отчеты</h1>

    <!-- Навигация по вкладкам -->
    <div class="dashboard-tabs">
        <button class="tab-button active" data-tab="clients-analytics"><i class="fa fa-users"></i> Аналитика по клиентам</button>
        <button class="tab-button" data-tab="appointments-analytics"><i class="fa fa-calendar"></i> Аналитика по записям</button>
        <button class="tab-button" data-tab="complex-analytics"><i class="fa fa-money"></i> Финансовая аналитика</button>
    </div>

    <!-- Новый блок фильтров -->
    <div class="filter-section">
        <div class="period-filters">
            <span class="period-tooltip" tabindex="0">
                <i class="fa fa-question-circle" aria-hidden="true"></i>
                <span class="period-tooltip-text">
                    <b>Пояснения к периодам:</b><br>
                    <b>За неделю</b>: с последнего понедельника по сегодня.<br>
                    <b>За 2 недели</b>: с предпоследнего понедельника по сегодня.<br>
                    <b>За месяц</b>: с 1-го числа текущего месяца по сегодня.<br>
                    <b>За полгода</b>: с 1-го числа месяца первой продажи за последние 6 месяцев по сегодня (или просто с 1-го числа 6 месяцев назад, если нет продаж).<br>
                    <b>За год</b>: с 1-го числа месяца первой продажи за последние 12 месяцев по сегодня (или просто с 1-го числа 12 месяцев назад, если нет продаж).
                </span>
            </span>
            <button class="filter-button active">За неделю</button>
            <button class="filter-button">За 2 недели</button>
            <button class="filter-button">За месяц</button>
            <button class="filter-button">За полгода</button>
            <button class="filter-button">За год</button>
            <button class="filter-button calendar-button" id="dateRangePicker">
                <i class="fa fa-calendar"></i>
            </button>
            <span id="calendarRangeDisplay"></span>
        </div>
    </div>

    <!-- Содержимое вкладок -->
    <div class="tab-content">
        <!-- Вкладка "Аналитика по клиентам" -->
        <div class="tab-pane active" id="clients-analytics">
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">Динамика клиентской базы</h4>
                        <p class="text-muted">Количество новых клиентов за выбранный период.</p>
                        <canvas id="clientDynamicsChart"></canvas>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">Топ-5 клиентов по визитам</h4>
                        <p class="text-muted">Самые частые посетители за период.</p>
                        <canvas id="topClientsByVisitsChart"></canvas>
                    </div>
                </div>
                
            </div>
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">Визиты: Первичные / Повторные</h4>
                        <p class="text-muted">Соотношение первичных и повторных визитов за период.</p>
                        <canvas id="newVsReturningChart"></canvas>
                    </div>
                </div>
                 <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">Распределение по типам клиентов</h4>
                        <p class="text-muted">Соотношение клиентов по реальным типам из вашего справочника.</p>
                        <canvas id="clientTypesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Вкладка "Аналитика по записям" -->
        <div class="tab-pane" id="appointments-analytics" style="display: none;">
            <div class="row">
                <div class="col-lg-7 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">Загруженность по дням</h4>
                        <p class="text-muted">Количество записей по дням в течение недели.</p>
                        <canvas id="loadChart"></canvas>
                    </div>
                </div>
                <div class="col-lg-5 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">Статусы записей</h4>
                        <p class="text-muted">Соотношение выполненных, отмененных и пропущенных визитов.</p>
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 mb-4">
                     <div class="report-card">
                        <h4 class="mb-3">Популярность услуг</h4>
                        <p class="text-muted">Рейтинг услуг по количеству записей.</p>
                        <canvas id="servicesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Вкладка "Финансовая аналитика" -->
        <div class="tab-pane" id="complex-analytics" style="display: none;">
             <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">Топ-5 клиентов по выручке</h4>
                        <p class="text-muted">Клиенты, принесшие больше всего денег за период.</p>
                        <canvas id="topClientsByRevenueChart"></canvas>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">Динамика среднего чека</h4>
                        <p class="text-muted">Средняя сумма, которую клиент тратит за один визит.</p>
                        <canvas id="avgCheckChart"></canvas>
                    </div>
                </div>
            </div>
             <div class="row">
                <div class="col-lg-12 mb-12">
                    <div class="report-card">
                        <h4 class="mb-3">Популярность услуг по выручке</h4>
                        <p class="text-muted">Топ-10 услуг по сумме выручки за период.</p>
                        <canvas id="topServicesByRevenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Глобальные переменные для хранения экземпляров графиков
    let charts = {};

    // --- Инициализация всех графиков ---
    function initializeCharts() {
        const chartConfigs = {
            // Аналитика по клиентам
            clientDynamicsChart: {
                type: 'line',
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 },
                            grid: { display: true, color: '#e5e7eb' }
                        },
                        x: {
                            ticks: { autoSkip: true, maxRotation: 0 },
                            grid: { display: false }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                title: function(tooltipItems) {
                                    const label = tooltipItems[0].label;
                                    if (label && /^\d{4}-\d{2}-\d{2}$/.test(label)) {
                                        const date = new Date(label);
                                        return date.toLocaleDateString('ru-RU', { day: 'numeric', month: 'long', year: 'numeric' });
                                    }
                                    return label;
                                },
                                label: function(context) {
                                    return `Новые клиенты: ${context.parsed.y}`;
                                }
                            }
                        }
                    }
                }
            },
            clientTypesChart: { type: 'pie', options: {} },
            newVsReturningChart: { type: 'doughnut', options: {} },
            topClientsByVisitsChart: { type: 'bar', options: { scales: { y: { beginAtZero: true, grid: { display: true, color: '#e5e7eb' }, ticks: { precision: 0 } }, x: { grid: { display: false } } } } },
            // Аналитика по записям
            loadChart: { 
                type: 'bar', 
                options: { 
                    scales: { 
                        y: { beginAtZero: true, ticks: { precision: 0 }, grid: { display: true, color: '#e5e7eb' } },
                        x: { ticks: { autoSkip: true, maxRotation: 0 }, grid: { display: false } }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                title: function(tooltipItems) {
                                    const label = tooltipItems[0].label;
                                    if (label && /^\d{4}-\d{2}-\d{2}$/.test(label)) {
                                        const date = new Date(label);
                                        return date.toLocaleDateString('ru-RU', { day: 'numeric', month: 'long', year: 'numeric' });
                                    }
                                    return label;
                                },
                                label: function(context) {
                                    return `Записи: ${context.parsed.y}`;
                                }
                            }
                        }
                    } 
                } 
            },
            statusChart: { type: 'pie', options: {} },
            servicesChart: { 
                type: 'bar', 
                options: { 
                    indexAxis: 'y', 
                    scales: { 
                        x: { beginAtZero: true, grid: { display: true, color: '#e5e7eb' } }, 
                        y: { grid: { display: false } } 
                    }, 
                    plugins: { 
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                title: function(tooltipItems) {
                                    // Убираем скобки и число
                                    const label = tooltipItems[0].label;
                                    return label.replace(/\s*\(\d+\)$/, '');
                                }
                            }
                        }
                    } 
                } 
            },
            // Финансовая аналитика
            topClientsByRevenueChart: { type: 'bar', options: { indexAxis: 'y', scales: { x: { beginAtZero: true, grid: { display: true, color: '#e5e7eb' } }, y: { grid: { display: false } } }, plugins: { legend: { display: false } } } },
            avgCheckChart: { type: 'line', options: { scales: { y: { grid: { display: false } }, x: { grid: { display: false } } } } },
            topServicesByRevenueChart: { type: 'bar', options: { indexAxis: 'y', scales: { x: { beginAtZero: true, grid: { display: true, color: '#e5e7eb' } }, y: { grid: { display: false } } }, plugins: { legend: { display: false } } } }
        };

        Object.keys(chartConfigs).forEach(id => {
            const canvas = document.getElementById(id);
            if (!canvas) {
                return;
            }
            
            const ctx = canvas.getContext('2d');
            charts[id] = new Chart(ctx, {
                type: chartConfigs[id].type,
                data: { labels: [], datasets: [] },
                options: chartConfigs[id].options,
            });
        });
    }

    // --- Функция для обновления данных на всех графиках ---
    async function updateClientAnalytics(period = 'week', params = null) {
        let url = '/reports/client-analytics';
        if (params) {
            url += '?' + params + '&_t=' + Date.now();
        } else {
            url += `?period=${period}&_t=${Date.now()}`;
        }
        try {
            const response = await fetch(url);

            if (!response.ok) {
                const errorText = await response.text();
                throw new Error(`Сетевой ответ не был успешным. Статус: ${response.status}. Ответ: ${errorText}`);
            }

            const data = await response.json();

            // 1. Динамика клиентской базы
            if (charts.clientDynamicsChart && data.clientDynamics) {

                const xTicks = charts.clientDynamicsChart.options.scales.x.ticks;
                
                const formatDate = (dateString) => {
                    const [year, month, day] = dateString.split('-');
                    return `${day}.${month}`;
                };

                if (period === 'month') {
                    xTicks.callback = function(value, index, ticks) {
                        const label = this.getLabelForValue(value);
                        if (!label) return '';
                        
                        const date = new Date(label);
                        if (date.getDay() === 0 || index === 0) {
                           return formatDate(label);
                        }
                        return '';
                    };
                    xTicks.autoSkip = false;
                } else {
                    xTicks.callback = function(value, index, ticks) {
                        const label = this.getLabelForValue(value);
                        if (label && /^\d{4}-\d{2}-\d{2}$/.test(label)) {
                            return formatDate(label);
                        }
                        return label;
                    };
                    xTicks.autoSkip = true;
                }

                if (data.clientDynamics.data && data.clientDynamics.data.length > 0) {
                    const maxValue = Math.max(...data.clientDynamics.data);
                    charts.clientDynamicsChart.options.scales.y.suggestedMax = maxValue + 1;
                } else {
                    delete charts.clientDynamicsChart.options.scales.y.suggestedMax;
                }
                const totalNew = data.clientDynamics.data.reduce((a, b) => a + b, 0);
                updateChart(charts.clientDynamicsChart, data.clientDynamics.labels, [{
                    label: `Новые клиенты (${totalNew})`,
                    data: data.clientDynamics.data,
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    fill: true,
                    tension: 0.4
                }]);
            }
            // 2. Распределение по типам клиентов
            if (charts.clientTypesChart && data.clientTypesDistribution) {
                const labelsWithCounts = data.clientTypesDistribution.labels.map((label, i) => `${label} (${data.clientTypesDistribution.data[i]})`);
                updateChart(charts.clientTypesChart, labelsWithCounts, [{
                    data: data.clientTypesDistribution.data,
                    backgroundColor: ['#7c3aed', '#c4b5fd', '#8b5cf6', '#a78bfa', '#6d28d9'],
                    hoverOffset: 4
                }]);
            }
            // 3. Новые vs. Повторные визиты
            if (charts.newVsReturningChart && data.newVsReturning) {
                const labelsWithCounts = data.newVsReturning.labels.map((label, i) => `${label} (${data.newVsReturning.data[i]})`);
                updateChart(charts.newVsReturningChart, labelsWithCounts, [{
                    data: data.newVsReturning.data,
                    backgroundColor: ['#f59e0b', '#fcd34d'],
                    hoverOffset: 4
                }]);
            }
            // 4. Топ-5 клиентов по визитам
            if (charts.topClientsByVisitsChart && data.topClientsByVisits) {
                if (data.topClientsByVisits.data && data.topClientsByVisits.data.length > 0) {
                    const maxValue = Math.max(...data.topClientsByVisits.data);
                    if (!charts.topClientsByVisitsChart.options.scales) charts.topClientsByVisitsChart.options.scales = {};
                    if (!charts.topClientsByVisitsChart.options.scales.y) charts.topClientsByVisitsChart.options.scales.y = {};
                    charts.topClientsByVisitsChart.options.scales.y.suggestedMax = maxValue + 1;
                    charts.topClientsByVisitsChart.options.scales.y.ticks = { precision: 0 };
                    const colors = data.topClientsByVisits.data.map(v => v === maxValue ? '#10b981' : 'rgba(234, 88, 12, 0.7)');
                    const labelsWithCounts = data.topClientsByVisits.labels.map((label, i) => `${label} (${data.topClientsByVisits.data[i]})`).slice();
                    const values = data.topClientsByVisits.data.slice();
                    updateChart(charts.topClientsByVisitsChart, [], []);
                    setTimeout(() => {
                        updateChart(charts.topClientsByVisitsChart, labelsWithCounts, [{
                            label: 'Рейтинг визитов',
                            data: values,
                            backgroundColor: colors.slice(),
                            borderColor: colors.slice(),
                            borderWidth: 1
                        }]);
                    }, 50);
                } else {
                    // Если данных нет — очищаем график полностью
                    updateChart(charts.topClientsByVisitsChart, [], []);
                }
            }
        } catch (error) {
            // console.error('Ошибка при загрузке аналитических данных:', error);
        }
    }

    // --- Функция для обновления данных на вкладке "Аналитика по записям" ---
    async function updateAppointmentsAnalytics(period = 'week', params = null) {
        let url = '/reports/appointments-by-day';
        if (params) {
            url += '?' + params;
        } else {
            url += `?period=${period}`;
        }
        if (!charts.loadChart) return;

        try {
            const response = await fetch(url);
            if (!response.ok) {
                throw new Error(`Сетевой ответ не был успешным. Статус: ${response.status}.`);
            }
            const data = await response.json();

            // Обновление графика "Загруженность по дням"
            const loadChart = charts.loadChart;

            // Логика форматирования меток оси X
            const xTicks = loadChart.options.scales.x.ticks;
            const formatDate = (dateString) => {
                const [year, month, day] = dateString.split('-');
                return `${day}.${month}`;
            };
            
            if (period === 'month') {
                xTicks.callback = function(value, index, ticks) {
                    const label = this.getLabelForValue(value);
                    if (!label) return '';
                    const date = new Date(label);
                    if (date.getDay() === 0 || index === 0) {
                       return formatDate(label);
                    }
                    return '';
                };
                xTicks.autoSkip = false;
            } else {
                xTicks.callback = function(value, index, ticks) {
                    const label = this.getLabelForValue(value);
                    if (label && /^\d{4}-\d{2}-\d{2}$/.test(label)) {
                        return formatDate(label);
                    }
                    return label;
                };
                xTicks.autoSkip = true;
            }

            if (data.data && data.data.length > 0) {
                const maxValue = Math.max(...data.data);
                if (!loadChart.options.scales) loadChart.options.scales = {};
                if (!loadChart.options.scales.y) loadChart.options.scales.y = {};
                loadChart.options.scales.y.suggestedMax = maxValue + 1;
                loadChart.options.scales.y.ticks = { precision: 0 };
            } else {
                delete loadChart.options.scales.y.suggestedMax;
            }
            updateChart(loadChart, data.labels, [{
                label: 'Записи',
                data: data.data,
                backgroundColor: 'rgba(59, 130, 246, 0.7)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 1
            }]);
        } catch (error) {
            // console.error('Ошибка при загрузке данных о загруженности:', error);
        }
    }

    // --- Функция для обновления графика статусов записей ---
    async function updateAppointmentStatusAnalytics(period = 'week', params = null) {
        let url = '/reports/appointment-status-data';
        if (params) {
            url += '?' + params;
        } else {
            url += `?period=${period}`;
        }
        if (!charts.statusChart) return;

        try {
            const response = await fetch(url);
            if (!response.ok) {
                throw new Error(`Сетевой ответ не был успешным. Статус: ${response.status}.`);
            }
            const data = await response.json();

            const labelsWithCounts = data.labels.map((label, i) => `${label} (${data.data[i]})`);
            
            updateChart(charts.statusChart, labelsWithCounts, [{
                data: data.data,
                backgroundColor: data.colors,
                hoverOffset: 4
            }]);
        } catch (error) {
            // console.error('Ошибка при загрузке данных о статусах записей:', error);
        }
    }

    // --- Функция для обновления популярности услуг ---
    async function updateServicePopularityAnalytics(period = 'week', params = null) {
        let url = '/reports/service-popularity-data';
        if (params) {
            url += '?' + params;
        } else {
            url += `?period=${period}`;
        }
        const servicesChart = charts.servicesChart;
        if (!servicesChart) return;

        try {
            const response = await fetch(url);
            if (!response.ok) {
                throw new Error(`Сетевой ответ не был успешным. Статус: ${response.status}.`);
            }
            const data = await response.json();
            
            // Формируем labels с числами в скобках
            const labelsWithCounts = data.labels.map((label, i) => `${label} (${data.data[i]})`);

            // Устанавливаем максимальное значение для шкалы X
            if (data.data && data.data.length > 0) {
                const maxValue = Math.max(...data.data);
                if (!servicesChart.options.scales) servicesChart.options.scales = {};
                if (!servicesChart.options.scales.x) servicesChart.options.scales.x = {};
                servicesChart.options.scales.x.suggestedMax = maxValue + 1;
                servicesChart.options.scales.x.ticks = { precision: 0 };
            } else {
                delete servicesChart.options.scales.x.suggestedMax;
            }

            const ctx = servicesChart.ctx;
            const gradient = ctx.createLinearGradient(0, 0, servicesChart.chartArea.right, 0);
            gradient.addColorStop(0, 'rgba(139, 92, 246, 0.9)');
            gradient.addColorStop(0.5, 'rgba(139, 92, 246, 0.6)');
            gradient.addColorStop(1, 'rgba(139, 92, 246, 0.4)');

            updateChart(servicesChart, labelsWithCounts, [{
                label: 'Количество записей',
                data: data.data,
                backgroundColor: gradient,
                borderColor: 'rgba(139, 92, 246, 0.3)',
                borderWidth: 1,
                borderRadius: 4,
                borderSkipped: false
            }]);
        } catch (error) {
            // console.error('Ошибка при загрузке данных о популярности услуг:', error);
        }
    }

    // --- Вспомогательная функция для обновления одного графика ---
    function updateChart(chart, labels, datasets) {
        // Если размеры ещё не рассчитаны — отложить обновление
        if (chart && chart.chartArea && (chart.chartArea.width === 0 || chart.chartArea.height === 0)) {
            setTimeout(() => updateChart(chart, labels, datasets), 120);
            return;
        }
        // Градиент для bar и line графиков
        if (chart && chart.config && chart.config.type) {
            const ctx = chart.ctx;
            const area = chart.chartArea;
            if (ctx && area && datasets.length > 0) {
                if (chart.config.type === 'bar') {
                    // Для topServicesByRevenueChart и servicesChart — фиолетовый градиент (перевёрнутый)
                    if ((chart.canvas && chart.canvas.id === 'topServicesByRevenueChart') || (chart.canvas && chart.canvas.id === 'servicesChart')) {
                        const grad = ctx.createLinearGradient(area.left, 0, area.right, 0);
                        grad.addColorStop(0, 'rgba(139,92,246,0.4)');
                        grad.addColorStop(0.5, 'rgba(139,92,246,0.6)');
                        grad.addColorStop(1, 'rgba(139,92,246,0.9)');
                        datasets[0].backgroundColor = grad;
                        datasets[0].borderColor = 'rgba(139,92,246,0.3)';
                        datasets[0].borderRadius = 4;
                        datasets[0].borderSkipped = false;
                    } else if (chart.options.indexAxis === 'y') {
                        // Для остальных горизонтальных bar — синий градиент (перевёрнутый)
                        const grad = ctx.createLinearGradient(area.left, 0, area.right, 0);
                        grad.addColorStop(0, 'rgba(59,130,246,0.2)');
                        grad.addColorStop(0.5, 'rgba(59,130,246,0.5)');
                        grad.addColorStop(1, 'rgba(59,130,246,0.9)');
                        datasets[0].backgroundColor = grad;
                        datasets[0].borderColor = 'rgba(59,130,246,0.3)';
                        datasets[0].borderRadius = 4;
                        datasets[0].borderSkipped = false;
                    } else {
                        // Для вертикальных bar — синий градиент (перевёрнутый)
                        const grad = ctx.createLinearGradient(0, area.bottom, 0, area.top);
                        grad.addColorStop(0, 'rgba(59,130,246,0.2)');
                        grad.addColorStop(0.5, 'rgba(59,130,246,0.5)');
                        grad.addColorStop(1, 'rgba(59,130,246,0.9)');
                        datasets[0].backgroundColor = grad;
                        datasets[0].borderColor = 'rgba(59,130,246,0.3)';
                        datasets[0].borderRadius = 4;
                        datasets[0].borderSkipped = false;
                    }
                } else if (chart.config.type === 'line') {
                    // Градиентная заливка под линией
                    const grad = ctx.createLinearGradient(0, area.bottom, 0, area.top);
                    grad.addColorStop(0, 'rgba(59,130,246,0.15)');
                    grad.addColorStop(1, 'rgba(59,130,246,0.01)');
                    datasets[0].backgroundColor = grad;
                    datasets[0].borderColor = 'rgb(59,130,246)';
                    datasets[0].pointBackgroundColor = 'rgb(59,130,246)';
                    datasets[0].pointRadius = 4;
                    datasets[0].pointHoverRadius = 6;
                    datasets[0].tension = 0.4;
                    datasets[0].fill = true;
                } else if (chart.config.type === 'pie' || chart.config.type === 'doughnut') {
                    // Современный стиль для pie/doughnut
                    // Цвета — только из данных, не трогаем
                    datasets[0].borderColor = 'rgba(255,255,255,0.7)';
                    datasets[0].borderWidth = 2;
                    datasets[0].hoverOffset = 12;
                    // Тень через plugin (Chart.js 3+)
                    chart.options.plugins.shadow = {
                        enabled: true,
                        color: 'rgba(59,130,246,0.18)',
                        blur: 12,
                        offsetX: 0,
                        offsetY: 4
                    };
                }
            }
        }
        // Принудительный сброс и обновление
        chart.data.labels = [];
        chart.data.datasets = [];
        chart.update();
        chart.data.labels = labels;
        chart.data.datasets = datasets;
        chart.update();
    }

    // --- Логика переключения вкладок и фильтров ---
    const mainTabs = document.querySelectorAll('.tab-button');
    const mainPanes = document.querySelectorAll('.tab-pane');

    mainTabs.forEach(tab => {
        tab.addEventListener('click', () => {
            mainTabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            const targetPaneId = tab.getAttribute('data-tab');
            mainPanes.forEach(pane => {
                pane.style.display = pane.id === targetPaneId ? 'block' : 'none';
            });
            // Получаем активный период
            const activeButton = document.querySelector('.filter-section .filter-button.active');
            const periodKey = activeButton.textContent.trim();
            const period = periodMapping[periodKey];
            // Принудительно обновляем данные для активной вкладки
            if (period) {
                 if (targetPaneId === 'clients-analytics') {
                    updateClientAnalytics(period);
                } else if (targetPaneId === 'appointments-analytics') {
                    updateAppointmentsAnalytics(period);
                    updateAppointmentStatusAnalytics(period);
                    updateServicePopularityAnalytics(period);
                } else if (targetPaneId === 'complex-analytics') {
                    updateTopClientsByRevenueAnalytics(period);
                    updateAvgCheckDynamicsAnalytics(period);
                    updateLtvByClientTypeAnalytics(period);
                    updateTopServicesByRevenueAnalytics(period);
                }
            }
            // ФИКС Chart.js: после показа вкладки пересчитываем размеры и обновляем все графики
            setTimeout(() => {
                Object.values(charts).forEach(chart => {
                    if (chart && chart.resize) chart.resize();
                    if (chart && chart.update) chart.update();
                });
            }, 120);
        });
    });

    const filterButtons = document.querySelectorAll('.filter-section .filter-button');
    const periodMapping = {
        'За неделю': 'week',
        'За 2 недели': '2weeks',
        'За месяц': 'month',
        'За полгода': 'half_year',
        'За год': 'year'
    };

    filterButtons.forEach(button => {
        button.addEventListener('click', () => {
            if (button.id === 'dateRangePicker') return; 

            // Сбросить отображение диапазона диапазона ДО обновления графиков
            calendarRangeDisplay.textContent = '';
            calendarRangeDisplay.style.minWidth = '';

            filterButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
            
            const periodKey = button.textContent.trim();
            const period = periodMapping[periodKey];
            if (period) {
                const activeTabId = document.querySelector('.tab-button.active').getAttribute('data-tab');
                // Всегда обновлять все графики для выбранной вкладки
                if (activeTabId === 'clients-analytics') {
                    updateClientAnalytics(period);
                } else if (activeTabId === 'appointments-analytics') {
                    updateAppointmentsAnalytics(period);
                    updateAppointmentStatusAnalytics(period);
                    updateServicePopularityAnalytics(period);
                } else if (activeTabId === 'complex-analytics') {
                    updateTopClientsByRevenueAnalytics(period);
                    updateAvgCheckDynamicsAnalytics(period);
                    updateLtvByClientTypeAnalytics(period);
                    updateTopServicesByRevenueAnalytics(period);
                }
                // УБРАН программный клик по вкладке
            }
        });
    });

    // --- Первоначальная загрузка для первой активной вкладки ---
    initializeCharts();
    // Сразу выбираем период 'За месяц'
    const filterButtonsArr = Array.from(document.querySelectorAll('.filter-section .filter-button'));
    const monthBtn = filterButtonsArr.find(btn => btn.textContent.trim() === 'За месяц');
    if (monthBtn) {
        filterButtonsArr.forEach(btn => btn.classList.remove('active'));
        monthBtn.classList.add('active');
    }
    const initialPeriod = 'month';
    // Определяем активную вкладку
    const initialTabId = document.querySelector('.tab-button.active').getAttribute('data-tab');
    if (initialTabId === 'clients-analytics') {
        updateClientAnalytics(initialPeriod);
    } else if (initialTabId === 'appointments-analytics') {
        updateAppointmentsAnalytics(initialPeriod);
        updateAppointmentStatusAnalytics(initialPeriod);
        updateServicePopularityAnalytics(initialPeriod);
    } else if (initialTabId === 'complex-analytics') {
        updateTopClientsByRevenueAnalytics(initialPeriod);
        updateAvgCheckDynamicsAnalytics(initialPeriod);
        updateLtvByClientTypeAnalytics(initialPeriod);
        updateTopServicesByRevenueAnalytics(initialPeriod);
    }

    // --- Графики для вкладки "Аналитика по записям" (статичные данные) ---
    // Загруженность по дням - теперь обновляется динамически

    // Статусы записей
    if (charts.statusChart) {
        // Этот блок теперь не нужен, так как данные загружаются динамически
        // updateChart(charts.statusChart,
        //     ['Выполнено', 'Отменено', 'Не пришел'],
        //     [{
        //         data: [250, 45, 15],
        //         backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
        //         hoverOffset: 4
        //     }]
        // );
    }
    // Популярность услуг
    if (charts.servicesChart) {
        // Этот блок теперь не нужен, так как данные загружаются динамически
        // updateChart(charts.servicesChart,
        //     ['Стрижка женская', 'Маникюр + Гель-лак', 'Окрашивание корней', 'Педикюр', 'Коррекция бровей', 'Укладка', 'Макияж'],
        //     [{
        //         label: 'Количество записей',
        //         data: [80, 75, 60, 50, 45, 30, 15],
        //         backgroundColor: 'rgba(139, 92, 246, 0.7)',
        //         borderColor: 'rgba(139, 92, 246, 1)',
        //         borderWidth: 1
        //     }]
        // );
    }

    // --- Функция для обновления топ-5 клиентов по выручке ---
    async function updateTopClientsByRevenueAnalytics(period = 'week', params = null) {
        let url = '/reports/top-clients-by-revenue';
        if (params) {
            url += '?' + params;
        } else {
            url += `?period=${period}`;
        }
        if (!charts.topClientsByRevenueChart) return;
        try {
            const response = await fetch(url);
            if (!response.ok) throw new Error('Ошибка сети');
            const data = await response.json();
            // labels с числом в скобках
            const labelsWithCounts = data.labels.map((label, i) => `${label} (${data.data[i]})`);
            // Цвета: максимальный — зелёный, остальные — оранжевый (как у топ-5 по визитам)
            const maxValue = data.data.length > 0 ? Math.max(...data.data) : 0;
            const colors = data.data.map(v => v === maxValue ? '#10b981' : 'rgba(234, 88, 12, 0.7)');
            updateChart(charts.topClientsByRevenueChart, labelsWithCounts, [{
                label: 'Выручка',
                data: data.data,
                backgroundColor: colors,
                borderColor: colors,
                borderWidth: 1
            }]);
        } catch (e) { // console.error('Ошибка загрузки топ-5 по выручке', e); }
    }
    // --- Функция для обновления динамики среднего чека ---
    async function updateAvgCheckDynamicsAnalytics(period = 'week', params = null) {
        let url = '/reports/avg-check-dynamics';
        if (params) {
            url += '?' + params;
        } else {
            url += `?period=${period}`;
        }
        if (!charts.avgCheckChart) return;
        try {
            const response = await fetch(url);
            if (!response.ok) throw new Error('Ошибка сети');
            const data = await response.json();
            updateChart(charts.avgCheckChart, data.labels, [{
                label: 'Средний чек',
                data: data.data
            }]);
        } catch (e) { // console.error('Ошибка загрузки динамики среднего чека', e); }
    }
    // --- Функция для обновления LTV по типам клиентов ---
    async function updateLtvByClientTypeAnalytics(period = 'week', params = null) {
        let url = '/reports/ltv-by-client-type';
        if (params) {
            url += '?' + params;
        } else {
            url += `?period=${period}`;
        }
        if (!charts.ltvChart) return;
        try {
            const response = await fetch(url);
            if (!response.ok) throw new Error('Ошибка сети');
            const data = await response.json();
            // Формируем labels с числами в скобках
            const labelsWithCounts = data.labels.map((label, i) => `${label} (${data.data[i]})`);
            updateChart(charts.ltvChart, labelsWithCounts, [{
                label: 'LTV',
                data: data.data
            }]);
        } catch (e) { // console.error('Ошибка загрузки LTV', e); }
    }
    // --- Функция для обновления топ-услуг по выручке ---
    async function updateTopServicesByRevenueAnalytics(period = 'week', params = null) {
        let url = '/reports/top-services-by-revenue';
        if (params) {
            url += '?' + params;
        } else {
            url += `?period=${period}`;
        }
        if (!charts.topServicesByRevenueChart) return;
        try {
            const response = await fetch(url);
            if (!response.ok) throw new Error('Ошибка сети');
            const data = await response.json();
            // Формируем labels с числами в скобках
            const labelsWithCounts = data.labels.map((label, i) => `${label} (${data.data[i]})`);
            updateChart(charts.topServicesByRevenueChart, labelsWithCounts, [{
                label: 'Выручка',
                data: data.data
            }]);
        } catch (e) { // console.error('Ошибка загрузки топ-услуг по выручке', e); }
    }

    // --- Логика для календаря ---
    const calendarBtn = document.getElementById('dateRangePicker');
    const calendarRangeDisplay = document.getElementById('calendarRangeDisplay');
    let calendarInstance = null;
    let selectedRange = null;
    calendarBtn.addEventListener('click', function(e) {
        if (!calendarInstance) {
            calendarInstance = flatpickr(calendarBtn, {
                mode: 'range',
                dateFormat: 'Y-m-d',
                locale: 'ru',
                onClose: function(selectedDates, dateStr) {
                    if (selectedDates.length === 2) {
                        selectedRange = {
                            start: selectedDates[0],
                            end: selectedDates[1]
                        };
                        filterButtons.forEach(btn => btn.classList.remove('active'));
                        calendarBtn.classList.add('active');
                        // Отображаем выбранный диапазон
                        const format = d => d.toLocaleDateString('ru-RU', { day: '2-digit', month: '2-digit' });
                        calendarRangeDisplay.textContent = `${format(selectedRange.start)} – ${format(selectedRange.end)}`;
                        calendarRangeDisplay.style.minWidth = '110px';
                        updateAllChartsWithRange(selectedRange.start, selectedRange.end);
                    }
                }
            });
        }
        calendarInstance.open();
    });

    // При выборе фиксированного периода очищаем отображение диапазона
    filterButtons.forEach(button => {
        button.addEventListener('click', () => {
            if (button.id !== 'dateRangePicker') {
                calendarRangeDisplay.textContent = '';
                calendarRangeDisplay.style.minWidth = '';
            }
        });
    });

    function updateAllChartsWithRange(startDate, endDate) {
        // Форматируем даты строго как локальные YYYY-MM-DD
        const format = d => `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
        const params = `start_date=${format(startDate)}&end_date=${format(endDate)}`;
        // Для каждой функции обновления графиков передаём диапазон
        updateClientAnalytics(null, params);
        updateAppointmentsAnalytics(null, params);
        updateAppointmentStatusAnalytics(null, params);
        updateServicePopularityAnalytics(null, params);
        updateTopClientsByRevenueAnalytics(null, params);
        updateAvgCheckDynamicsAnalytics(null, params);
        updateLtvByClientTypeAnalytics(null, params);
        updateTopServicesByRevenueAnalytics(null, params);
    }
});

// --- Chart.js shadow plugin ---
Chart.register({
    id: 'shadow',
    beforeDraw: function(chart) {
        if (chart.options.plugins && chart.options.plugins.shadow && chart.options.plugins.shadow.enabled) {
            const ctx = chart.ctx;
            ctx.save();
            ctx.shadowColor = chart.options.plugins.shadow.color || 'rgba(0,0,0,0.15)';
            ctx.shadowBlur = chart.options.plugins.shadow.blur || 10;
            ctx.shadowOffsetX = chart.options.plugins.shadow.offsetX || 0;
            ctx.shadowOffsetY = chart.options.plugins.shadow.offsetY || 4;
        }
    },
    afterDraw: function(chart) {
        if (chart.options.plugins && chart.options.plugins.shadow && chart.options.plugins.shadow.enabled) {
            const ctx = chart.ctx;
            ctx.restore();
        }
    }
});
</script>
@endpush 
