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

/* Стили для period-фильтров */
.period-filters {
    display: flex;
    gap: 0.5rem;
}
.period-btn {
    background: #fff;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 0.5rem 1.2rem;
    font-size: 0.95rem;
    font-weight: 600;
    color: #64748b;
    cursor: pointer;
    transition: all 0.3s;
    outline: none;
}
.period-btn:hover {
    border-color: #3b82f6;
    color: #3b82f6;
    box-shadow: 0 2px 8px rgba(59,130,246,0.10);
}
.period-btn.active {
    background: linear-gradient(135deg, #3b82f6, #60a5fa);
    border-color: #3b82f6;
    color: #fff;
    box-shadow: 0 4px 12px rgba(59,130,246,0.15);
}

/* Удаляю все ::after для .metric-toggle, если есть */
.metric-toggle::after {
    display: none !important;
    content: none !important;
}

.metric-dropdown {
    position: relative;
}
.metric-toggle {
    background: #fff;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    padding: 0.6rem 1.2rem;
    font-size: 1rem;
    font-weight: 600;
    color: #374151;
    box-shadow: 0 2px 8px rgba(59,130,246,0.04);
    transition: border 0.2s, box-shadow 0.2s;
}
.metric-toggle:focus, .metric-toggle:hover {
    border-color: #3b82f6;
    box-shadow: 0 4px 16px rgba(59,130,246,0.10);
}
.metric-menu {
    display: none;
    position: absolute;
    left: 0;
    top: 110%;
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 8px 32px rgba(59,130,246,0.13), 0 1.5px 6px rgba(0,0,0,0.04);
    min-width: 190px;
    padding: 0.4rem 0;
    z-index: 10;
    border: none;
    opacity: 0;
    transform: translateY(10px) scale(0.98);
    pointer-events: none;
    transition: opacity 0.22s cubic-bezier(.4,0,.2,1), transform 0.22s cubic-bezier(.4,0,.2,1);
}
.metric-dropdown.open .metric-menu {
    display: block;
    opacity: 1;
    transform: translateY(0) scale(1);
    pointer-events: auto;
}
.metric-item {
    width: 100%;
    background: none;
    border: none;
    outline: none;
    text-align: left;
    padding: 0.7rem 1.2rem;
    font-size: 1rem;
    color: #374151;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.18s, color 0.18s;
    border-radius: 8px;
    display: flex;
    align-items: center;
    gap: 0.7rem;
}
.metric-item:hover, .metric-item:focus {
    background: linear-gradient(90deg, #e0e7ff 0%, #f0f9ff 100%);
    color: #3b82f6;
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
            <div class="chart-toolbar" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
                <!-- Dropdown слева -->
                <div class="dropdown metric-dropdown" style="position: relative;">
                    <button class="dropdown-toggle metric-toggle" type="button" style="display: flex; align-items: center; gap: 0.5rem; background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.5rem 1rem; font-weight: 600; cursor: pointer; min-width: 140px;">
                        <i class="fas fa-chart-line"></i>
                        <span id="selectedMetricLabel">Прибыль</span>
                        <i class="fas fa-chevron-down" style="margin-left: 0.5rem;"></i>
                    </button>
                    <div class="dropdown-menu metric-menu">
                        <button class="dropdown-item metric-item" data-type="profit"><i class="fas fa-chart-line"></i> Прибыль</button>
                        <button class="dropdown-item metric-item" data-type="sales"><i class="fas fa-shopping-cart"></i> Продажи товаров</button>
                        <button class="dropdown-item metric-item" data-type="services"><i class="fas fa-spa"></i> Продажи услуг</button>
                        <button class="dropdown-item metric-item" data-type="expenses"><i class="fas fa-credit-card"></i> Расходы</button>
                        <button class="dropdown-item metric-item" data-type="activity"><i class="fas fa-bolt"></i> Активность</button>
                    </div>
                </div>
                <!-- Фильтры справа -->
                <div class="period-filters" style="display: flex; gap: 0.5rem;">
                    <button class="period-btn" data-period="30">За месяц</button>
                    <button class="period-btn" data-period="90">За 3 месяца</button>
                    <button class="period-btn" data-period="180">За 6 месяцев</button>
                    <button class="period-btn" data-period="365">За год</button>
                </div>
            </div>
            <canvas id="universalChart" height="150"></canvas>
        </div>
    </div>

    <!-- Подключаем Font Awesome для иконок -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Подключаем Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        let currentMetric = 'profit';
        let currentPeriod = '30';

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

        // Данные для графика (пример)
        const datasets = {
            profit: {
                label: "Прибыль",
                icon: "fa-chart-line",
                data: {
                    7: [18000, 17500, 17000, 16800, 16500, 16200, 16000],
                    30: [10000, 12000, 15000, 14000, 16000, 18000, 17500, 17000, 16800, 16500, 16200, 16000, 15800, 15500, 15300, 15000, 14800, 14500, 14300, 14000, 13800, 13500, 13300, 13000, 12800, 12500, 12300, 12000, 11800, 11500],
                    90: [10000, 10500, 11000, 11500, 12000, 12500, 13000, 13500, 14000, 14500, 15000, 15500, 16000, 16500, 17000, 17500, 18000, 18500, 19000, 19500, 20000, 20500, 21000, 21500, 22000, 22500, 23000, 23500, 24000, 24500, 25000, 25500, 26000, 26500, 27000, 27500, 28000, 28500, 29000, 29500, 30000, 30500, 31000, 31500, 32000, 32500, 33000, 33500, 34000, 34500, 35000, 35500, 36000, 36500, 37000, 37500, 38000, 38500, 39000, 39500, 40000, 40500, 41000, 41500, 42000, 42500, 43000, 43500, 44000, 44500, 45000, 45500, 46000, 46500, 47000, 47500, 48000, 48500, 49000, 49500]
                }
            },
            sales: {
                label: "Продажи товаров",
                icon: "fa-shopping-cart",
                data: {
                    7: [14000, 13500, 13000, 12800, 12500, 12200, 12000],
                    30: [8000, 9000, 11000, 13000, 12500, 14000, 13500, 13000, 12800, 12500, 12200, 12000, 11800, 11500, 11300, 11000, 10800, 10500, 10300, 10000, 9800, 9500, 9300, 9000, 8800, 8500, 8300, 8000, 7800, 7500],
                    90: [8000, 8500, 9000, 9500, 10000, 10500, 11000, 11500, 12000, 12500, 13000, 13500, 14000, 14500, 15000, 15500, 16000, 16500, 17000, 17500, 18000, 18500, 19000, 19500, 20000, 20500, 21000, 21500, 22000, 22500, 23000, 23500, 24000, 24500, 25000, 25500, 26000, 26500, 27000, 27500, 28000, 28500, 29000, 29500, 30000, 30500, 31000, 31500, 32000, 32500, 33000, 33500, 34000, 34500, 35000, 35500, 36000, 36500, 37000, 37500, 38000, 38500, 39000, 39500, 40000, 40500, 41000, 41500, 42000, 42500, 43000, 43500, 44000, 44500, 45000, 45500, 46000, 46500, 47000, 47500]
                }
            },
            services: {
                label: "Продажи услуг",
                icon: "fa-spa",
                data: {
                    7: [4500, 4400, 4300, 4280, 4250, 4220, 4200],
                    30: [3000, 3500, 4000, 3800, 4100, 4500, 4400, 4300, 4280, 4250, 4220, 4200, 4180, 4150, 4130, 4100, 4080, 4050, 4030, 4000, 3980, 3950, 3930, 3900, 3880, 3850, 3830, 3800, 3780, 3750],
                    90: [3000, 3200, 3400, 3600, 3800, 4000, 4200, 4400, 4600, 4800, 5000, 5200, 5400, 5600, 5800, 6000, 6200, 6400, 6600, 6800, 7000, 7200, 7400, 7600, 7800, 8000, 8200, 8400, 8600, 8800, 9000, 9200, 9400, 9600, 9800, 10000, 10200, 10400, 10600, 10800, 11000, 11200, 11400, 11600, 11800, 12000, 12200, 12400, 12600, 12800, 13000, 13200, 13400, 13600, 13800, 14000, 14200, 14400, 14600, 14800, 15000, 15200, 15400, 15600, 15800, 16000, 16200, 16400, 16600, 16800, 17000, 17200, 17400, 17600, 17800, 18000, 18200, 18400, 18600, 18800]
                }
            },
            expenses: {
                label: "Расходы",
                icon: "fa-credit-card",
                data: {
                    7: [2900, 2850, 2800, 2780, 2750, 2720, 2700],
                    30: [2000, 2500, 3000, 2800, 2700, 2900, 2850, 2800, 2780, 2750, 2720, 2700, 2680, 2650, 2630, 2600, 2580, 2550, 2530, 2500, 2480, 2450, 2430, 2400, 2380, 2350, 2330, 2300, 2280, 2250],
                    90: [2000, 2100, 2200, 2300, 2400, 2500, 2600, 2700, 2800, 2900, 3000, 3100, 3200, 3300, 3400, 3500, 3600, 3700, 3800, 3900, 4000, 4100, 4200, 4300, 4400, 4500, 4600, 4700, 4800, 4900, 5000, 5100, 5200, 5300, 5400, 5500, 5600, 5700, 5800, 5900, 6000, 6100, 6200, 6300, 6400, 6500, 6600, 6700, 6800, 6900, 7000, 7100, 7200, 7300, 7400, 7500, 7600, 7700, 7800, 7900, 8000, 8100, 8200, 8300, 8400, 8500, 8600, 8700, 8800, 8900, 9000, 9100, 9200, 9300, 9400, 9500, 9600, 9700, 9800, 9900]
                }
            },
            clients: {
                label: "Клиенты",
                icon: "fa-users",
                data: {
                    7: [90, 88, 85, 83, 80, 78, 75],
                    30: [50, 60, 65, 80, 75, 90, 88, 85, 83, 80, 78, 75, 73, 70, 68, 65, 63, 60, 58, 55, 53, 50, 48, 45, 43, 40, 38, 35, 33, 30],
                    90: [50, 52, 54, 56, 58, 60, 62, 64, 66, 68, 70, 72, 74, 76, 78, 80, 82, 84, 86, 88, 90, 92, 94, 96, 98, 100, 102, 104, 106, 108, 110, 112, 114, 116, 118, 120, 122, 124, 126, 128, 130, 132, 134, 136, 138, 140, 142, 144, 146, 148, 150, 152, 154, 156, 158, 160, 162, 164, 166, 168, 170, 172, 174, 176, 178, 180, 182, 184, 186, 188, 190, 192, 194, 196, 198, 200, 202, 204, 206, 208]
                }
            },
            appointments: {
                label: "Записи",
                icon: "fa-calendar-check",
                data: {
                    7: [35, 34, 33, 32, 31, 30, 28],
                    30: [20, 25, 22, 30, 28, 35, 34, 33, 32, 31, 30, 28, 27, 26, 25, 24, 23, 22, 21, 20, 19, 18, 17, 16, 15, 14, 13, 12, 11, 10],
                    90: [20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99]
                }
            }
        };
        // Формируем activity только после определения всех метрик
        datasets.activity = {
            label: 'Активность',
            icon: 'fa-bolt',
            data: {
                clients: datasets.clients ? datasets.clients.data : {},
                appointments: datasets.appointments ? datasets.appointments.data : {},
                services: datasets.services ? datasets.services.data : {}
            }
        };
        // Лейблы для разных периодов
        const periodLabels = {
            7: ['6 дн', '5 дн', '4 дн', '3 дн', '2 дн', 'Вчера', 'Сегодня'],
            30: Array.from({length: 30}, (_, i) => `${30-i} дн назад`).reverse(),
            90: Array.from({length: 90}, (_, i) => `${90-i} дн назад`).reverse()
        };
        function getChartLabels(period) {
            if (period === '7') return getLastNDates(7);
            if (period === '30') return getWeekStartDates(4);
            if (period === '90') return getWeekStartDates(13);
            return [];
        }
        function getActivityDatasets(period) {
            return [
                {
                    label: 'Клиенты',
                    data: datasets.activity.data.clients[period],
                    borderColor: '#8b5cf6',
                    backgroundColor: '#8b5cf6' + '33',
                    tension: 0.4,
                    fill: false,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    pointHitRadius: 12,
                    spanGaps: true
                },
                {
                    label: 'Записи',
                    data: datasets.activity.data.appointments[period],
                    borderColor: '#f59e0b',
                    backgroundColor: '#f59e0b' + '33',
                    tension: 0.4,
                    fill: false,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    pointHitRadius: 12,
                    spanGaps: true
                },
                {
                    label: 'Продажи услуг',
                    data: datasets.activity.data.services[period],
                    borderColor: '#8b5cf6',
                    backgroundColor: '#8b5cf6' + '33',
                    tension: 0.4,
                    fill: false,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    pointHitRadius: 12,
                    spanGaps: true
                }
            ];
        }
        const ctx = document.getElementById('universalChart').getContext('2d');
        let universalChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: getLastNDates(7),
                datasets: [{
                    label: datasets[currentMetric].label,
                    data: currentMetric === 'profit' ? [] : datasets[currentMetric].data['7'],
                    borderColor: getMetricColor(currentMetric),
                    backgroundColor: getMetricColor(currentMetric) + '33',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    pointHitRadius: 12,
                    spanGaps: true
                }]
            },
            options: {
                responsive: true,
                animation: true,
                plugins: {
                    legend: { display: true },
                    tooltip: { mode: 'index', intersect: false },
                    decimation: {
                        enabled: true,
                        algorithm: 'min-max'
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            callback: function(value, index, ticks) {
                                if (currentPeriod === '180' || currentPeriod === '365') {
                                    return getMonthLabels(this.getLabels().length)[index];
                                }
                                if (currentPeriod === '7') return this.getLabelForValue(this.getLabels()[index]);
                                const date = new Date();
                                date.setDate(date.getDate() - (this.getLabels().length - 1 - index));
                                if (date.getDay() === 1) {
                                    return this.getLabelForValue(this.getLabels()[index]);
                                }
                                return '';
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        max: undefined
                    }
                }
            }
        });
        // Dropdown логика
        const metricToggle = document.querySelector('.metric-toggle');
        const metricMenu = document.querySelector('.metric-menu');
        const metricDropdown = document.querySelector('.metric-dropdown');
        const selectedMetricLabel = document.getElementById('selectedMetricLabel');
        metricToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            metricDropdown.classList.toggle('open');
        });
        document.addEventListener('click', function() {
            metricDropdown.classList.remove('open');
        });
        document.querySelectorAll('.metric-item').forEach(item => {
            item.addEventListener('click', function() {
                const type = this.dataset.type;
                currentMetric = type;
                selectedMetricLabel.textContent = datasets[type].label;
                metricToggle.querySelector('i').className = 'fas ' + datasets[type].icon;

                if (type === 'profit') {
                    fetch(`/api/dashboard/profit-chart?period=${currentPeriod}`)
                        .then(res => res.json())
                        .then(res => {
                            universalChart.data.labels = res.labels;
                            universalChart.data.datasets = [{
                                label: 'Прибыль',
                                data: getCumulativeData(res.data),
                                borderColor: getMetricColor('profit'),
                                backgroundColor: getMetricColor('profit') + '33',
                                tension: 0.4,
                                fill: true,
                                pointRadius: 0,
                                pointHoverRadius: 6,
                                pointHitRadius: 12,
                                spanGaps: true
                            }];
                            // Устанавливаем максимум оси Y на 15% выше максимального значения
                            const maxValue = Math.max(...getCumulativeData(res.data));
                            universalChart.options.scales.y.max = maxValue > 0 ? Math.ceil(maxValue * 1.15) : undefined;
                            universalChart.update();
                        });
                    return;
                }
                if (type === 'sales') {
                    fetch(`/api/dashboard/sales-chart?period=${currentPeriod}`)
                        .then(res => res.json())
                        .then(res => {
                            if (currentPeriod === '7') {
                                universalChart.data.labels = res.labels;
                                universalChart.data.datasets = [{
                                    label: datasets['sales'].label,
                                    data: res.data,
                                    borderColor: getMetricColor('sales'),
                                    backgroundColor: getMetricColor('sales') + '33',
                                    tension: 0.4,
                                    fill: true,
                                    pointRadius: 0,
                                    pointHoverRadius: 6,
                                    pointHitRadius: 12,
                                    spanGaps: true
                                }];
                                universalChart.options.scales.x.ticks.callback = function(value, index, ticks) {
                                    return this.getLabelForValue(this.getLabels()[index]);
                                };
                            } else if (currentPeriod === '30' || currentPeriod === '90') {
                                universalChart.data.labels = res.labels;
                                universalChart.data.datasets = [{
                                    label: datasets['sales'].label,
                                    data: res.data,
                                    borderColor: getMetricColor('sales'),
                                    backgroundColor: getMetricColor('sales') + '33',
                                    tension: 0.4,
                                    fill: true,
                                    pointRadius: 0,
                                    pointHoverRadius: 6,
                                    pointHitRadius: 12,
                                    spanGaps: true
                                }];
                                universalChart.options.scales.x.ticks.callback = function(value, index, ticks) {
                                    const label = this.getLabelForValue(this.getLabels()[index]);
                                    const parts = label.split(' ');
                                    if (parts.length === 2) {
                                        const day = parseInt(parts[0]);
                                        const month = parts[1];
                                        const date = new Date();
                                        date.setDate(day);
                                        const now = new Date();
                                        const d = new Date(now);
                                        d.setDate(now.getDate() - (this.getLabels().length - 1 - index));
                                        if (d.getDay() === 1) {
                                            return label;
                                        }
                                    }
                                    return '';
                                };
                            }
                            // Устанавливаем максимум оси Y на 15% выше максимального значения
                            const maxValue = Math.max(...res.data);
                            universalChart.options.scales.y.max = maxValue > 0 ? Math.ceil(maxValue * 1.15) : undefined;
                            universalChart.update();
                        });
                    return;
                }
                if (type === 'services') {
                    fetch(`/api/dashboard/services-chart?period=${currentPeriod}`)
                        .then(res => res.json())
                        .then(res => {
                            if (currentPeriod === '7') {
                                universalChart.data.labels = res.labels;
                                universalChart.data.datasets = [{
                                    label: datasets['services'].label,
                                    data: res.data,
                                    borderColor: getMetricColor('services'),
                                    backgroundColor: getMetricColor('services') + '33',
                                    tension: 0.4,
                                    fill: true,
                                    pointRadius: 0,
                                    pointHoverRadius: 6,
                                    pointHitRadius: 12,
                                    spanGaps: true
                                }];
                                universalChart.options.scales.x.ticks.callback = function(value, index, ticks) {
                                    return this.getLabelForValue(this.getLabels()[index]);
                                };
                            } else if (currentPeriod === '30' || currentPeriod === '90') {
                                universalChart.data.labels = res.labels;
                                universalChart.data.datasets = [{
                                    label: datasets['services'].label,
                                    data: res.data,
                                    borderColor: getMetricColor('services'),
                                    backgroundColor: getMetricColor('services') + '33',
                                    tension: 0.4,
                                    fill: true,
                                    pointRadius: 0,
                                    pointHoverRadius: 6,
                                    pointHitRadius: 12,
                                    spanGaps: true
                                }];
                                universalChart.options.scales.x.ticks.callback = function(value, index, ticks) {
                                    const label = this.getLabelForValue(this.getLabels()[index]);
                                    const parts = label.split(' ');
                                    if (parts.length === 2) {
                                        const day = parseInt(parts[0]);
                                        const month = parts[1];
                                        const date = new Date();
                                        date.setDate(day);
                                        const now = new Date();
                                        const d = new Date(now);
                                        d.setDate(now.getDate() - (this.getLabels().length - 1 - index));
                                        if (d.getDay() === 1) {
                                            return label;
                                        }
                                    }
                                    return '';
                                };
                            }
                            // Устанавливаем максимум оси Y на 15% выше максимального значения
                            const maxValue = Math.max(...res.data);
                            universalChart.options.scales.y.max = maxValue > 0 ? Math.ceil(maxValue * 1.15) : undefined;
                            universalChart.update();
                        });
                    return;
                }
                if (type === 'activity') {
                    universalChart.data.labels = getLastNDates(currentPeriod);
                    universalChart.data.datasets = getActivityDatasets(currentPeriod);
                    universalChart.update();
                    return;
                }
            });
        });
        // Фильтры периода
        document.querySelectorAll('.period-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.period-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                currentPeriod = this.dataset.period;
                console.log('currentMetric:', currentMetric, 'currentPeriod:', currentPeriod, 'typeof:', typeof currentMetric);
                if (currentMetric === 'sales') {
                    fetch(`/api/dashboard/sales-chart?period=${currentPeriod}`)
                        .then(res => res.json())
                        .then(res => {
                            universalChart.data.labels = res.labels;
                            universalChart.data.datasets = [{
                                label: datasets['sales'].label,
                                data: res.data,
                                borderColor: getMetricColor('sales'),
                                backgroundColor: getMetricColor('sales') + '33',
                                tension: 0.4,
                                fill: true,
                                pointRadius: 0,
                                pointHoverRadius: 6,
                                pointHitRadius: 12,
                                spanGaps: true
                            }];
                            universalChart.options.scales.x.ticks.callback = function(value, index, ticks) {
                                if (currentPeriod === '7') {
                                    return this.getLabelForValue(this.getLabels()[index]);
                                } else {
                                    const now = new Date();
                                    const d = new Date(now);
                                    d.setDate(now.getDate() - (this.getLabels().length - 1 - index));
                                    if (d.getDay() === 1) {
                                        return this.getLabelForValue(this.getLabels()[index]);
                                    }
                                    return '';
                                }
                            };
                            // Устанавливаем максимум оси Y на 15% выше максимального значения
                            const maxValue = Math.max(...res.data);
                            universalChart.options.scales.y.max = maxValue > 0 ? Math.ceil(maxValue * 1.15) : undefined;
                            universalChart.update();
                        });
                    return;
                } else if (currentMetric === 'services') {
                    fetch(`/api/dashboard/services-chart?period=${currentPeriod}`)
                        .then(res => res.json())
                        .then(res => {
                            universalChart.data.labels = res.labels;
                            universalChart.data.datasets = [{
                                label: datasets['services'].label,
                                data: res.data,
                                borderColor: getMetricColor('services'),
                                backgroundColor: getMetricColor('services') + '33',
                                tension: 0.4,
                                fill: true,
                                pointRadius: 0,
                                pointHoverRadius: 6,
                                pointHitRadius: 12,
                                spanGaps: true
                            }];
                            universalChart.options.scales.x.ticks.callback = function(value, index, ticks) {
                                if (currentPeriod === '7') {
                                    return this.getLabelForValue(this.getLabels()[index]);
                                } else {
                                    const now = new Date();
                                    const d = new Date(now);
                                    d.setDate(now.getDate() - (this.getLabels().length - 1 - index));
                                    if (d.getDay() === 1) {
                                        return this.getLabelForValue(this.getLabels()[index]);
                                    }
                                    return '';
                                }
                            };
                            // Устанавливаем максимум оси Y на 15% выше максимального значения
                            const maxValue = Math.max(...res.data);
                            universalChart.options.scales.y.max = maxValue > 0 ? Math.ceil(maxValue * 1.15) : undefined;
                            universalChart.update();
                        });
                    return;
                } else if (currentMetric === 'activity') {
                    universalChart.data.labels = getLastNDates(currentPeriod);
                    universalChart.data.datasets = getActivityDatasets(currentPeriod);
                    universalChart.update();
                    return;
                } else if (currentMetric === 'profit') {
                    fetch(`/api/dashboard/profit-chart?period=${currentPeriod}`)
                        .then(res => res.json())
                        .then(res => {
                            universalChart.data.labels = res.labels;
                            universalChart.data.datasets = [{
                                label: 'Прибыль',
                                data: getCumulativeData(res.data),
                                borderColor: getMetricColor('profit'),
                                backgroundColor: getMetricColor('profit') + '33',
                                tension: 0.4,
                                fill: true,
                                pointRadius: 0,
                                pointHoverRadius: 6,
                                pointHitRadius: 12,
                                spanGaps: true
                            }];
                            // Устанавливаем максимум оси Y на 15% выше максимального значения
                            const maxValue = Math.max(...getCumulativeData(res.data));
                            universalChart.options.scales.y.max = maxValue > 0 ? Math.ceil(maxValue * 1.15) : undefined;
                            universalChart.update();
                        });
                    return;
                } else {
                    universalChart.data.labels = getLastNDates(currentPeriod);
                    universalChart.data.datasets = [{
                        label: datasets[currentMetric].label,
                        data: datasets[currentMetric].data[currentPeriod],
                        borderColor: getMetricColor(currentMetric),
                        backgroundColor: getMetricColor(currentMetric) + '33',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 0,
                        pointHoverRadius: 6,
                        pointHitRadius: 12,
                        spanGaps: true
                    }];
                    // Устанавливаем максимум оси Y на 15% выше максимального значения
                    const maxValue = Math.max(...datasets[currentMetric].data[currentPeriod]);
                    universalChart.options.scales.y.max = maxValue > 0 ? Math.ceil(maxValue * 1.15) : undefined;
                    universalChart.update();
                }
            });
        });
        // Цвета для метрик
        function getMetricColor(type) {
            const colors = {
                profit: '#10b981', // зелёный
                sales: '#3b82f6', // синий
                services: '#8b5cf6', // фиолетовый
                expenses: '#ef4444', // красный
                clients: '#8b5cf6', // фиолетовый
                appointments: '#f59e0b' // оранжевый
            };
            return colors[type] || '#10b981';
        }
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

        function getLastNDates(n) {
            const arr = [];
            const now = new Date();
            for (let i = n - 1; i >= 0; i--) {
                const d = new Date(now);
                d.setDate(now.getDate() - i);
                arr.push(d.toLocaleDateString('ru-RU', { day: 'numeric', month: 'short' }).replace('.', ''));
            }
            return arr;
        }

        function getWeekStartDates(weeks) {
            const arr = [];
            const now = new Date();
            let monday = new Date(now);
            monday.setDate(now.getDate() - ((now.getDay() + 6) % 7));
            for (let i = weeks - 1; i >= 0; i--) {
                const d = new Date(monday);
                d.setDate(monday.getDate() - i * 7);
                arr.push(d.toLocaleDateString('ru-RU', { day: 'numeric', month: 'short' }).replace('.', ''));
            }
            return arr;
        }

        // Функция для получения накопительных данных
        function getCumulativeData(arr) {
            let result = [];
            let sum = 0;
            for (let i = 0; i < arr.length; i++) {
                sum += arr[i];
                result.push(Number(sum.toFixed(2)));
            }
            return result;
        }

        // По умолчанию активна кнопка 'За месяц'
        document.querySelectorAll('.period-btn').forEach(btn => {
            btn.classList.remove('active');
            if (btn.getAttribute('data-period') === '30') btn.classList.add('active');
        });

        // Добавляю функцию для генерации подписей месяцев
        function getMonthLabels(n) {
            const arr = [];
            const now = new Date();
            let prevMonth = null;
            for (let i = n - 1; i >= 0; i--) {
                const d = new Date(now);
                d.setDate(now.getDate() - i);
                const month = d.toLocaleDateString('ru-RU', { month: 'short' });
                if (prevMonth !== month) {
                    arr.push(month.charAt(0).toUpperCase() + month.slice(1));
                    prevMonth = month;
                } else {
                    arr.push('');
                }
            }
            return arr;
        }
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
