// Глобальная система валют
window.CurrencyManager = {
    // Текущая валюта
    currentCurrency: 'UAH',
    currentSymbol: 'грн',
    
    // Инициализация
    init: function(currencyData) {
        this.currentCurrency = currencyData.current || 'UAH';
        this.currentSymbol = currencyData.symbol || 'грн';
        this.updateAllCurrencies();
    },
    
    // Обновить валюту
    updateCurrency: function(currency) {
        this.currentCurrency = currency;
        this.currentSymbol = this.getSymbol(currency);
        this.updateAllCurrencies();
    },
    
    // Получить символ валюты
    getSymbol: function(currency) {
        const symbols = {
            'UAH': 'грн',
            'USD': '$',
            'EUR': '€'
        };
        return symbols[currency] || 'грн';
    },
    
    // Форматировать сумму
    formatAmount: function(amount) {
        const num = parseFloat(amount);
        if (isNaN(num)) return amount;
        
        const formatted = num.toLocaleString('ru-RU');
        return formatted + ' ' + this.currentSymbol;
    },
    
    // Обновить все валюты на странице
    updateAllCurrencies: function() {
        // Обновляем элементы с data-currency
        document.querySelectorAll('[data-currency]').forEach(function(element) {
            const amount = element.getAttribute('data-amount');
            if (amount) {
                element.textContent = window.CurrencyManager.formatAmount(amount);
            }
        });
        
        // Обновляем элементы с data-currency-symbol
        document.querySelectorAll('[data-currency-symbol]').forEach(function(element) {
            element.textContent = window.CurrencyManager.currentSymbol;
        });
        
        // Обновляем элементы с data-currency-code
        document.querySelectorAll('[data-currency-code]').forEach(function(element) {
            element.textContent = window.CurrencyManager.currentCurrency;
        });
        
        // Обновляем элементы с классом currency-amount
        document.querySelectorAll('.currency-amount').forEach(function(element) {
            const amount = element.getAttribute('data-amount') || element.textContent.replace(/[^\d.,]/g, '');
            if (amount) {
                element.textContent = window.CurrencyManager.formatAmount(amount);
            }
        });
    }
};

// Автоматическое обновление валют при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    // Если данные валюты переданы через window.currencyData
    if (window.currencyData) {
        window.CurrencyManager.init(window.currencyData);
    }
    
    // Обновляем валюты каждые 5 секунд (на случай динамического контента)
    setInterval(function() {
        window.CurrencyManager.updateAllCurrencies();
    }, 5000);
}); 