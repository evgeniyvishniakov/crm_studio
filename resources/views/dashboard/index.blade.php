@extends('layouts.app')

@section('content')
<style>
/* Dashboard Statistics Cards - Inline Styles */
.dashboard-container {
    padding: 10px !important;
    background: #f8f9fa !important;
    min-height: 100vh !important;
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
    gap: 0.8rem !important;
    margin-bottom: 2rem !important;
}

.stat-card {
    background: white !important;
    border-radius: 12px !important;
    padding: 1rem !important;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
    transition: all 0.3s ease !important;
    position: relative !important;
    overflow: hidden !important;
    display: flex !important;
    align-items: center !important;
    gap: 0.8rem !important;
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

@media (max-width: 1000px) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 1rem !important;
    }
    
    .stat-card {
        padding: 1rem !important;
    }
    
    .stat-icon {
        width: 45px !important;
        height: 45px !important;
        font-size: 1.1rem !important;
    }
    
    .stat-value {
        font-size: 1.3rem !important;
    }
}
</style>

    <div class="dashboard-container">
        <h1 class="dashboard-title">CRM Analytics Dashboard</h1>

        <div class="stats-grid">
            <!-- Карточка Прибыль -->
            <div class="stat-card profit-card">
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-title">Прибыль</h3>
                    <p class="stat-value">₽ 2,847,500</p>
                    <p class="stat-change positive">
                        <i class="fas fa-arrow-up"></i>
                        15.3% с прошлого месяца
                    </p>
                </div>
            </div>

            <!-- Карточка Продажи -->
            <div class="stat-card sales-card">
                <div class="stat-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-title">Продажи</h3>
                    <p class="stat-value">₽ 4,250,000</p>
                    <p class="stat-change positive">
                        <i class="fas fa-arrow-up"></i>
                        8.7% с прошлого месяца
                    </p>
                </div>
            </div>

            <!-- Карточка Клиенты -->
            <div class="stat-card clients-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-title">Клиенты</h3>
                    <p class="stat-value">1,248</p>
                    <p class="stat-change positive">
                        <i class="fas fa-arrow-up"></i>
                        12% с прошлого месяца
                    </p>
                </div>
            </div>

            <!-- Карточка Записи -->
            <div class="stat-card appointments-card">
                <div class="stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-title">Записи</h3>
                    <p class="stat-value">156</p>
                    <p class="stat-change positive">
                        <i class="fas fa-arrow-up"></i>
                        5.2% с прошлой недели
                    </p>
                </div>
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

    <!-- Подключаем Font Awesome для иконок -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
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
