@extends('client.layouts.app')

@php
use App\Helpers\CurrencyHelper;
$currency = $project->currency ?? 'UAH';
$currencySymbol = CurrencyHelper::getSymbol($currency);
@endphp

@section('content')
<div class="dashboard-container">
    <div class="report-header">
        <h1 class="dashboard-title">{{ __('messages.turnover_analytics') }}</h1>

        <!-- Навигация по вкладкам -->
        <div class="dashboard-tabs">
            <button class="tab-button active" data-tab="dynamic-analytics"><i class="fa fa-line-chart"></i> {{ __('messages.dynamics_and_structure') }}</button>
            <button class="tab-button" data-tab="tops-analytics"><i class="fa fa-star"></i> {{ __('messages.tops') }}</button>
            <button class="tab-button" data-tab="suppliers-analytics"><i class="fa fa-truck"></i> {{ __('messages.suppliers_and_stock') }}</button>
            <button class="tab-button" data-tab="expenses-analytics"><i class="fa fa-credit-card"></i> {{ __('messages.expenses') }}</button>
            <button class="tab-button" data-tab="employees-analytics"><i class="fa fa-users"></i> {{ __('messages.employees') }}</button>
        </div>

        <!-- Фильтры периода -->
        <div class="filter-section" id="periodFiltersSection">
            <div class="period-filters" style="display:flex;align-items:center;gap:8px;">
                <span class="period-tooltip" tabindex="0">
                    <i class="fa fa-question-circle" aria-hidden="true"></i>
                    <span class="period-tooltip-text">
                        <b>{{ __('messages.period_explanations') }}:</b><br>
                        <b>{{ __('messages.for_week') }}</b>: {{ __('messages.week_explanation') }}<br>
                        <b>{{ __('messages.for_2_weeks') }}</b>: {{ __('messages.2weeks_explanation') }}<br>
                        <b>{{ __('messages.for_month') }}</b>: {{ __('messages.month_explanation') }}<br>
                        <b>{{ __('messages.for_half_year') }}</b>: {{ __('messages.half_year_explanation') }}<br>
                        <b>{{ __('messages.for_year') }}</b>: {{ __('messages.year_explanation') }}
                    </span>
                </span>
                <button class="filter-button active">{{ __('messages.for_week') }}</button>
                <button class="filter-button">{{ __('messages.for_2_weeks') }}</button>
                <button class="filter-button">{{ __('messages.for_month') }}</button>
                <button class="filter-button">{{ __('messages.for_half_year') }}</button>
                <button class="filter-button">{{ __('messages.for_year') }}</button>
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
                        <h4 class="mb-3">{{ __('messages.sales_structure_by_categories') }}</h4>
                        <p class="text-muted">{{ __('messages.distribution_by_categories') }}</p>
                        <canvas id="turnoverCategoryPie"></canvas>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">{{ __('messages.sales_structure_by_brands') }}</h4>
                        <p class="text-muted">{{ __('messages.distribution_by_brands') }}</p>
                        <canvas id="turnoverBrandPie"></canvas>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">{{ __('messages.gross_profit_dynamics') }}</h4>
                        <p class="text-muted">{{ __('messages.difference_between_sales_and_cost') }}</p>
                        <canvas id="grossProfitChart"></canvas>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">{{ __('messages.structure_by_product_types') }}</h4>
                        <p class="text-muted">{{ __('messages.distribution_services_products') }}</p>
                        <canvas id="turnoverTypePie"></canvas>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">{{ __('messages.turnover_dynamics') }}</h4>
                        <p class="text-muted">{{ __('messages.total_sales_and_purchases_by_days') }}</p>
                        <canvas id="turnoverDynamicChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="tops-analytics" style="display: none;">
            <div class="row">
                <div class="col-lg-12 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">{{ __('messages.top_5_products_by_sales') }}</h4>
                        <p class="text-muted">{{ __('messages.most_sold_products_for_period') }}</p>
                        <canvas id="topSalesBar"></canvas>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">{{ __('messages.top_5_products_by_purchases') }}</h4>
                        <p class="text-muted">{{ __('messages.products_with_highest_purchase_volume') }}</p>
                        <canvas id="topPurchasesBar"></canvas>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">{{ __('messages.top_5_clients_by_product_purchases') }}</h4>
                        <p class="text-muted">{{ __('messages.clients_who_bought_products_for_highest_amount') }}</p>
                        <canvas id="topClientsBySalesBar"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="suppliers-analytics" style="display: none;">
            <div class="row mb-4" id="stockSummaryRow">
                <div class="col-lg-4 mb-2">
                    <div class="stat-card" style="background:#f3f4f6;padding:18px 24px;border-radius:10px;font-size:1.1rem;font-weight:600;">
                        <span>{{ __('messages.total_products_on_warehouse') }}: </span><span id="stockTotalQty">—</span> {{ __('messages.pieces') }}
                    </div>
                </div>
                <div class="col-lg-4 mb-2">
                    <div class="stat-card" style="background:#f3f4f6;padding:18px 24px;border-radius:10px;font-size:1.1rem;font-weight:600;">
                        <span>{{ __('messages.total_wholesale_amount_on_warehouse') }}: </span><span id="stockTotalWholesale" data-currency data-amount="0">—</span> {{ $currencySymbol }}
                    </div>
                </div>
                <div class="col-lg-4 mb-2">
                    <div class="stat-card" style="background:#f3f4f6;padding:18px 24px;border-radius:10px;font-size:1.1rem;font-weight:600;">
                        <span>{{ __('messages.total_retail_amount_on_warehouse') }}: </span><span id="stockTotalRetail" data-currency data-amount="0">—</span> {{ $currencySymbol }}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">{{ __('messages.average_turnover_period') }}</h4>
                        <p class="text-muted">{{ __('messages.average_time_between_purchase_and_sale') }}</p>
                        <canvas id="turnoverDaysChart"></canvas>
                        <div id="avgTurnoverDaysValue" style="font-size:1.2rem;font-weight:600;margin-top:12px;"></div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">{{ __('messages.structure_by_suppliers') }}</h4>
                        <p class="text-muted">{{ __('messages.share_of_top_5_suppliers_in_total_purchases') }}</p>
                        <canvas id="supplierStructurePie"></canvas>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">{{ __('messages.suppliers_by_purchase_volume') }}</h4>
                        <p class="text-muted">{{ __('messages.top_6_suppliers_with_highest_purchase_volume') }}</p>
                        <canvas id="topSuppliersBar"></canvas>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">{{ __('messages.warehouse_stock_by_categories') }}</h4>
                        <p class="text-muted">{{ __('messages.quantity_and_amount_of_stock_by_wholesale_and_retail_price') }}</p>
                        <canvas id="stockByCategoryBar"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="expenses-analytics" style="display: none;">
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">{{ __('messages.share_of_fixed_and_variable_expenses') }}</h4>
                        <p class="text-muted">{{ __('messages.comparison_of_fixed_and_variable_expenses') }}</p>
                        <canvas id="expensesFixedVariablePie"></canvas>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">{{ __('messages.expenses_structure_by_categories') }}</h4>
                        <p class="text-muted">{{ __('messages.share_of_each_category_in_total_expenses') }}</p>
                        <canvas id="expensesByCategoryPie"></canvas>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">{{ __('messages.expenses_dynamics_by_categories') }}</h4>
                        <p class="text-muted">{{ __('messages.how_expenses_changed_by_each_category_over_time') }}</p>
                        <canvas id="expensesCategoryDynamicsChart"></canvas>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">{{ __('messages.expenses_dynamics_by_months') }}</h4>
                        <p class="text-muted">{{ __('messages.how_total_expenses_changed_by_months') }}</p>
                        <canvas id="expensesByMonthChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">{{ __('messages.top_3_months_by_expenses') }}</h4>
                        <p class="text-muted">{{ __('messages.months_with_highest_expenses') }}</p>
                        <canvas id="expensesTopMonthsBar"></canvas>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">{{ __('messages.average_expense_by_categories') }}</h4>
                        <p class="text-muted">{{ __('messages.average_monthly_expense_by_each_category') }}</p>
                        <canvas id="expensesAverageByCategoryBar"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="employees-analytics" style="display: none;">
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">{{ __('messages.top_5_employees_by_sales_volume') }}</h4>
                        <p class="text-muted">{{ __('messages.employees_with_highest_sales_amount_for_period') }}</p>
                        <canvas id="topEmployeesBar"></canvas>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">{{ __('messages.sales_structure_by_employees') }}</h4>
                        <p class="text-muted">{{ __('messages.share_of_each_employee_in_total_sales') }}</p>
                        <canvas id="employeesStructurePie"></canvas>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">{{ __('messages.sales_dynamics_by_employees') }}</h4>
                        <p class="text-muted">{{ __('messages.how_sales_of_each_employee_changed_over_time') }}</p>
                        <canvas id="employeesDynamicsChart"></canvas>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">{{ __('messages.average_sale_amount_by_employees') }}</h4>
                        <p class="text-muted">{{ __('messages.average_amount_of_one_sale_for_each_employee') }}</p>
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

    // Функция форматирования валюты
    function formatCurrency(value) {
        if (window.CurrencyManager) {
            return window.CurrencyManager.formatAmount(value);
        } else {
            value = parseFloat(value);
            if (isNaN(value)) return '0';
            return value.toLocaleString('ru-RU') + ' грн';
        }
    }
    // --- Функция для вычисления диапазона дат по календарным периодам ---
    function getPeriodParams(period) {
        const end = new Date();
        let start;
        switch (period) {
            case '{{ __('messages.for_week') }}':
                start = new Date(end);
                start.setDate(end.getDate() - ((end.getDay() + 6) % 7)); // последний понедельник
                break;
            case '{{ __('messages.for_2_weeks') }}':
                start = new Date(end);
                start.setDate(end.getDate() - ((end.getDay() + 6) % 7) - 7); // предпоследний понедельник
                break;
            case '{{ __('messages.for_month') }}':
                start = new Date(end.getFullYear(), end.getMonth(), 1); // строго 1-е число месяца
                break;
            case '{{ __('messages.for_half_year') }}':
                start = new Date(end.getFullYear(), end.getMonth() - 5, 1);
                break;
            case '{{ __('messages.for_year') }}':
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
        // Снимаем выделение со всех кнопок и выделяем месяц
        const filterButtonsArr = Array.from(document.querySelectorAll('.filter-section .filter-button'));
        const monthBtn = filterButtonsArr.find(btn => btn.textContent.trim() === '{{ __('messages.for_month') }}');
        if (monthBtn) {
            filterButtonsArr.forEach(btn => btn.classList.remove('active'));
            monthBtn.classList.add('active');
        }
        // Формируем параметры для месяца
        const params = getPeriodParams('{{ __('messages.for_month') }}');
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
                 params = getPeriodParams('{{ __('messages.for_month') }}'); // Фоллбэк на месяц, если ничего не выбрано
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
                                label: '{{ __('messages.sales') }}',
                                data: data.dynamic.sales,
                                borderColor: 'rgb(59, 130, 246)',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                fill: true,
                                tension: 0.4
                            },
                            {
                                label: '{{ __('messages.purchases') }}',
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
                                label: '{{ __('messages.gross_profit') }}',
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
                                                `{{ __('messages.quantity') }}: ${count} {{ __('messages.pieces') }}`,
                                                `{{ __('messages.sum') }}: ${formatCurrency(sum)}`
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
                                                `{{ __('messages.quantity') }}: ${count} {{ __('messages.pieces') }}`,
                                                `{{ __('messages.sum') }}: ${formatCurrency(sum)}`
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
                                                `{{ __('messages.sum') }}: ${formatCurrency(sum)}`
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
                                                `{{ __('messages.sum') }}: ${formatCurrency(sum)}`
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
                                label: '{{ __('messages.sales') }}, {{ __('messages.pieces') }}',
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
                                label: '{{ __('messages.purchases') }}, {{ __('messages.pieces') }}',
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
                                label: '{{ __('messages.purchases') }}',
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
        '{{ __('messages.for_week') }}': 7,
        '{{ __('messages.for_2_weeks') }}': 14,
        '{{ __('messages.for_month') }}': 30,
        '{{ __('messages.for_half_year') }}': 182,
        '{{ __('messages.for_year') }}': 365
    };
    filterButtons.forEach(button => {
        button.addEventListener('click', () => {
            if (button.id === 'dateRangePicker') return;
            filterButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
            // Очищаем отображение диапазона дат при выборе любого периода
            if (calendarRangeDisplay) {
                calendarRangeDisplay.textContent = '';
                calendarRangeDisplay.style.minWidth = '';
            }
            
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
                locale: '{{ app()->getLocale() }}',
                onClose: function (selectedDates, dateStr) {
                    if (selectedDates.length === 2) {
                        filterButtons.forEach(btn => btn.classList.remove('active'));
                        calendarBtn.classList.add('active');
                        const format = d => d.toLocaleDateString('{{ app()->getLocale() }}-{{ strtoupper(app()->getLocale()) }}', {day: '2-digit', month: '2-digit'});
                        calendarRangeDisplay.textContent = `${format(selectedDates[0])} — ${format(selectedDates[1])}`;
                        calendarRangeDisplay.style.minWidth = '110px';
                        // Форматируем для запроса
                        const formatISO = d => d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
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
                                label: '{{ __('messages.purchases') }}',
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
                                            return `${label}: ${formatCurrency(value)}`;
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
                                label: '{{ __('messages.stock') }}, {{ __('messages.pieces') }}',
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
                                            `{{ __('messages.category') }}: ${cat.label}`,
                                            `{{ __('messages.stock') }}: ${cat.qty} {{ __('messages.pieces') }}`,
                                            `{{ __('messages.wholesale') }}: ${formatCurrency(cat.wholesale)}`,
                                            `{{ __('messages.retail') }}: ${formatCurrency(cat.retail)}`
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
                        labels: ['{{ __('messages.average_period_days') }}'],
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
                    document.getElementById('avgTurnoverDaysValue').textContent = val !== null ? val + ' {{ __('messages.days') }}' : '—';
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
                            label: '{{ __('messages.total_expenses') }}',
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
                                label: '{{ __('messages.average_expense') }}',
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
                                label: '{{ __('messages.expenses') }}',
                                data: data.data,
                                backgroundColor: '#7c3aed'
                            }]
                    },
                    options: {scales: {y: {beginAtZero: true}}}
                });
            });
        // 6. Фиксированные/переменные
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
                                label: '{{ __('messages.sales') }}',
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
                                            return `${label}: ${formatCurrency(value)}`;
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
                                label: '{{ __('messages.average_sale_amount') }}',
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
