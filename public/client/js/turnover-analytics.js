// ===== АНАЛИТИКА ПО ОБОРОТУ =====

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
    // --- Инициализация при загрузке (по умолчанию за месяц) ---
    // Снимаем выделение со всех кнопок и выделяем месяц
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
                                    return this.getLabelForValue(value);
                                }
                            }
                        }
                    }
                }
            };
            
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
                                    return this.getLabelForValue(value);
                                }
                            }
                        }
                    }
                }
            };
            
            const strictPalette = [
                '#2563eb', '#10b981', '#f59e42', '#7c3aed', '#ef4444', '#0ea5e9',
                '#fbbf24', '#6366f1', '#dc2626', '#059669', '#eab308', '#334155'
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
                                        `Сумма: ${formatCurrency(sum)}`
                                    ];
                                }
                            }
                        }
                    }
                }
            };
            
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
                                        `Сумма: ${formatCurrency(sum)}`
                                    ];
                                }
                            }
                        }
                    }
                }
            };
            
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
                                        `Сумма: ${formatCurrency(sum)}`
                                    ];
                                }
                            }
                        }
                    }
                }
            };
            
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
                                        `Сумма: ${formatCurrency(sum)}`
                                    ];
                                }
                            }
                        }
                    }
                }
            };
            
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
            const barColor = 'rgba(59, 130, 246, 0.7)';
            const barColor2 = 'rgba(16, 185, 129, 0.7)';
            
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
                const yMax = maxValue > 0 ? Math.ceil(maxValue * 1.15) : undefined;
                charts.topClientsBySalesBar = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.topClients.labels,
                        datasets: [{
                            label: 'Закупки',
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
        if (calendarRangeDisplay) {
            calendarRangeDisplay.textContent = '';
            calendarRangeDisplay.style.minWidth = '';
        }
        
        const period = button.textContent.trim();
        const params = getPeriodParams(period);
        
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
                    calendarRangeDisplay.style.minWidth = '110px';
                    const formatISO = d => d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
                    const params = `start_date=${formatISO(selectedDates[0])}&end_date=${formatISO(selectedDates[1])}`;
                    window.selectedRange = {start: selectedDates[0], end: selectedDates[1]};
                    
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
                            label: 'Закупки',
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
                const yMax = maxValue > 0 ? Math.ceil(maxValue * 1.15) : undefined;
                charts.stockByCategoryBar = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.stockByCategory.map(c => c.label),
                        datasets: [{
                            label: 'Остатки, шт',
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
                                            `Остатки: ${cat.qty} шт`,
                                            `Опт: ${formatCurrency(cat.wholesale)}`,
                                            `Розница: ${formatCurrency(cat.retail)}`
                                        ];
                                    }
                                }
                            }
                        },
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
            
            if (document.getElementById('stockTotalQty')) {
                document.getElementById('stockTotalQty').textContent = data.stockTotalQty.toLocaleString('ru-RU');
            }
            if (document.getElementById('stockTotalWholesale')) {
                document.getElementById('stockTotalWholesale').textContent = data.stockTotalWholesale.toLocaleString('ru-RU');
            }
            if (document.getElementById('stockTotalRetail')) {
                document.getElementById('stockTotalRetail').textContent = data.stockTotalRetail.toLocaleString('ru-RU');
            }

            const turnoverDaysChart = document.getElementById('turnoverDaysChart');
            if (turnoverDaysChart) {
                const ctx = turnoverDaysChart.getContext('2d');
                if (charts.turnoverDaysChart) charts.turnoverDaysChart.destroy();
                charts.turnoverDaysChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Средний период дней'],
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
            if (document.getElementById('avgTurnoverDaysValue')) {
                const val = data.avgTurnoverDays;
                document.getElementById('avgTurnoverDaysValue').textContent = val !== null ? val + ' дней' : '—';
            }
        });
}

function updateExpensesAnalytics(params = '') {
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
                            label: 'Продажи',
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