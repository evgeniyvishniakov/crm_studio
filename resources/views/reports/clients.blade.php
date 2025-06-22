@extends('layouts.app')

@section('content')
<div class="dashboard-container">
    <h1 class="dashboard-title">Отчеты</h1>

    <!-- Фильтры -->
    <div class="filter-section">
        <div class="row align-items-end">
            <div class="col-md-4">
                <label for="daterange" class="font-weight-bold">Период:</label>
                <input type="text" id="daterange" name="daterange" class="form-control" placeholder="Выберите период...">
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary btn-block" style="background: linear-gradient(135deg, #3b82f6, #60a5fa); border: none;">Применить</button>
            </div>
        </div>
    </div>

    <!-- Навигация по вкладкам -->
    <div class="dashboard-tabs">
        <button class="tab-button active" data-tab="clients-analytics"><i class="fa fa-users"></i> Аналитика по клиентам</button>
        <button class="tab-button" data-tab="appointments-analytics"><i class="fa fa-calendar"></i> Аналитика по записям</button>
        <button class="tab-button" data-tab="complex-analytics"><i class="fa fa-money"></i> Финансовая аналитика</button>
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
                        <h4 class="mb-3">Распределение по типам клиентов</h4>
                        <p class="text-muted">Соотношение клиентов по реальным типам из вашего справочника.</p>
                        <canvas id="clientTypesChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">Новые vs. Вернувшиеся клиенты</h4>
                        <p class="text-muted">Соотношение новых и повторных клиентов за период.</p>
                        <canvas id="newVsReturningChart"></canvas>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">RFM-анализ (в будущем)</h4>
                        <p class="text-muted">Более сложная сегментация по давности, частоте и сумме покупок.</p>
                        <div class="alert alert-info mt-4">Этот блок пока неактивен.</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Вкладка "Аналитика по записям" -->
        <div class="tab-pane" id="appointments-analytics" style="display: none;">
            <div class="row">
                <div class="col-lg-7 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">Загруженность по дням и часам</h4>
                        <p class="text-muted">Среднее количество записей по времени в течение недели.</p>
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
                        <h4 class="mb-3">Топ-5 клиентов по визитам</h4>
                        <p class="text-muted">Самые частые посетители за период.</p>
                        <canvas id="topClientsByVisitsChart"></canvas>
                    </div>
                </div>
            </div>
             <div class="row">
                <div class="col-lg-7 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">Динамика среднего чека</h4>
                        <p class="text-muted">Средняя сумма, которую клиент тратит за один визит.</p>
                        <canvas id="avgCheckChart"></canvas>
                    </div>
                </div>
                <div class="col-lg-5 mb-4">
                    <div class="report-card">
                        <h4 class="mb-3">Ценность клиента (LTV) по типам</h4>
                        <p class="text-muted">Прогнозируемая выручка с клиента за все время.</p>
                        <canvas id="ltvChart"></canvas>
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Логика переключения вкладок
    const tabs = document.querySelectorAll('.tab-button');
    const panes = document.querySelectorAll('.tab-pane');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');

            const targetPaneId = tab.getAttribute('data-tab');
            
            panes.forEach(pane => {
                if (pane.id === targetPaneId) {
                    pane.style.display = 'block';
                } else {
                    pane.style.display = 'none';
                }
            });
        });
    });

    // Инициализация графиков
    // 1. Динамика клиентской базы
    const clientDynamicsCtx = document.getElementById('clientDynamicsChart').getContext('2d');
    new Chart(clientDynamicsCtx, {
        type: 'line',
        data: {
            labels: ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл'],
            datasets: [{
                label: 'Новые клиенты',
                data: [12, 19, 10, 15, 22, 30, 25],
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                fill: true,
                tension: 0.4
            }]
        }
    });

    // 2. Распределение по типам клиентов
    const clientTypesCtx = document.getElementById('clientTypesChart').getContext('2d');
    new Chart(clientTypesCtx, {
        type: 'pie',
        data: {
            labels: ['Новые', 'Постоянные'],
            datasets: [{
                data: [50, 85],
                backgroundColor: ['#3b82f6', '#60a5fa'],
                hoverOffset: 4
            }]
        }
    });

    // 3. Новые vs. Вернувшиеся клиенты
    const newVsReturningCtx = document.getElementById('newVsReturningChart').getContext('2d');
    new Chart(newVsReturningCtx, {
        type: 'doughnut',
        data: {
            labels: ['Новые клиенты', 'Вернувшиеся клиенты'],
            datasets: [{
                data: [35, 65],
                backgroundColor: ['#8b5cf6', '#a78bfa'],
                hoverOffset: 4
            }]
        }
    });

    // --- Графики для вкладки "Аналитика по записям" ---

    // 4. Загруженность по дням и часам
    const loadCtx = document.getElementById('loadChart').getContext('2d');
    new Chart(loadCtx, {
        type: 'bar',
        data: {
            labels: ['Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота', 'Воскресенье'],
            datasets: [{
                label: 'Записи',
                data: [5, 8, 12, 10, 15, 20, 4],
                backgroundColor: 'rgba(59, 130, 246, 0.7)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // 5. Статусы записей
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'pie',
        data: {
            labels: ['Выполнено', 'Отменено', 'Не пришел'],
            datasets: [{
                data: [250, 45, 15],
                backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                hoverOffset: 4
            }]
        }
    });

    // 6. Популярность услуг
    const servicesCtx = document.getElementById('servicesChart').getContext('2d');
    new Chart(servicesCtx, {
        type: 'bar',
        data: {
            labels: ['Стрижка женская', 'Маникюр + Гель-лак', 'Окрашивание корней', 'Педикюр', 'Коррекция бровей', 'Укладка', 'Макияж'],
            datasets: [{
                label: 'Количество записей',
                data: [80, 75, 60, 50, 45, 30, 15],
                backgroundColor: 'rgba(139, 92, 246, 0.7)',
                borderColor: 'rgba(139, 92, 246, 1)',
                borderWidth: 1
            }]
        },
        options: {
            indexAxis: 'y', // Делаем диаграмму горизонтальной
            scales: {
                x: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: false // Можно скрыть легенду, т.к. только один набор данных
                }
            }
        }
    });

    // --- Графики для вкладки "Финансовая аналитика" ---

    // 7. Динамика среднего чека
    const avgCheckCtx = document.getElementById('avgCheckChart').getContext('2d');
    new Chart(avgCheckCtx, {
        type: 'line',
        data: {
            labels: ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл'],
            datasets: [{
                label: 'Средний чек (руб.)',
                data: [1500, 1550, 1650, 1600, 1750, 1800, 1780],
                borderColor: 'rgb(239, 68, 68)',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                fill: true,
                tension: 0.4
            }]
        }
    });

    // 8. LTV по типам клиентов
    const ltvCtx = document.getElementById('ltvChart').getContext('2d');
    new Chart(ltvCtx, {
        type: 'bar',
        data: {
            labels: ['Новые', 'Постоянные'],
            datasets: [{
                label: 'LTV (руб.)',
                data: [5000, 25000],
                backgroundColor: ['#f59e0b', '#fbbf24'],
            }]
        },
        options: {
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // 9. Топ-5 клиентов по выручке
    const topRevenueCtx = document.getElementById('topClientsByRevenueChart').getContext('2d');
    new Chart(topRevenueCtx, {
        type: 'bar',
        data: {
            labels: ['Екатерина Иванова', 'Алексей Смирнов', 'Ольга Васильева', 'Дмитрий Петров', 'Марина Соколова'],
            datasets: [{
                label: 'Выручка (руб.)',
                data: [45000, 41000, 38000, 32000, 29000],
                backgroundColor: 'rgba(239, 68, 68, 0.7)',
            }]
        },
        options: {
            indexAxis: 'y',
            plugins: { legend: { display: false } }
        }
    });

    // 10. Топ-5 клиентов по визитам
    const topVisitsCtx = document.getElementById('topClientsByVisitsChart').getContext('2d');
    new Chart(topVisitsCtx, {
        type: 'bar',
        data: {
            labels: ['Марина Соколова', 'Ольга Васильева', 'Екатерина Иванова', 'Анна Попова', 'Иван Лебедев'],
            datasets: [{
                label: 'Кол-во визитов',
                data: [15, 14, 12, 12, 11],
                backgroundColor: 'rgba(16, 185, 129, 0.7)',
            }]
        },
        options: {
            indexAxis: 'y',
            plugins: { legend: { display: false } }
        }
    });
});
</script>
@endpush 