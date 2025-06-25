@extends('layouts.app')

@section('content')
<div class="dashboard-container">
    <h1 class="dashboard-title">Аналитика по товарообороту</h1>

    <!-- Навигация по вкладкам -->
    <div class="dashboard-tabs">
        <button class="tab-button active" data-tab="dynamic-analytics"><i class="fa fa-line-chart"></i></i> Динамика и структура</button>
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
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">Топ-5 товаров по продажам</h4>
                        <p class="text-muted">Самые продаваемые товары за период.</p>
                        <canvas id="topSalesBar"></canvas>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
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
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">Товары с максимальным сроком без продажи</h4>
                        <p class="text-muted">Товары, которые не продавались дольше всего (залежалые остатки).</p>
                        <canvas id="slowMovingProductsBar"></canvas>
                    </div>
                </div>
            </div>
            <div class="row align-items-center mb-3">
                <div class="col-lg-6 mb-2">
                    <div class="stat-card" style="background:#f3f4f6;padding:18px 24px;border-radius:10px;font-size:1.1rem;font-weight:600;">
                        <span>Общая сумма опт: </span><span id="stockTotalWholesale">—</span> грн
                    </div>
                </div>
                <div class="col-lg-6 mb-2">
                    <div class="stat-card" style="background:#f3f4f6;padding:18px 24px;border-radius:10px;font-size:1.1rem;font-weight:600;">
                        <span>Общая сумма розница: </span><span id="stockTotalRetail">—</span> грн
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">Остатки на складе по категориям</h4>
                        <p class="text-muted">Количество и сумма остатков по оптовой и розничной цене для каждой категории товаров.</p>
                        <canvas id="stockByCategoryBar"></canvas>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 mb-4">
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
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Flatpickr для выбора диапазона дат -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
.analytics-group-container {
    background: #f9fafb;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.03);
    padding: 24px 18px 10px 18px;
    margin-bottom: 32px;
    border: 1px solid #e5e7eb;
}
.analytics-group-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #374151;
    margin-bottom: 18px;
    letter-spacing: 0.01em;
}
</style>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ru.js"></script>
<script>
// --- Данные для динамики товарооборота ---
// const turnoverDays = ...
// const turnoverSales = ...
// const turnoverPurchases = ...

// --- Инициализация графиков с реальными данными ---
document.addEventListener('DOMContentLoaded', function() {
    let charts = {};
    // Загрузка данных с сервера
    fetch('/reports/turnover-analytics')
        .then(response => response.json())
        .then(data => {
            // Динамика
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
                        y: { beginAtZero: true, grid: { display: true, color: '#e5e7eb' } },
                        x: {
                            grid: { display: false },
                            ticks: {
                                callback: function(value, index, values) {
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
                options: { ...pieOptions }
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
                options: { ...pieOptions }
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
                options: { ...pieOptions }
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
                options: { ...pieOptions }
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
                charts[id] = new Chart(ctx, config);
            });
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
                Object.values(charts).forEach(chart => { if (chart && chart.resize) chart.resize(); });
            }, 120);
        });
    });
    // Фильтры периода (заглушка)
    const filterButtons = document.querySelectorAll('.filter-section .filter-button');
    filterButtons.forEach(button => {
        button.addEventListener('click', () => {
            if (button.id === 'dateRangePicker') return;
            filterButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
            // Здесь можно реализовать обновление данных по периоду
        });
    });
    // Flatpickr календарь
    const calendarBtn = document.getElementById('dateRangePicker');
    const calendarRangeDisplay = document.getElementById('calendarRangeDisplay');
    let calendarInstance = null;
    calendarBtn.addEventListener('click', function(e) {
        if (!calendarInstance) {
            calendarInstance = flatpickr(calendarBtn, {
                mode: 'range',
                dateFormat: 'Y-m-d',
                locale: 'ru',
                onClose: function(selectedDates, dateStr) {
                    if (selectedDates.length === 2) {
                        filterButtons.forEach(btn => btn.classList.remove('active'));
                        calendarBtn.classList.add('active');
                        const format = d => d.toLocaleDateString('ru-RU', { day: '2-digit', month: '2-digit' });
                        calendarRangeDisplay.textContent = `${format(selectedDates[0])} — ${format(selectedDates[1])}`;
                    }
                }
            });
        }
        calendarInstance.open();
    });

    if (document.getElementById('stockTotalWholesale')) {
        document.getElementById('stockTotalWholesale').textContent = stockTotalWholesale.toLocaleString('ru-RU');
    }
    if (document.getElementById('stockTotalRetail')) {
        document.getElementById('stockTotalRetail').textContent = stockTotalRetail.toLocaleString('ru-RU');
    }
});
</script>
@endpush 