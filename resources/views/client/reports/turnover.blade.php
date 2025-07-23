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
            <button class="tab-button" data-tab="expenses-analytics"><i class="fa fa-credit-card"></i> Расходы</button>
            <button class="tab-button" data-tab="employees-analytics"><i class="fa fa-users"></i> Сотрудники</button>
        </div>

        <!-- Фильтры периода -->
        <div class="filter-section" id="periodFiltersSection">
            <div class="period-filters" style="display:flex;align-items:center;gap:8px;">
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
                        <h4 class="mb-3">Динамика валовой прибыли</h4>
                        <p class="text-muted">Разница между суммой продаж и себестоимостью товаров.</p>
                        <canvas id="grossProfitChart"></canvas>
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
            <div class="row mb-4" id="stockSummaryRow">
                <div class="col-lg-4 mb-2">
                    <div class="stat-card" style="background:#f3f4f6;padding:18px 24px;border-radius:10px;font-size:1.1rem;font-weight:600;">
                        <span>Общее количество товаров на складе: </span><span id="stockTotalQty">—</span> шт
                    </div>
                </div>
                <div class="col-lg-4 mb-2">
                    <div class="stat-card" style="background:#f3f4f6;padding:18px 24px;border-radius:10px;font-size:1.1rem;font-weight:600;">
                        <span>Общая сумма опта на складе: </span><span id="stockTotalWholesale">—</span> грн
                    </div>
                </div>
                <div class="col-lg-4 mb-2">
                    <div class="stat-card" style="background:#f3f4f6;padding:18px 24px;border-radius:10px;font-size:1.1rem;font-weight:600;">
                        <span>Общая сумма розници на складе: </span><span id="stockTotalRetail">—</span> грн
                    </div>
                </div>
            </div>
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
                        <h4 class="mb-3">Структура по поставщикам</h4>
                        <p class="text-muted">Доля топ-5 поставщиков в общем объеме закупок.</p>
                        <canvas id="supplierStructurePie"></canvas>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">Поставщики по объёму закупок</h4>
                        <p class="text-muted">Топ-6 поставщиков с наибольшим объемом закупок за период.</p>
                        <canvas id="topSuppliersBar"></canvas>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">Остатки на складе по категориям</h4>
                        <p class="text-muted">Количество и сумма остатков по оптовой и розничной цене для каждой категории товаров.</p>
                        <canvas id="stockByCategoryBar"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="expenses-analytics" style="display: none;">
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">Доля фиксированных и переменных расходов</h4>
                        <p class="text-muted">Сравнение фиксированных и переменных расходов.</p>
                        <canvas id="expensesFixedVariablePie"></canvas>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">Структура расходов по категориям</h4>
                        <p class="text-muted">Доля каждой категории в общих расходах.</p>
                        <canvas id="expensesByCategoryPie"></canvas>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">Динамика расходов по категориям</h4>
                        <p class="text-muted">Как менялись расходы по каждой категории во времени.</p>
                        <canvas id="expensesCategoryDynamicsChart"></canvas>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">Динамика расходов по месяцам</h4>
                        <p class="text-muted">Как менялись общие расходы по месяцам.</p>
                        <canvas id="expensesByMonthChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">Топ-3 месяца по расходам</h4>
                        <p class="text-muted">Месяцы с наибольшими расходами.</p>
                        <canvas id="expensesTopMonthsBar"></canvas>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">Средний расход по категориям</h4>
                        <p class="text-muted">Средний ежемесячный расход по каждой категории.</p>
                        <canvas id="expensesAverageByCategoryBar"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="employees-analytics" style="display: none;">
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">Топ-5 сотрудников по объему продаж</h4>
                        <p class="text-muted">Сотрудники с наибольшей суммой продаж за период.</p>
                        <canvas id="topEmployeesBar"></canvas>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">Структура продаж по сотрудникам</h4>
                        <p class="text-muted">Доля каждого сотрудника в общих продажах.</p>
                        <canvas id="employeesStructurePie"></canvas>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">Динамика продаж по сотрудникам</h4>
                        <p class="text-muted">Как менялись продажи каждого сотрудника во времени.</p>
                        <canvas id="employeesDynamicsChart"></canvas>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">Средняя сумма продажи по сотрудникам</h4>
                        <p class="text-muted">Средняя сумма одной продажи у каждого сотрудника.</p>
                        <canvas id="employeesAverageBar"></canvas>
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
                start = new Date(end.getFullYear(), end.getMonth(), 1); // строго 1-е число месяца
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
        const format = d => d.getFullYear() + '-' +
            String(d.getMonth() + 1).padStart(2, '0') + '-' +
            String(d.getDate()).padStart(2, '0');
        const params = `start_date=${format(start)}&end_date=${format(end)}&period=${encodeURIComponent(period)}`;
        return params;
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
        updateTopsAnalytics(params);
        updateSuppliersAnalytics(params);
        // updateExpensesAnalytics(params); // Убрано - будет вызвано при переключении на вкладку

        if (document.getElementById('stockTotalQty')) {
            document.getElementById('stockTotalQty').textContent = stockTotalQty.toLocaleString('ru-RU');
        }
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
    const periodFiltersSection = document.getElementById('periodFiltersSection');
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
            
            // Определяем активный период и обновляем данные для ВСЕХ вкладок при переключении
            // Это гарантирует, что данные всегда соответствуют фильтру
                const activeBtn = document.querySelector('.filter-section .filter-button.active');
                let params = '';
            if (activeBtn && activeBtn.id !== 'dateRangePicker') {
                    const period = activeBtn.textContent.trim();
                    params = getPeriodParams(period);
            } else if (window.selectedRange) {
                    const formatISO = d => d.toISOString().slice(0, 10);
                    params = `start_date=${formatISO(window.selectedRange.start)}&end_date=${formatISO(window.selectedRange.end)}`;
            } else {
                 params = getPeriodParams('За месяц'); // Фоллбэк на месяц, если ничего не выбрано
                }

            if (targetPaneId === 'dynamic-analytics') {
                updateTurnoverAnalytics(params);
            } else if (targetPaneId === 'tops-analytics') {
                updateTopsAnalytics(params);
            } else if (targetPaneId === 'suppliers-analytics') {
                updateSuppliersAnalytics(params);
            } else if (targetPaneId === 'expenses-analytics') {
                updateExpensesAnalytics(params);
            } else if (targetPaneId === 'employees-analytics') {
                updateEmployeesAnalytics(params);
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
                                        // Используем labels для отображения (они уже отформатированы)
                                        return this.getLabelForValue(value);
                                    }
                                }
                            }
                        }
                    }
                };
                // Валовая прибыль
                const grossProfitConfig = {
                    type: 'line',
                    data: {
                        labels: data.dynamic.labels,
                        datasets: [
                            {
                                label: 'Валовая прибыль',
                                data: data.dynamic.gross_profit,
                                borderColor: 'rgb(245, 158, 11)',
                                backgroundColor: 'rgba(245, 158, 11, 0.1)',
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
                                        // Используем labels для отображения (они уже отформатированы)
                                        return this.getLabelForValue(value);
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
                                        const sum = data.type.sums[i];
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
                // Инициализация графиков
                const chartMap = [
                    {id: 'turnoverDynamicChart', config: dynamicConfig},
                    {id: 'grossProfitChart', config: grossProfitConfig},
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
            
            const period = button.textContent.trim();
            const params = getPeriodParams(period);
            
            // Обновляем данные на активной вкладке
            const activeTab = document.querySelector('.tab-button.active');
            const activeTabId = activeTab ? activeTab.getAttribute('data-tab') : 'dynamic-analytics';

            if (activeTabId === 'dynamic-analytics') {
                updateTurnoverAnalytics(params);
            } else if (activeTabId === 'tops-analytics') {
                updateTopsAnalytics(params);
            } else if (activeTabId === 'suppliers-analytics') {
                updateSuppliersAnalytics(params);
            } else if (activeTabId === 'expenses-analytics') {
                updateExpensesAnalytics(params);
            } else if (activeTabId === 'employees-analytics') {
                updateEmployeesAnalytics(params);
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
                        window.selectedRange = {start: selectedDates[0], end: selectedDates[1]};
                        
                        // Обновляем данные на активной вкладке
                        const activeTab = document.querySelector('.tab-button.active');
                        const activeTabId = activeTab ? activeTab.getAttribute('data-tab') : 'dynamic-analytics';

                        if (activeTabId === 'dynamic-analytics') {
                            updateTurnoverAnalytics(params);
                        } else if (activeTabId === 'tops-analytics') {
                            updateTopsAnalytics(params);
                        } else if (activeTabId === 'suppliers-analytics') {
                            updateSuppliersAnalytics(params);
                        } else if (activeTabId === 'expenses-analytics') {
                            updateExpensesAnalytics(params);
                        } else if (activeTabId === 'employees-analytics') {
                            updateEmployeesAnalytics(params);
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

                // --- Структура по поставщикам (круговая диаграмма) ---
                if (charts.supplierStructurePie) charts.supplierStructurePie.destroy();
                const supplierStructurePie = document.getElementById('supplierStructurePie');
                if (supplierStructurePie) {
                    const pieColors = ['#2563eb', '#10b981', '#f59e42', '#7c3aed', '#ef4444', '#64748b'];
                    const ctx = supplierStructurePie.getContext('2d');
                    charts.supplierStructurePie = new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: data.supplierStructure.map(s => s.label),
                            datasets: [{
                                data: data.supplierStructure.map(s => s.sum),
                                backgroundColor: pieColors,
                                borderColor: 'rgba(255,255,255,0.7)',
                                borderWidth: 2,
                                hoverOffset: 12,
                            }]
                        },
                        options: {
                             plugins: {
                                legend: { position: 'top' },
                                tooltip: {
                                    callbacks: {
                                        label: function (context) {
                                            const label = context.label || '';
                                            const value = context.raw || 0;
                                            return `${label}: ${value.toLocaleString('ru-RU')} грн`;
                                        }
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
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const i = context.dataIndex;
                                            const cat = data.stockByCategory[i];
                                            return [
                                                `Категория: ${cat.label}`,
                                                `Остаток: ${cat.qty} шт`,
                                                `Опт: ${cat.wholesale.toLocaleString('ru-RU')} грн`,
                                                `Розница: ${cat.retail.toLocaleString('ru-RU')} грн`
                                            ];
                                        }
                                    }
                                }
                            },
                            // indexAxis: 'y', // УБРАНО! Теперь график снова вертикальный
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
                if (document.getElementById('stockTotalQty')) {
                    document.getElementById('stockTotalQty').textContent = data.stockTotalQty.toLocaleString('ru-RU');
                }
                if (document.getElementById('stockTotalWholesale')) {
                    document.getElementById('stockTotalWholesale').textContent = data.stockTotalWholesale.toLocaleString('ru-RU');
                }
                if (document.getElementById('stockTotalRetail')) {
                    document.getElementById('stockTotalRetail').textContent = data.stockTotalRetail.toLocaleString('ru-RU');
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

    // --- Функция для обновления графиков расходов ---
    function updateExpensesAnalytics(params = '') {
        // 1. Динамика по месяцам
        fetch('/analytics/expenses-by-month' + (params ? '?' + params : ''))
            .then(r => r.json())
            .then(data => {
                const ctx = document.getElementById('expensesByMonthChart').getContext('2d');
                if (charts.expensesByMonthChart) charts.expensesByMonthChart.destroy();
                charts.expensesByMonthChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Общие расходы',
                            data: data.data,
                            borderColor: '#ef4444',
                            backgroundColor: 'rgba(239,68,68,0.1)',
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {scales: {y: {beginAtZero: true}}}
                });
            });
        // 2. Структура по категориям
        fetch('/analytics/expenses-by-category' + (params ? '?' + params : ''))
            .then(r => r.json())
            .then(data => {
                const ctx = document.getElementById('expensesByCategoryPie').getContext('2d');
                if (charts.expensesByCategoryPie) charts.expensesByCategoryPie.destroy();
                charts.expensesByCategoryPie = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            data: data.data,
                            backgroundColor: [
                                '#2563eb','#10b981','#f59e42','#7c3aed','#ef4444','#0ea5e9'
                            ]
                        }]
                    }
                });
            });
        // 3. Динамика по категориям
        fetch('/analytics/expenses-category-dynamics' + (params ? '?' + params : ''))
            .then(r => r.json())
            .then(data => {
                const ctx = document.getElementById('expensesCategoryDynamicsChart').getContext('2d');
                if (charts.expensesCategoryDynamicsChart) charts.expensesCategoryDynamicsChart.destroy();
                const datasets = Object.keys(data.datasets).map((cat, i) => ({
                    label: cat,
                    data: data.datasets[cat],
                    borderColor: ['#2563eb','#10b981','#f59e42','#7c3aed','#ef4444','#0ea5e9'][i % 6],
                    backgroundColor: 'rgba(0,0,0,0)',
                    fill: false,
                    tension: 0.4
                }));
                charts.expensesCategoryDynamicsChart = new Chart(ctx, {
                    type: 'line',
                    data: {labels: data.labels, datasets},
                    options: {scales: {y: {beginAtZero: true}}}
                });
            });
        // 4. Средний расход по категориям
        fetch('/analytics/expenses-average-by-category' + (params ? '?' + params : ''))
            .then(r => r.json())
            .then(data => {
                const ctx = document.getElementById('expensesAverageByCategoryBar').getContext('2d');
                if (charts.expensesAverageByCategoryBar) charts.expensesAverageByCategoryBar.destroy();
                charts.expensesAverageByCategoryBar = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Средний расход',
                            data: data.data,
                            backgroundColor: '#f59e42'
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true,
                                suggestedMax: Math.max(...data.data) * 1.1
                            }
                        }
                    }
                });
            });
        // 5. Топ-3 месяца
        fetch('/analytics/expenses-top-months' + (params ? '?' + params : ''))
            .then(r => r.json())
            .then(data => {
                const ctx = document.getElementById('expensesTopMonthsBar').getContext('2d');
                if (charts.expensesTopMonthsBar) charts.expensesTopMonthsBar.destroy();
                charts.expensesTopMonthsBar = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Расходы',
                            data: data.data,
                            backgroundColor: '#7c3aed'
                        }]
                    },
                    options: {scales: {y: {beginAtZero: true}}}
                });
            });
        // 6. Фикс/переменные
        fetch('/analytics/expenses-fixed-variable' + (params ? '?' + params : ''))
            .then(r => r.json())
            .then(data => {
                const ctx = document.getElementById('expensesFixedVariablePie').getContext('2d');
                if (charts.expensesFixedVariablePie) charts.expensesFixedVariablePie.destroy();
                charts.expensesFixedVariablePie = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            data: data.data,
                            backgroundColor: ['#2563eb','#ef4444']
                        }]
                    }
                });
            });
    }

    function updateEmployeesAnalytics(params = '') {
        let url = '/analytics/employees-analytics';
        if (params) url += '?' + params;
        fetch(url)
            .then(response => response.json())
            .then(data => {
                // Топ-5 сотрудников по объему продаж
                if (charts.topEmployeesBar) charts.topEmployeesBar.destroy();
                const topEmployeesBar = document.getElementById('topEmployeesBar');
                if (topEmployeesBar) {
                    const maxValue = data.topEmployees.length > 0 ? Math.max(...data.topEmployees.map(e => e.sum)) : 0;
                    const ctx = topEmployeesBar.getContext('2d');
                    const area = {left: 0, right: topEmployeesBar.width};
                    const grad = ctx.createLinearGradient(area.left, 0, area.right, 0);
                    grad.addColorStop(0, 'rgba(59,130,246,0.2)');
                    grad.addColorStop(0.5, 'rgba(59,130,246,0.5)');
                    grad.addColorStop(1, 'rgba(59,130,246,0.9)');
                    charts.topEmployeesBar = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: data.topEmployees.map(e => e.label),
                            datasets: [{
                                label: 'Продажи, грн',
                                data: data.topEmployees.map(e => e.sum),
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
                                    suggestedMax: maxValue * 1.1,
                                    grid: { display: true, color: '#e5e7eb' },
                                    ticks: {
                                        callback: function(value) { return value.toLocaleString('ru-RU'); },
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

                // Структура продаж по сотрудникам (круговая диаграмма)
                if (charts.employeesStructurePie) charts.employeesStructurePie.destroy();
                const employeesStructurePie = document.getElementById('employeesStructurePie');
                if (employeesStructurePie) {
                    const pieColors = ['#2563eb', '#10b981', '#f59e42', '#7c3aed', '#ef4444', '#64748b'];
                    const ctx = employeesStructurePie.getContext('2d');
                    charts.employeesStructurePie = new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: data.employeesStructure.map(e => e.label),
                            datasets: [{
                                data: data.employeesStructure.map(e => e.sum),
                                backgroundColor: pieColors,
                                borderColor: 'rgba(255,255,255,0.7)',
                                borderWidth: 2,
                                hoverOffset: 12,
                            }]
                        },
                        options: {
                             plugins: {
                                legend: { position: 'top' },
                                tooltip: {
                                    callbacks: {
                                        label: function (context) {
                                            const label = context.label || '';
                                            const value = context.raw || 0;
                                            return `${label}: ${value.toLocaleString('ru-RU')} грн`;
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
                
                // Динамика продаж по сотрудникам
                if (charts.employeesDynamicsChart) charts.employeesDynamicsChart.destroy();
                const employeesDynamicsChart = document.getElementById('employeesDynamicsChart');
                if (employeesDynamicsChart) {
                    const ctx = employeesDynamicsChart.getContext('2d');
                    const datasets = Object.keys(data.employeesDynamics).map((emp, i) => ({
                        label: emp,
                        data: data.employeesDynamics[emp],
                        borderColor: ['#2563eb','#10b981','#f59e42','#7c3aed','#ef4444','#0ea5e9'][i % 6],
                        backgroundColor: 'rgba(0,0,0,0)',
                        fill: false,
                        tension: 0.4
                    }));
                    charts.employeesDynamicsChart = new Chart(ctx, {
                        type: 'line',
                        data: {labels: data.employeesDynamicsLabels, datasets},
                        options: {scales: {y: {beginAtZero: true}}}
                    });
                }

                // Средняя сумма продажи по сотрудникам
                if (charts.employeesAverageBar) charts.employeesAverageBar.destroy();
                const employeesAverageBar = document.getElementById('employeesAverageBar');
                if (employeesAverageBar) {
                    const ctx = employeesAverageBar.getContext('2d');
                    if (charts.employeesAverageBar) charts.employeesAverageBar.destroy();
                    charts.employeesAverageBar = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: data.employeesAverage.labels,
                            datasets: [{
                                label: 'Средняя сумма продажи',
                                data: data.employeesAverage.data,
                                backgroundColor: '#f59e42'
                            }]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    suggestedMax: Math.max(...data.employeesAverage.data) * 1.1
                                }
                            }
                        }
                    });
                }
            });
    }
</script>
@endpush

<style>
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
