@extends('client.layouts.app')

@section('content')
<div class="dashboard-container">
    <div class="report-header">
        <h1 class="dashboard-title">Аналитика по товарообороту</h1>

        <!-- Навигация по вкладкам -->
        <div class="dashboard-tabs">
            <button class="tab-button active" data-tab="dynamic-analytics"><i class="fa fa-line-chart"></i> Динамика и структура</button>
            <button class="tab-button" data-tab="tops-analytics"><i class="fa fa-star"></i> Топы</button>
            <button class="tab-button" data-tab="suppliers-analytics"><i class="fa fa-truck"></i> Поставщики и остатки</button>
        </div>

        <!-- Фильтры периода -->
        <div class="filter-section">
            <div class="period-filters" style="display:flex;align-items:center;gap:8px;">
                <button class="filter-button active">За неделю</button>
                <button class="filter-button">За 2 недели</button>
                <button class="filter-button">За месяц</button>
                <button class="filter-button">За полгода</button>
                <button class="filter-button">За год</button>
                <button class="filter-button calendar-button" id="dateRangePicker">
                    <i class="fa fa-calendar"></i>
                </button>
                <span id="calendarRangeDisplay" style="min-width:110px;text-align:center;vertical-align:middle;font-family:inherit;font-size:14px;font-weight:600;color: #64748b;"></span>
            </div>
        </div>
    </div>

    <div class="tab-content">
        <div class="tab-pane active" id="dynamic-analytics">
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">Структура продаж по категориям</h4>
                        <p class="text-muted">Распределение по категориям.</p>
                        <canvas id="turnoverCategoryPie"></canvas>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">Структура продаж по брендам</h4>
                        <p class="text-muted">Распределение по брендам.</p>
                        <canvas id="turnoverBrandPie"></canvas>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">Структура по поставщикам</h4>
                        <p class="text-muted">Доля поставщиков в закупках.</p>
                        <canvas id="turnoverSupplierPie"></canvas>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">Структура по типам товаров</h4>
                        <p class="text-muted">Распределение услуги/товары.</p>
                        <canvas id="turnoverTypePie"></canvas>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">Динамика товарооборота</h4>
                        <p class="text-muted">Общий объем продаж и закупок по дням.</p>
                        <canvas id="turnoverDynamicChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="tops-analytics" style="display: none;">
            <div class="row">
                <div class="col-lg-12 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">Топ-5 товаров по продажам</h4>
                        <p class="text-muted">Самые продаваемые товары за период.</p>
                        <canvas id="topSalesBar"></canvas>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">Топ-5 товаров по закупкам</h4>
                        <p class="text-muted">Товары с наибольшим объемом закупок.</p>
                        <canvas id="topPurchasesBar"></canvas>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">Топ-5 клиентов по объёму покупок товаров</h4>
                        <p class="text-muted">Клиенты, купившие товаров наибольшую сумму за период.</p>
                        <canvas id="topClientsBySalesBar"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="suppliers-analytics" style="display: none;">
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">Средний срок оборачиваемости</h4>
                        <p class="text-muted">Среднее время между закупкой и продажей товара.</p>
                        <canvas id="turnoverDaysChart"></canvas>
                        <div id="avgTurnoverDaysValue" style="font-size:1.2rem;font-weight:600;margin-top:12px;"></div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">Товары с максимальным сроком без продажи</h4>
                        <p class="text-muted">Товары, которые не продавались дольше всего (залежалые остатки).</p>
                        <canvas id="slowMovingProductsBar"></canvas>
                        <div class="no-data" id="slowMovingProductsNoData" style="display:none;text-align:center;color:#888;font-size:1.1rem;padding:32px 0;">Нет данных для отображения</div>
                    </div>
                </div>
            </div>
            <div class="row align-items-center mb-3">
                <div class="col-lg-6 mb-2">
                    <div class="stat-card" style="background:#f3f4f6;padding:18px 24px;border-radius:10px;font-size:1.1rem;font-weight:600;">
                        <span>Общая сумма опта на складе: </span><span id="stockTotalWholesale">—</span> грн
                    </div>
                </div>
                <div class="col-lg-6 mb-2">
                    <div class="stat-card" style="background:#f3f4f6;padding:18px 24px;border-radius:10px;font-size:1.1rem;font-weight:600;">
                        <span>Общая сумма розници на складе: </span><span id="stockTotalRetail">—</span> грн
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">Остатки на складе по категориям</h4>
                        <p class="text-muted">Количество и сумма остатков по оптовой и розничной цене для каждой категории товаров.</p>
                        <canvas id="stockByCategoryBar"></canvas>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">Поставщики по объёму закупок</h4>
                        <p class="text-muted">Поставщики с наибольшим объемом закупок.</p>
                        <canvas id="topSuppliersBar"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    var charts = {};
    // --- Функция для вычисления диапазона дат по календарным периодам ---
    function getPeriodParams(period) {
        const end = new Date();
        let start;
        switch (period) {
            case 'За неделю':
                start = new Date(end);
                start.setDate(end.getDate() - ((end.getDay() + 6) % 7)); // последний понедельник
                break;
            case 'За 2 недели':
                start = new Date(end);
                start.setDate(end.getDate() - ((end.getDay() + 6) % 7) - 7); // предпоследний понедельник
                break;
            case 'За месяц':
                start = new Date(end.getFullYear(), end.getMonth(), 1);
                break;
            case 'За полгода':
                start = new Date(end.getFullYear(), end.getMonth() - 5, 1);
                break;
            case 'За год':
                start = new Date(end.getFullYear(), end.getMonth() - 11, 1);
                break;
            default:
                start = new Date(end.getFullYear(), end.getMonth(), 1);
        }
        const format = d => d.toISOString().slice(0, 10);
        return `start_date=${format(start)}&end_date=${format(end)}`;
    }

    document.addEventListener('DOMContentLoaded', function () {
        // --- Удалён первый fetch('/reports/turnover-analytics') ---
        // --- Инициализация при загрузке (по умолчанию за месяц) ---
        // Снимаем выделение со всех кнопок и выделяем 'За месяц'
        const filterButtonsArr = Array.from(document.querySelectorAll('.filter-section .filter-button'));
        const monthBtn = filterButtonsArr.find(btn => btn.textContent.trim() === 'За месяц');
        if (monthBtn) {
            filterButtonsArr.forEach(btn => btn.classList.remove('active'));
            monthBtn.classList.add('active');
        }
        // Формируем параметры для месяца
        const params = getPeriodParams('За месяц');
        updateTurnoverAnalytics(params);

        if (document.getElementById('stockTotalWholesale')) {
            document.getElementById('stockTotalWholesale').textContent = stockTotalWholesale.toLocaleString('ru-RU');
        }
        if (document.getElementById('stockTotalRetail')) {
            document.getElementById('stockTotalRetail').textContent = stockTotalRetail.toLocaleString('ru-RU');
        }
    });

    // Переключение вкладок
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
            setTimeout(() => {
                Object.values(charts).forEach(chart => {
                    if (chart && chart.resize) chart.resize();
                });
            }, 120);
            // Если выбрана вкладка Топы — обновляем топы
            if (targetPaneId === 'tops-analytics') {
                // Определяем активный период
                const activeBtn = document.querySelector('.filter-section .filter-button.active');
                let params = '';
                if (activeBtn) {
                    const period = activeBtn.textContent.trim();
                    params = getPeriodParams(period);
                }
                // Если выбран календарь — берём даты из calendarRangeDisplay
                if (activeBtn && activeBtn.id === 'dateRangePicker' && window.selectedRange) {
                    const formatISO = d => d.toISOString().slice(0, 10);
                    params = `start_date=${formatISO(window.selectedRange.start)}&end_date=${formatISO(window.selectedRange.end)}`;
                }
                updateTopsAnalytics(params);
            }
            // Если выбрана вкладка Поставщики — обновляем аналитику
            if (targetPaneId === 'suppliers-analytics') {
                // Определяем активный период
                const activeBtn = document.querySelector('.filter-section .filter-button.active');
                let params = '';
                if (activeBtn) {
                    const period = activeBtn.textContent.trim();
                    params = getPeriodParams(period);
                }
                // Если выбран календарь — берём даты из calendarRangeDisplay
                if (activeBtn && activeBtn.id === 'dateRangePicker' && window.selectedRange) {
                    const formatISO = d => d.toISOString().slice(0,10);
                    params = `start_date=${formatISO(window.selectedRange.start)}&end_date=${formatISO(window.selectedRange.end)}`;
                }
                updateSuppliersAnalytics(params);
            }
        });
    });

    // --- Функция для обновления графиков по периоду ---
    function updateTurnoverAnalytics(params = '') {
        let url = '/reports/turnover-analytics';
        if (params) url += '?' + params;
        fetch(url)
            .then(response => response.json())
            .then(data => {
                // --- (тот же код инициализации графиков, как выше) ---
                const dynamicConfig = {
                    type: 'line',
                    data: {
                        labels: data.dynamic.labels,
                        datasets: [
                            {
                                label: 'Продажи',
                                data: data.dynamic.sales,
                                borderColor: 'rgb(59, 130, 246)',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                fill: true,
                                tension: 0.4
                            },
                            {
                                label: 'Закупки',
                                data: data.dynamic.purchases,
                                borderColor: 'rgb(16, 185, 129)',
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                fill: true,
                                tension: 0.4
                            }
                        ]
                    },
                    options: {
                        scales: {
                            y: {beginAtZero: true, grid: {display: true, color: '#e5e7eb'}},
                            x: {
                                grid: {display: false},
                                ticks: {
                                    callback: function (value, index, values) {
                                        const label = this.getLabelForValue(value);
                                        // Определяем период по длине массива
                                        const total = values.length;
                                        // Форматируем дату
                                        const date = new Date(label);
                                        const day = date.getDate().toString().padStart(2, '0');
                                        const month = date.toLocaleString('ru-RU', {month: 'short'});
                                        // За неделю/2 недели — каждый день
                                        if (total <= 14) {
                                            return `${day} ${month}`;
                                        }
                                        // За месяц/полгода — только начало недели (Пн)
                                        if (total <= 31 * 2) {
                                            if (date.getDay() === 1 || index === 0) {
                                                return `Пн ${day} ${month}`;
                                            }
                                            return '';
                                        }
                                        // За год — только первый день месяца
                                        if (date.getDate() === 1) {
                                            return month;
                                        }
                                        return '';
                                    }
                                }
                            }
                        }
                    }
                };
                // Категории
                const strictPalette = [
                    '#2563eb', // синий
                    '#10b981', // зелёный
                    '#f59e42', // оранжевый
                    '#7c3aed', // фиолетовый
                    '#ef4444', // красный
                    '#0ea5e9', // голубой
                    '#fbbf24', // жёлтый
                    '#6366f1', // индиго
                    '#dc2626', // бордовый
                    '#059669', // тёмно-зелёный
                    '#eab308', // золотой
                    '#334155', // графит
                ];

                function generatePieColors(count) {
                    return Array.from({length: count}, (_, i) => strictPalette[i % strictPalette.length]);
                }

                const pieOptions = {
                    borderColor: 'rgba(255,255,255,0.7)',
                    borderWidth: 2,
                    hoverOffset: 12,
                    plugins: {
                        shadow: {
                            enabled: true,
                            color: 'rgba(59,130,246,0.18)',
                            blur: 12,
                            offsetX: 0,
                            offsetY: 4
                        }
                    }
                };
                const categoryConfig = {
                    type: 'pie',
                    data: {
                        labels: data.category.labels,
                        datasets: [{
                            data: data.category.data,
                            backgroundColor: generatePieColors(data.category.labels.length),
                            ...pieOptions
                        }]
                    },
                    options: {
                        ...pieOptions,
                        plugins: {
                            ...pieOptions.plugins,
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        const i = context.dataIndex;
                                        const label = data.category.labels[i];
                                        const count = data.category.data[i];
                                        const sum = data.category.sums[i];
                                        return [
                                            `${label}`,
                                            `Количество: ${count} шт`,
                                            `Сумма: ${sum.toLocaleString('ru-RU')} грн`
                                        ];
                                    }
                                }
                            }
                        }
                    }
                };
                // Бренды
                const brandConfig = {
                    type: 'pie',
                    data: {
                        labels: data.brand.labels,
                        datasets: [{
                            data: data.brand.data,
                            backgroundColor: generatePieColors(data.brand.labels.length),
                            ...pieOptions
                        }]
                    },
                    options: {
                        ...pieOptions,
                        plugins: {
                            ...pieOptions.plugins,
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        const i = context.dataIndex;
                                        const label = data.brand.labels[i];
                                        const count = data.brand.data[i];
                                        const sum = data.brand.sums[i];
                                        return [
                                            `${label}`,
                                            `Количество: ${count} шт`,
                                            `Сумма: ${sum.toLocaleString('ru-RU')} грн`
                                        ];
                                    }
                                }
                            }
                        }
                    }
                };
                // Поставщики
                const supplierConfig = {
                    type: 'pie',
                    data: {
                        labels: data.supplier.labels,
                        datasets: [{
                            data: data.supplier.data,
                            backgroundColor: generatePieColors(data.supplier.labels.length),
                            ...pieOptions
                        }]
                    },
                    options: {
                        ...pieOptions,
                        plugins: {
                            ...pieOptions.plugins,
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        const i = context.dataIndex;
                                        const label = data.supplier.labels[i];
                                        const sum = data.supplier.data[i];
                                        return [
                                            `${label}`,
                                            `Сумма: ${sum.toLocaleString('ru-RU')} грн`
                                        ];
                                    }
                                }
                            }
                        }
                    }
                };
                // Типы
                const typeConfig = {
                    type: 'pie',
                    data: {
                        labels: data.type.labels,
                        datasets: [{
                            data: data.type.data,
                            backgroundColor: generatePieColors(data.type.labels.length),
                            ...pieOptions
                        }]
                    },
                    options: {
                        ...pieOptions,
                        plugins: {
                            ...pieOptions.plugins,
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        const i = context.dataIndex;
                                        const label = data.type.labels[i];
                                        const count = data.type.data[i];
                                        const sum = data.type.sums[i];
                                        return [
                                            `${label}`,
                                            `Количество: ${count} шт`,
                                            `Сумма: ${sum.toLocaleString('ru-RU')} грн`
                                        ];
                                    }
                                }
                            }
                        }
                    }
                };
                // Инициализация графиков
                const chartMap = [
                    {id: 'turnoverDynamicChart', config: dynamicConfig},
                    {id: 'turnoverCategoryPie', config: categoryConfig},
                    {id: 'turnoverBrandPie', config: brandConfig},
                    {id: 'turnoverSupplierPie', config: supplierConfig},
                    {id: 'turnoverTypePie', config: typeConfig},
                ];
                chartMap.forEach(({id, config}) => {
                    const canvas = document.getElementById(id);
                    if (!canvas) return;
                    const ctx = canvas.getContext('2d');
                    if (charts[id]) {
                        charts[id].destroy();
                    }
                    charts[id] = new Chart(ctx, config);
                });
            });
        }

    // --- Функция для обновления графиков Топы ---
    function updateTopsAnalytics(params = '') {
        let url = '/reports/turnover-tops';
        if (params) url += '?' + params;
        fetch(url)
            .then(response => response.json())
            .then(data => {
                // Цвета для bar-графиков
                const barColor = 'rgba(59, 130, 246, 0.7)';
                const barColor2 = 'rgba(16, 185, 129, 0.7)';
                // Топ-6 товаров по продажам
                if (charts.topSalesBar) charts.topSalesBar.destroy();
                const topSalesBar = document.getElementById('topSalesBar');
                if (topSalesBar) {
                    const maxValue = data.topSales.data.length > 0 ? Math.max(...data.topSales.data) : 0;
                    const ctx = topSalesBar.getContext('2d');
                    const area = {left: 0, right: topSalesBar.width};
                    const grad = ctx.createLinearGradient(area.left, 0, area.right, 0);
                    grad.addColorStop(0, 'rgba(59,130,246,0.2)');
                    grad.addColorStop(0.5, 'rgba(59,130,246,0.5)');
                    grad.addColorStop(1, 'rgba(59,130,246,0.9)');
                    charts.topSalesBar = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: data.topSales.labels,
                            datasets: [{
                                label: 'Продажи, шт',
                                data: data.topSales.data,
                                backgroundColor: grad,
                                borderColor: 'rgba(59,130,246,0.3)',
                                borderRadius: 8,
                                borderSkipped: false
                            }]
                        },
                        options: {
                            indexAxis: 'y',
                            plugins: { legend: { display: false } },
                            scales: {
                                x: {
                                    beginAtZero: true,
                                    suggestedMax: maxValue + 1,
                                    grid: { display: true, color: '#e5e7eb' },
                                    ticks: {
                                        callback: function(value) { return Number.isInteger(value) ? value : ''; },
                                        stepSize: 1,
                                        padding: 8
                                    }
                                },
                                y: {
                                    grid: { display: false },
                                    ticks: {
                                        callback: function(value) {
                                            const label = this.getLabelForValue(value);
                                            return label.length > 15 ? label.slice(0, 15) + '...' : label;
                                        },
                                        padding: 8
                                    }
                                }
                            }
                        }
                    });
                }
                // Топ-6 товаров по закупкам
                if (charts.topPurchasesBar) charts.topPurchasesBar.destroy();
                const topPurchasesBar = document.getElementById('topPurchasesBar');
                if (topPurchasesBar) {
                    const maxValue = data.topPurchases.data.length > 0 ? Math.max(...data.topPurchases.data) : 0;
                    const ctx = topPurchasesBar.getContext('2d');
                    const area = {left: 0, right: topPurchasesBar.width};
                    const grad = ctx.createLinearGradient(area.left, 0, area.right, 0);
                    grad.addColorStop(0, 'rgba(16,185,129,0.2)');
                    grad.addColorStop(0.5, 'rgba(16,185,129,0.5)');
                    grad.addColorStop(1, 'rgba(16,185,129,0.9)');
                    charts.topPurchasesBar = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: data.topPurchases.labels,
                            datasets: [{
                                label: 'Закупки, шт',
                                data: data.topPurchases.data,
                                backgroundColor: grad,
                                borderColor: 'rgba(16,185,129,0.3)',
                                borderRadius: 8,
                                borderSkipped: false
                            }]
                        },
                        options: {
                            indexAxis: 'y',
                            plugins: { legend: { display: false } },
                            scales: {
                                x: {
                                    beginAtZero: true,
                                    suggestedMax: maxValue + 1,
                                    grid: { display: true, color: '#e5e7eb' },
                                    ticks: {
                                        callback: function(value) { return Number.isInteger(value) ? value : ''; },
                                        stepSize: 1,
                                        padding: 8
                                    }
                                },
                                y: {
                                    grid: { display: false },
                                    ticks: {
                                        callback: function(value) {
                                            const label = this.getLabelForValue(value);
                                            return label.length > 15 ? label.slice(0, 15) + '...' : label;
                                        },
                                        padding: 8
                                    }
                                }
                            }
                        }
                    });
                }
                // Топ-6 клиентов по объёму покупок
                if (charts.topClientsBySalesBar) charts.topClientsBySalesBar.destroy();
                const topClientsBySalesBar = document.getElementById('topClientsBySalesBar');
                if (topClientsBySalesBar) {
                    const maxValue = data.topClients.data.length > 0 ? Math.max(...data.topClients.data) : 0;
                    const ctx = topClientsBySalesBar.getContext('2d');
                    const area = {left: 0, right: topClientsBySalesBar.width};
                    const grad = ctx.createLinearGradient(0, topClientsBySalesBar.height, 0, 0);
                    grad.addColorStop(0, 'rgba(245,158,11,0.4)');
                    grad.addColorStop(0.5, 'rgba(245,158,11,0.7)');
                    grad.addColorStop(1, 'rgba(245,158,11,1)');
                    // Добавляем отступ сверху: maxValue * 1.15, округлить вверх
                    const yMax = maxValue > 0 ? Math.ceil(maxValue * 1.15) : undefined;
                    charts.topClientsBySalesBar = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: data.topClients.labels,
                            datasets: [{
                                label: 'Покупки, грн',
                                data: data.topClients.data,
                                backgroundColor: grad,
                                borderColor: 'rgba(245,158,11,0.3)',
                                borderRadius: 8,
                                borderSkipped: false
                            }]
                        },
                        options: {
                            plugins: { legend: { display: false } },
                            scales: {
                                x: {
                                    grid: { display: false },
                                    ticks: {
                                        callback: function(value) {
                                            const label = this.getLabelForValue(value);
                                            return label.length > 15 ? label.slice(0, 15) + '...' : label;
                                        },
                                        padding: 8
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    suggestedMax: yMax,
                                    max: yMax,
                                    grid: { display: true, color: '#e5e7eb' },
                                    ticks: {
                                        callback: function(value) { return Number.isInteger(value) ? value : ''; },
                                        stepSize: 300,
                                        padding: 8
                                    }
                                }
                            }
                        }
                    });
                }
            });
    }

    // --- Обработка фильтров периода ---
    const filterButtons = document.querySelectorAll('.filter-section .filter-button');
    const periodMapping = {
        'За неделю': 7,
        'За 2 недели': 14,
        'За месяц': 30,
        'За полгода': 182,
        'За год': 365
    };
    filterButtons.forEach(button => {
        button.addEventListener('click', () => {
            if (button.id === 'dateRangePicker') return;
            filterButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
            // Очищаем отображение диапазона дат при выборе любого периода
            if (calendarRangeDisplay) calendarRangeDisplay.textContent = '';
            // Определяем период
            const period = button.textContent.trim();
            const params = getPeriodParams(period);
            updateTurnoverAnalytics(params);
            // Если активна вкладка Топы — обновляем топы
            const activeTab = document.querySelector('.tab-button.active');
            if (activeTab && activeTab.getAttribute('data-tab') === 'tops-analytics') {
                updateTopsAnalytics(params);
            }
        });
    });
    // Flatpickr календарь
    const calendarBtn = document.getElementById('dateRangePicker');
    const calendarRangeDisplay = document.getElementById('calendarRangeDisplay');
    let calendarInstance = null;
    calendarBtn.addEventListener('click', function (e) {
        if (!calendarInstance) {
            calendarInstance = flatpickr(calendarBtn, {
                mode: 'range',
                dateFormat: 'Y-m-d',
                locale: 'ru',
                onClose: function (selectedDates, dateStr) {
                    if (selectedDates.length === 2) {
                        filterButtons.forEach(btn => btn.classList.remove('active'));
                        calendarBtn.classList.add('active');
                        const format = d => d.toLocaleDateString('ru-RU', {day: '2-digit', month: '2-digit'});
                        calendarRangeDisplay.textContent = `${format(selectedDates[0])} — ${format(selectedDates[1])}`;
                        // Форматируем для запроса
                        const formatISO = d => d.toISOString().slice(0, 10);
                        const params = `start_date=${formatISO(selectedDates[0])}&end_date=${formatISO(selectedDates[1])}`;
                        updateTurnoverAnalytics(params);
                        window.selectedRange = {start: selectedDates[0], end: selectedDates[1]};
                        // Если активна вкладка Топы — обновляем топы
                        const activeTab = document.querySelector('.tab-button.active');
                        if (activeTab && activeTab.getAttribute('data-tab') === 'tops-analytics') {
                            const formatISO = d => d.toISOString().slice(0, 10);
                            const params = `start_date=${formatISO(selectedDates[0])}&end_date=${formatISO(selectedDates[1])}`;
                            updateTopsAnalytics(params);
                        }
                    }
                }
            });
        }
        calendarInstance.open();
    });

    function updateSuppliersAnalytics(params = '') {
        let url = '/reports/suppliers-analytics';
        if (params) url += '?' + params;
        fetch(url)
            .then(response => response.json())
            .then(data => {
                // --- Топ-6 поставщиков по объёму закупок ---
                if (charts.topSuppliersBar) charts.topSuppliersBar.destroy();
                const topSuppliersBar = document.getElementById('topSuppliersBar');
                if (topSuppliersBar) {
                    const maxValue = data.topSuppliers.length > 0 ? Math.max(...data.topSuppliers.map(s => s.sum)) : 0;
                    const step = maxValue > 20000 ? 5000 : 3000;
                    const ctx = topSuppliersBar.getContext('2d');
                    const area = {left: 0, right: topSuppliersBar.width};
                    const grad = ctx.createLinearGradient(area.left, 0, area.right, 0);
                    grad.addColorStop(0, 'rgba(139,92,246,0.4)');
                    grad.addColorStop(0.5, 'rgba(139,92,246,0.7)');
                    grad.addColorStop(1, 'rgba(139,92,246,1)');
                    charts.topSuppliersBar = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: data.topSuppliers.map(s => s.label),
                            datasets: [{
                                label: 'Закупки, грн',
                                data: data.topSuppliers.map(s => s.sum),
                                backgroundColor: grad,
                                borderColor: 'rgba(139,92,246,0.3)',
                                borderRadius: 8,
                                borderSkipped: false
                            }]
                        },
                        options: {
                            indexAxis: 'y',
                            plugins: { legend: { display: false } },
                            scales: {
                                x: {
                                    beginAtZero: true,
                                    suggestedMax: maxValue + step,
                                    grid: { display: true, color: '#e5e7eb' },
                                    ticks: {
                                        callback: function(value) { return Number.isInteger(value) ? value : ''; },
                                        stepSize: step,
                                        padding: 8
                                    }
                                },
                                y: {
                                    grid: { display: false },
                                    ticks: {
                                        callback: function(value) {
                                            const label = this.getLabelForValue(value);
                                            return label.length > 15 ? label.slice(0, 15) + '...' : label;
                                        },
                                        padding: 8
                                    }
                                }
                            }
                        }
                    });
                }
                // --- Остатки по категориям ---
                if (charts.stockByCategoryBar) charts.stockByCategoryBar.destroy();
                const stockByCategoryBar = document.getElementById('stockByCategoryBar');
                if (stockByCategoryBar) {
                    const maxValue = data.stockByCategory.length > 0 ? Math.max(...data.stockByCategory.map(c => c.qty)) : 0;
                    const ctx = stockByCategoryBar.getContext('2d');
                    const area = {left: 0, right: stockByCategoryBar.width};
                    const grad = ctx.createLinearGradient(0, stockByCategoryBar.height, 0, 0);
                    grad.addColorStop(0, 'rgba(59,130,246,0.2)');
                    grad.addColorStop(0.5, 'rgba(59,130,246,0.5)');
                    grad.addColorStop(1, 'rgba(59,130,246,0.9)');
                    // Добавляем отступ сверху: maxValue * 1.15, округлить вверх
                    const yMax = maxValue > 0 ? Math.ceil(maxValue * 1.15) : undefined;
                    charts.stockByCategoryBar = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: data.stockByCategory.map(c => c.label),
                            datasets: [{
                                label: 'Остаток, шт',
                                data: data.stockByCategory.map(c => c.qty),
                                backgroundColor: grad,
                                borderColor: 'rgba(59,130,246,0.3)',
                                borderRadius: 8,
                                borderSkipped: false
                            }]
                        },
                        options: {
                            plugins: { legend: { display: false } },
                            scales: {
                                x: {
                                    grid: { display: false },
                                    ticks: {
                                        callback: function(value) {
                                            const label = this.getLabelForValue(value);
                                            return label.length > 15 ? label.slice(0, 15) + '...' : label;
                                        },
                                        padding: 8
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    suggestedMax: yMax,
                                    max: yMax,
                                    grid: { display: true, color: '#e5e7eb' },
                                    ticks: {
                                        callback: function(value) { return Number.isInteger(value) ? value : ''; },
                                        stepSize: 1,
                                        padding: 8
                                    }
                                }
                            }
                        }
                    });
                }
                // --- Общая сумма остатков ---
                if (document.getElementById('stockTotalWholesale')) {
                    document.getElementById('stockTotalWholesale').textContent = data.stockTotalWholesale.toLocaleString('ru-RU');
                }
                if (document.getElementById('stockTotalRetail')) {
                    document.getElementById('stockTotalRetail').textContent = data.stockTotalRetail.toLocaleString('ru-RU');
                }
                // --- Топ-6 самых залежалых товаров ---
                if (charts.slowMovingProductsBar) charts.slowMovingProductsBar.destroy();
                const slowMovingProductsBar = document.getElementById('slowMovingProductsBar');
                const slowMovingProductsNoData = document.getElementById('slowMovingProductsNoData');
                if (slowMovingProductsBar) {
                    if (data.slowMovingProducts.length === 0) {
                        slowMovingProductsBar.style.display = 'none';
                        if (slowMovingProductsNoData) slowMovingProductsNoData.style.display = 'block';
                    } else {
                        slowMovingProductsBar.style.display = 'block';
                        if (slowMovingProductsNoData) slowMovingProductsNoData.style.display = 'none';
                        const maxValue = Math.max(...data.slowMovingProducts.map(p => p.days));
                        const ctx = slowMovingProductsBar.getContext('2d');
                        const area = {left: 0, right: slowMovingProductsBar.width};
                        const grad = ctx.createLinearGradient(area.left, 0, area.right, 0);
                        grad.addColorStop(0, 'rgba(139,92,246,0.4)');
                        grad.addColorStop(0.5, 'rgba(139,92,246,0.7)');
                        grad.addColorStop(1, 'rgba(139,92,246,1)');
                        charts.slowMovingProductsBar = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: data.slowMovingProducts.map(p => p.label),
                                datasets: [{
                                    label: 'Дней без продажи',
                                    data: data.slowMovingProducts.map(p => p.days),
                                    backgroundColor: grad,
                                    borderColor: 'rgba(139,92,246,0.3)',
                                    borderRadius: 8,
                                    borderSkipped: false
                                }]
                            },
                            options: {
                                indexAxis: 'y',
                                plugins: { legend: { display: false } },
                                scales: {
                                    x: {
                                        beginAtZero: true,
                                        suggestedMax: maxValue + 1,
                                        grid: { display: true, color: '#e5e7eb' },
                                        ticks: {
                                            callback: function(value) { return Number.isInteger(value) ? value : ''; },
                                            stepSize: 1,
                                            padding: 8
                                        }
                                    },
                                    y: {
                                        grid: { display: false },
                                        ticks: {
                                            callback: function(value) {
                                                const label = this.getLabelForValue(value);
                                                return label.length > 15 ? label.slice(0, 15) + '...' : label;
                                            },
                                            padding: 8
                                        }
                                    }
                                }
                            }
                        });
                    }
                }
                // --- Средний срок оборачиваемости ---
                const turnoverDaysChart = document.getElementById('turnoverDaysChart');
                if (turnoverDaysChart) {
                    const ctx = turnoverDaysChart.getContext('2d');
                    if (charts.turnoverDaysChart) charts.turnoverDaysChart.destroy();
                    charts.turnoverDaysChart = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Средний срок, дней'],
                            datasets: [{
                                data: [data.avgTurnoverDays ?? 0, (data.avgTurnoverDays ? 100 - data.avgTurnoverDays : 100)],
                                backgroundColor: ['#6366f1', '#e5e7eb'],
                                borderWidth: 0
                            }]
                        },
                        options: {
                            cutout: '80%',
                            plugins: {
                                legend: { display: false },
                                tooltip: { enabled: false },
                                datalabels: { display: false },
                                title: { display: false },
                                annotation: { display: false }
                            }
                        }
                    });
                    // В центр круга — число
                    if (turnoverDaysChart.parentNode.querySelector('.turnover-days-value')) {
                        turnoverDaysChart.parentNode.querySelector('.turnover-days-value').textContent = data.avgTurnoverDays ?? '—';
                    } else {
                        const val = document.createElement('div');
                        val.className = 'turnover-days-value';
                        val.style.position = 'absolute';
                        val.style.left = '50%';
                        val.style.top = '50%';
                        val.style.transform = 'translate(-50%,-50%)';
                        val.style.fontSize = '2.2rem';
                        val.style.fontWeight = '700';
                        val.style.color = '#6366f1';
                        val.textContent = data.avgTurnoverDays ?? '—';
                        turnoverDaysChart.parentNode.style.position = 'relative';
                        turnoverDaysChart.parentNode.appendChild(val);
                    }
                }
                // После получения данных из API, добавить вывод значения с подписью 'дней'
                if (document.getElementById('avgTurnoverDaysValue')) {
                    const val = data.avgTurnoverDays;
                    document.getElementById('avgTurnoverDaysValue').textContent = val !== null ? val + ' дней' : '—';
                }
            });
    }
</script>
@endpush
