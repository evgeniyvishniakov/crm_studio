@extends('layouts.app')

@section('content')
    <div class="dashboard-container">
        <h1 class="dashboard-title">CRM Analytics Dashboard</h1>

        <div class="stats-grid">
            <!-- Статистические карточки -->
            <div class="stat-card">
                <h3 class="stat-title">Total Customers</h3>
                <p class="stat-value blue">1,248</p>
                <p class="stat-change positive">↑ 12% from last month</p>
            </div>

            <div class="stat-card">
                <h3 class="stat-title">New Leads</h3>
                <p class="stat-value purple">84</p>
                <p class="stat-change positive">↑ 5% from last week</p>
            </div>

            <div class="stat-card">
                <h3 class="stat-title">Conversion Rate</h3>
                <p class="stat-value green">24%</p>
                <p class="stat-change negative">↓ 2% from last quarter</p>
            </div>
        </div>

        <!-- Графики -->
        <div class="charts-grid">
            <!-- 1. График продаж по месяцам -->
            <div class="chart-container">
                <h3 class="chart-title">Sales Performance</h3>
                <canvas id="salesChart" height="300"></canvas>
            </div>

            <!-- 2. Воронка продаж -->
            <div class="chart-container">
                <h3 class="chart-title">Sales Funnel</h3>
                <canvas id="funnelChart" height="300"></canvas>
            </div>

            <!-- 3. Распределение клиентов по регионам -->
            <div class="chart-container">
                <h3 class="chart-title">Customers by Region</h3>
                <canvas id="regionChart" height="300"></canvas>
            </div>

            <!-- 4. Активность клиентов -->
            <div class="chart-container">
                <h3 class="chart-title">Customer Activity</h3>
                <canvas id="activityChart" height="300"></canvas>
            </div>
        </div>
    </div>

    <!-- Подключаем Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // 1. График продаж по месяцам (линейный график)
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Sales 2023',
                    data: [12000, 19000, 15000, 22000, 21000, 25000, 28000, 26000, 30000, 32000, 35000, 40000],
                    borderColor: 'rgba(79, 70, 229, 1)',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // 2. Воронка продаж
        const funnelCtx = document.getElementById('funnelChart').getContext('2d');
        const funnelChart = new Chart(funnelCtx, {
            type: 'bar',
            data: {
                labels: ['Leads', 'Prospects', 'Negotiation', 'Closed Won', 'Closed Lost'],
                datasets: [{
                    label: 'Sales Funnel',
                    data: [1000, 600, 300, 150, 200],
                    backgroundColor: [
                        'rgba(99, 102, 241, 0.7)',
                        'rgba(129, 140, 248, 0.7)',
                        'rgba(167, 139, 250, 0.7)',
                        'rgba(14, 165, 233, 0.7)',
                        'rgba(239, 68, 68, 0.7)'
                    ],
                    borderColor: [
                        'rgba(99, 102, 241, 1)',
                        'rgba(129, 140, 248, 1)',
                        'rgba(167, 139, 250, 1)',
                        'rgba(14, 165, 233, 1)',
                        'rgba(239, 68, 68, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true
                    }
                }
            }
        });

        // 3. Распределение клиентов по регионам (круговая диаграмма)
        const regionCtx = document.getElementById('regionChart').getContext('2d');
        const regionChart = new Chart(regionCtx, {
            type: 'doughnut',
            data: {
                labels: ['North', 'South', 'East', 'West', 'Central'],
                datasets: [{
                    data: [25, 20, 30, 15, 10],
                    backgroundColor: [
                        'rgba(239, 68, 68, 0.7)',
                        'rgba(16, 185, 129, 0.7)',
                        'rgba(59, 130, 246, 0.7)',
                        'rgba(245, 158, 11, 0.7)',
                        'rgba(139, 92, 246, 0.7)'
                    ],
                    borderColor: [
                        'rgba(239, 68, 68, 1)',
                        'rgba(16, 185, 129, 1)',
                        'rgba(59, 130, 246, 1)',
                        'rgba(245, 158, 11, 1)',
                        'rgba(139, 92, 246, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right',
                    }
                }
            }
        });

        // 4. Активность клиентов (столбчатая диаграмма с группировкой)
        const activityCtx = document.getElementById('activityChart').getContext('2d');
        const activityChart = new Chart(activityCtx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [
                    {
                        label: 'New Customers',
                        data: [45, 60, 55, 70, 65, 80],
                        backgroundColor: 'rgba(59, 130, 246, 0.7)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Active Customers',
                        data: [30, 40, 50, 60, 55, 70],
                        backgroundColor: 'rgba(16, 185, 129, 0.7)',
                        borderColor: 'rgba(16, 185, 129, 1)',
                        borderWidth: 1
                    }
                ]
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
                }
            }
        });
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
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: 20px;
        }

        .chart-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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

            .charts-grid {
                grid-template-columns: 1fr;
            }

            .dashboard-title {
                font-size: 24px;
            }
        }
    </style>
@endsection
