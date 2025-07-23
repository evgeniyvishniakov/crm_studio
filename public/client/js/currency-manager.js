/**
 * Глобальный менеджер валют
 * Автоматически загружает валюты из базы данных и обновляет интерфейс
 */
class CurrencyManager {
    constructor() {
        this.currencies = [];
        this.currentCurrency = null;
        this.defaultCurrency = null;
        this.isLoaded = false;
        this.loadingPromise = null;
        
        // Инициализация
        this.init();
    }

    /**
     * Инициализация менеджера
     */
    async init() {
        try {
            // Очищаем старый кэш для отладки
            localStorage.removeItem('currencies');
            console.log('Кэш валют очищен');
            
            await this.loadCurrencies();
            this.setupEventListeners();
            this.updateAllCurrencyDisplays();
        } catch (error) {
            console.error('Ошибка инициализации CurrencyManager:', error);
            this.setupFallback();
        }
    }

    /**
     * Загрузка валют из API
     */
    async loadCurrencies() {
        if (this.loadingPromise) {
            return this.loadingPromise;
        }

        this.loadingPromise = fetch('/api/currencies')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Данные валют получены:', data);
                    this.currencies = data.currencies;
                    // Приоритет: текущая валюта (уже определена на сервере) > валюта по умолчанию
                    this.currentCurrency = data.current || data.default;
                    this.defaultCurrency = data.default;
                    this.isLoaded = true;
                    console.log('Текущая валюта установлена:', this.currentCurrency);
                    
                    // Сохраняем в localStorage для кэширования
                    localStorage.setItem('currencies', JSON.stringify({
                        currencies: this.currencies,
                        current: this.currentCurrency,
                        default: this.defaultCurrency,
                        timestamp: Date.now()
                    }));

                    // Обновляем все отображения валют на странице
                    this.updateAllCurrencyDisplays();
                } else {
                    throw new Error('Не удалось загрузить валюты');
                }
            })
            .catch(error => {
                console.error('Ошибка загрузки валют:', error);
                // Пробуем загрузить из кэша
                this.loadFromCache();
                throw error;
            });

        return this.loadingPromise;
    }

    /**
     * Загрузка из кэша localStorage
     */
    loadFromCache() {
        const cached = localStorage.getItem('currencies');
        if (cached) {
            try {
                const data = JSON.parse(cached);
                // Проверяем, что кэш не старше 1 часа
                if (Date.now() - data.timestamp < 3600000) {
                    this.currencies = data.currencies;
                    this.currentCurrency = data.current;
                    this.defaultCurrency = data.default;
                    this.isLoaded = true;
                    
                    // Обновляем все отображения валют на странице
                    this.updateAllCurrencyDisplays();
                    
                    return true;
                }
            } catch (error) {
                console.error('Ошибка загрузки из кэша:', error);
            }
        }
        return false;
    }

    /**
     * Получить текущую валюту
     */
    getCurrentCurrency() {
        return this.currencies.find(c => c.code === this.currentCurrency) || 
               this.currencies.find(c => c.is_default) ||
               this.currencies[0];
    }

    /**
     * Получить валюту по коду
     */
    getCurrencyByCode(code) {
        return this.currencies.find(c => c.code === code);
    }

    /**
     * Форматировать сумму
     */
    formatAmount(amount, currencyCode = null) {
        if (!this.isLoaded) {
            return this.formatFallback(amount);
        }

        const currency = currencyCode ? 
            this.getCurrencyByCode(currencyCode) : 
            this.getCurrentCurrency();

        if (!currency) {
            return this.formatFallback(amount);
        }

        const amountNum = parseFloat(amount) || 0;
        
        // Проверяем, есть ли копейки
        const hasDecimals = amountNum % 1 !== 0;
        
        // Форматируем число без разделителей тысяч и с копейками только если они есть
        let formatted;
        if (hasDecimals) {
            // Есть копейки - показываем их
            formatted = amountNum.toFixed(currency.decimal_places)
                .replace(/\./g, currency.decimal_separator);
        } else {
            // Нет копеек - показываем только целую часть
            formatted = Math.floor(amountNum).toString();
        }

        // Добавляем символ валюты
        if (currency.symbol_position === 'before') {
            return `${currency.symbol}${formatted}`;
        } else {
            return `${formatted} ${currency.symbol}`;
        }
    }

    /**
     * Fallback форматирование
     */
    formatFallback(amount) {
        const amountNum = parseFloat(amount) || 0;
        const hasDecimals = amountNum % 1 !== 0;
        
        let formatted;
        if (hasDecimals) {
            formatted = amountNum.toFixed(2);
        } else {
            formatted = Math.floor(amountNum).toString();
        }
        
        return formatted + ' ₴';
    }

    /**
     * Изменить валюту
     */
    async changeCurrency(code) {
        try {
            console.log('Changing currency to:', code);
            const url = `/api/currencies/set/${code}`;
            console.log('Request URL:', url);
            
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json',
                }
            });

            const data = await response.json();
            
            if (data.success) {
                this.currentCurrency = code;
                this.updateAllCurrencyDisplays();
                
                // Показываем уведомление
                if (window.showNotification) {
                    window.showNotification('success', `Валюта изменена на ${data.currency.name}`);
                }
                
                return true;
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            console.error('Ошибка изменения валюты:', error);
            if (window.showNotification) {
                window.showNotification('error', 'Ошибка изменения валюты');
            }
            return false;
        }
    }

    /**
     * Обновить все отображения валют на странице
     */
    updateAllCurrencyDisplays() {
        // Обновляем элементы с классом currency-amount
        const elements = document.querySelectorAll('.currency-amount');
        
        elements.forEach((element) => {
            const amount = element.getAttribute('data-amount') || element.textContent;
            if (amount) {
                const formatted = this.formatAmount(amount);
                element.textContent = formatted;
            }
        });



        // Обновляем графики, если есть
        this.updateCharts();
    }

    /**
     * Обновить графики
     */
    updateCharts() {
        // Обновляем Chart.js графики
        if (window.Chart && window.Chart.instances) {
            Object.values(window.Chart.instances).forEach(chart => {
                if (chart.config && chart.config.options && chart.config.options.plugins) {
                    // Обновляем tooltips
                    if (chart.config.options.plugins.tooltip) {
                        chart.config.options.plugins.tooltip.callbacks = {
                            ...chart.config.options.plugins.tooltip.callbacks,
                            label: (context) => {
                                const label = context.dataset.label || '';
                                const value = context.parsed.y || context.parsed;
                                return `${label}: ${this.formatAmount(value)}`;
                            }
                        };
                    }
                    
                    // Обновляем оси Y
                    if (chart.config.options.scales && chart.config.options.scales.y) {
                        chart.config.options.scales.y.ticks = {
                            ...chart.config.options.scales.y.ticks,
                            callback: (value) => this.formatAmount(value)
                        };
                    }
                    
                    chart.update();
                }
            });
        }
    }

    /**
     * Настройка обработчиков событий
     */
    setupEventListeners() {
        // Обработчик для динамически добавленных элементов
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.type === 'childList') {
                    mutation.addedNodes.forEach((node) => {
                        if (node.nodeType === 1) { // Element node
                            // Обновляем новые элементы с валютой
                            node.querySelectorAll('.currency-amount').forEach(element => {
                                const amount = element.getAttribute('data-amount') || element.textContent;
                                if (amount) {
                                    element.textContent = this.formatAmount(amount);
                                }
                            });
                        }
                    });
                }
            });
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }

    /**
     * Настройка fallback режима
     */
    setupFallback() {
        this.currencies = [
            { code: 'UAH', name: 'Украинская гривна', symbol: '₴', symbol_position: 'after', decimal_places: 2, decimal_separator: '.', thousands_separator: ',' },
            { code: 'USD', name: 'US Dollar', symbol: '$', symbol_position: 'before', decimal_places: 2, decimal_separator: '.', thousands_separator: ',' },
            { code: 'EUR', name: 'Euro', symbol: '€', symbol_position: 'before', decimal_places: 2, decimal_separator: '.', thousands_separator: ',' }
        ];
        this.currentCurrency = 'UAH';
        this.defaultCurrency = 'UAH';
        this.isLoaded = true;
    }



    /**
     * Принудительное обновление валют
     */
    async refresh() {
        this.isLoaded = false;
        this.loadingPromise = null;
        localStorage.removeItem('currencies');
        await this.loadCurrencies();
        this.updateAllCurrencyDisplays();
    }
}

// Создаем глобальный экземпляр
window.CurrencyManager = new CurrencyManager();

// Глобальные функции для совместимости
window.formatPrice = function(amount) {
    return window.CurrencyManager.formatAmount(amount);
};

window.formatCurrency = function(amount) {
    return window.CurrencyManager.formatAmount(amount);
};

// Функция для обновления валют при изменениях в админке
window.refreshCurrencies = function() {
    window.CurrencyManager.refresh();
}; 