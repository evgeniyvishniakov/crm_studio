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
    gap: 1.5rem !important;
    margin-bottom: 2rem !important;
    align-items: stretch !important;
}

.stat-card {
    height: 150px !important;
    min-width: 0 !important;
    max-width: 100% !important;
    display: flex !important;
    flex-direction: column !important;
    justify-content: center !important;
    align-items: flex-start !important;
    padding: 1.2rem 1.1rem !important;
    box-sizing: border-box !important;
    background: white !important;
    border-radius: 12px !important;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
    transition: all 0.3s ease !important;
    position: relative !important;
    overflow: hidden !important;
}

.stat-card .stat-content {
    flex: 1 1 auto !important;
    display: flex !important;
    flex-direction: column !important;
    justify-content: center !important;
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

@media (max-width: 1100px) {
    .stats-grid {
        grid-template-columns: 1fr !important;
        gap: 1rem !important;
    }
    .stat-card.profit-card {
        max-width: 100%;
        margin-bottom: 1rem;
    }
    .stat-cards-group {
        grid-template-columns: 1fr !important;
        gap: 1rem !important;
        position: static !important;
        opacity: 1 !important;
        transform: none !important;
        pointer-events: all !important;
        height: auto;
    }
    .stat-cards-container {
        min-height: unset !important;
        display: block !important;
    }
}

/* Переключатели */
.dashboard-tabs {
    display: flex !important;
    justify-content: center !important;
    gap: 1rem !important;
    margin-bottom: 2rem !important;
}

.tab-button {
    background: white !important;
    border: 2px solid #e2e8f0 !important;
    border-radius: 12px !important;
    padding: 0.75rem 1.5rem !important;
    font-size: 0.9rem !important;
    font-weight: 600 !important;
    color: #64748b !important;
    cursor: pointer !important;
    transition: all 0.3s ease !important;
    display: flex !important;
    align-items: center !important;
    gap: 0.5rem !important;
}

.tab-button:hover {
    border-color: #3b82f6 !important;
    color: #3b82f6 !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15) !important;
}

.tab-button.active {
    background: linear-gradient(135deg, #3b82f6, #60a5fa) !important;
    border-color: #3b82f6 !important;
    color: white !important;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3) !important;
}

.tab-button i {
    font-size: 1rem !important;
}

/* Контейнер для переключаемых карточек */
.stat-cards-container {
    display: contents !important;
}

/* Группы карточек */
.stat-cards-group {
    display: contents !important;
    position: static !important;
    opacity: 1 !important;
    transform: none !important;
    pointer-events: all !important;
    height: auto !important;
    transition: none !important;
}

.stat-cards-group:not(.active) .stat-card {
    display: none !important;
}

/* Дополнительные цвета для новых карточек */
.services-card {
    --card-color: #8b5cf6 !important;
    --card-color-light: #a78bfa !important;
}

.expenses-card {
    --card-color: #ef4444 !important;
    --card-color-light: #f87171 !important;
}

.procedures-card {
    --card-color: #06b6d4 !important;
    --card-color-light: #22d3ee !important;
}

/* Анимация при переключении */
.stat-cards-group.finances-group {
    transform: translateX(-20px) !important;
}

.stat-cards-group.activity-group {
    transform: translateX(20px) !important;
}

.stat-cards-group.active.finances-group,
.stat-cards-group.active.activity-group {
    transform: translateX(0) !important;
}
</style>

    <div class="dashboard-container">
        <h1 class="dashboard-title">CRM Analytics Dashboard</h1>

        <div class="stats-grid">
            <!-- Левая колонка: прибыль -->
            <div class="stat-card profit-card">
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-title">Прибыль</h3>
                    <p class="stat-value">{{ number_format($totalProfit, 2, '.', ' ') }} грн</p>
                    @if($showDynamics)
                        <p class="stat-change positive">
                            <i class="fas fa-arrow-up"></i>
                            15.3% с прошлого месяца
                        </p>
                    @endif
                </div>
            </div>
            <!-- Правая колонка: переключаемые карточки -->
            <div class="stat-cards-container">
                <!-- Финансы -->
                <div class="stat-cards-group finances-group active">
                    <div class="stat-card sales-card">
                        <div class="stat-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-title">Продажи товаров</h3>
                            <p class="stat-value">{{ number_format($productsRevenue, 2, '.', ' ') }} грн</p>
                            @if($showDynamics)
                                <p class="stat-change positive">
                                    <i class="fas fa-arrow-up"></i>
                                    8.7% с прошлого месяца
                                </p>
                            @endif
                        </div>
                    </div>
                    <div class="stat-card services-card">
                        <div class="stat-icon">
                            <i class="fas fa-spa"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-title">Продажи услуг</h3>
                            <p class="stat-value">{{ number_format($servicesRevenue, 2, '.', ' ') }} грн</p>
                            @if($showDynamics)
                                <p class="stat-change positive">
                                    <i class="fas fa-arrow-up"></i>
                                    12.5% с прошлого месяца
                                </p>
                            @endif
                        </div>
                    </div>
                    <div class="stat-card expenses-card">
                        <div class="stat-icon">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-title">Расходы</h3>
                            <p class="stat-value">{{ number_format($totalExpenses, 2, '.', ' ') }} грн</p>
                            @if($showDynamics)
                                <p class="stat-change negative">
                                    <i class="fas fa-arrow-down"></i>
                                    5.2% с прошлого месяца
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
                <!-- Активность -->
                <div class="stat-cards-group activity-group">
                    <div class="stat-card procedures-card">
                        <div class="stat-icon">
                            <i class="fas fa-spa"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-title">Услуги</h3>
                            <p class="stat-value">{{ $servicesCount }}</p>
                            @if($showDynamics)
                                <p class="stat-change positive">
                                    <i class="fas fa-arrow-up"></i>
                                    18.2% с прошлого месяца
                                </p>
                            @endif
                        </div>
                    </div>
                    <div class="stat-card clients-card">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-title">Клиенты</h3>
                            <p class="stat-value">{{ $clientsCount }}</p>
                            @if($showDynamics)
                                <p class="stat-change positive">
                                    <i class="fas fa-arrow-up"></i>
                                    12% с прошлого месяца
                                </p>
                            @endif
                        </div>
                    </div>
                    <div class="stat-card appointments-card">
                        <div class="stat-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-title">Записи</h3>
                            <p class="stat-value">{{ $appointmentsCount }}</p>
                            @if($showDynamics)
                                <p class="stat-change positive">
                                    <i class="fas fa-arrow-up"></i>
                                    5.2% с прошлой недели
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Переключатели -->
        <div class="dashboard-tabs">
            <button class="tab-button active" data-tab="finances">
                <i class="fas fa-chart-pie"></i>
                Финансы
            </button>
            <button class="tab-button" data-tab="activity">
                <i class="fas fa-chart-bar"></i>
                Активность
            </button>
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
        // Анимация счетчика для карточек
        function animateCounter(element, start, end, duration = 1500) {
            const startTime = performance.now();
            const originalText = element.textContent;
            const isCurrency = originalText.includes('грн');
            
            function updateCounter(currentTime) {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                
                // Плавная анимация с easeOutQuart
                const easeProgress = 1 - Math.pow(1 - progress, 4);
                const current = start + (end - start) * easeProgress;
                
                if (isCurrency) {
                    element.textContent = new Intl.NumberFormat('ru-RU', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }).format(current) + ' грн';
                } else {
                    element.textContent = Math.floor(current).toLocaleString('ru-RU');
                }
                
                if (progress < 1) {
                    requestAnimationFrame(updateCounter);
                }
            }
            
            requestAnimationFrame(updateCounter);
        }
        
        // Запускаем анимацию для всех карточек
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.stat-value');
            
            cards.forEach((card, index) => {
                const finalValue = card.textContent;
                const numericValue = parseFloat(finalValue.replace(/[^\d.-]/g, ''));
                
                if (!isNaN(numericValue)) {
                    // Запускаем анимацию с небольшой задержкой для каждой карточки
                    setTimeout(() => {
                        animateCounter(card, 0, numericValue, 1500);
                    }, index * 200);
                }
            });
        });

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

        // Функциональность переключения вкладок
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('.tab-button');
            const cardGroups = document.querySelectorAll('.stat-cards-group');
            
            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const targetTab = this.getAttribute('data-tab');
                    
                    // Убираем активный класс со всех кнопок
                    tabButtons.forEach(btn => btn.classList.remove('active'));
                    
                    // Убираем активный класс со всех групп карточек
                    cardGroups.forEach(group => group.classList.remove('active'));
                    
                    // Добавляем активный класс к нажатой кнопке
                    this.classList.add('active');
                    
                    // Показываем соответствующую группу карточек
                    const targetGroup = document.querySelector(`.${targetTab}-group`);
                    if (targetGroup) {
                        targetGroup.classList.add('active');
                    }
                });
            });
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
