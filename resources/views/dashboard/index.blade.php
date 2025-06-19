@extends('layouts.app')

@section('content')
<style>
/* Dashboard Statistics Cards - Inline Styles */
body {
    background: #f8f9fa !important;
    margin: 0 !important;
    padding: 0 !important;
}

.dashboard-container {
    padding: 24px !important;
    background: #fff !important;
    border-radius: 16px !important;
    box-shadow: 0 4px 24px rgba(0,0,0,0.08) !important;
    border: 1px solid #e5e7eb !important;
    min-height: 100vh !important;
    max-width: 1400px !important;
    margin: 32px auto 0 auto !important;
    width: 100% !important;
    box-sizing: border-box !important;
    visibility: visible !important;
    opacity: 1 !important;
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
    max-width: 100% !important;
    width: 100% !important;
    visibility: visible !important;
    opacity: 1 !important;
}

.stat-card {
    height: 150px !important;
    min-width: 0 !important;
    max-width: 100% !important;
    width: 100% !important;
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
    flex-shrink: 0 !important;
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
    opacity: 0 !important;
    transition: opacity 0.3s ease !important;
    min-width: 80px !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

.stat-value.animated {
    opacity: 1 !important;
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
        <div class="chart-container" style="width: 100%; max-width: 100%; padding: 8px 0 0 0; box-sizing: border-box;">
            <h3 class="chart-title">Динамика показателей</h3>
            <div class="chart-buttons" style="margin-bottom: 1rem; display: flex; gap: 0.5rem; flex-wrap: wrap;">
                <button class="tab-button active" data-type="profit">Прибыль</button>
                <button class="tab-button" data-type="sales">Продажи товаров</button>
                <button class="tab-button" data-type="services">Услуги</button>
                <button class="tab-button" data-type="expenses">Расходы</button>
                <button class="tab-button" data-type="clients">Клиенты</button>
                <button class="tab-button" data-type="appointments">Записи</button>
            </div>
            <canvas id="universalChart" height="150"></canvas>
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
                const isCurrency = finalValue.includes('грн');
                
                if (!isNaN(numericValue)) {
                    // Сразу устанавливаем 0 и показываем элемент
                    card.textContent = '0' + (isCurrency ? ' грн' : '');
                    card.classList.add('animated');
                    
                    // Запускаем анимацию с небольшой задержкой для каждой карточки
                    setTimeout(() => {
                        animateCounter(card, 0, numericValue, 1500);
                    }, index * 200);
                } else {
                    // Для нечисловых значений просто показываем
                    card.classList.add('animated');
                }
            });
        });

        const datasets = {
            profit: {
                label: "Прибыль",
                data: [10000, 12000, 15000, 14000, 16000, 18000],
                color: "#10b981"
            },
            sales: {
                label: "Продажи товаров",
                data: [8000, 9000, 11000, 13000, 12500, 14000],
                color: "#3b82f6"
            },
            services: {
                label: "Услуги",
                data: [3000, 3500, 4000, 3800, 4100, 4500],
                color: "#8b5cf6"
            },
            expenses: {
                label: "Расходы",
                data: [2000, 2500, 3000, 2800, 2700, 2900],
                color: "#ef4444"
            },
            clients: {
                label: "Клиенты",
                data: [50, 60, 65, 80, 75, 90],
                color: "#8b5cf6"
            },
            appointments: {
                label: "Записи",
                data: [20, 25, 22, 30, 28, 35],
                color: "#f59e0b"
            }
        };

        const ctx = document.getElementById('universalChart').getContext('2d');
        let universalChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн'],
                datasets: [{
                    label: datasets.profit.label,
                    data: datasets.profit.data,
                    borderColor: datasets.profit.color,
                    backgroundColor: datasets.profit.color + '33',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: { mode: 'index', intersect: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        document.querySelectorAll('.chart-buttons .tab-button').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.chart-buttons .tab-button').forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                const type = this.dataset.type;
                const ds = datasets[type];

                universalChart.data.datasets[0].label = ds.label;
                universalChart.data.datasets[0].data = ds.data;
                universalChart.data.datasets[0].borderColor = ds.color;
                universalChart.data.datasets[0].backgroundColor = ds.color + '33';
                universalChart.update();
            });
        });
        // ВОССТАНАВЛИВАЮ переключение вкладок Финансы/Активность
        document.querySelectorAll('.dashboard-tabs .tab-button').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.dashboard-tabs .tab-button').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                const tab = this.getAttribute('data-tab');
                document.querySelectorAll('.stat-cards-group').forEach(group => group.classList.remove('active'));
                const target = document.querySelector(`.stat-cards-group.${tab}-group`);
                if(target) target.classList.add('active');
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

            .chart-container {
                padding: 10px;
            }

            .dashboard-title {
                font-size: 24px;
            }
        }
    </style>
@endsection
