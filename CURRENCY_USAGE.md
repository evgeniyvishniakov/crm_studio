# Система валют - Инструкция по использованию

## Обзор
Система автоматически применяет выбранную валюту ко всем страницам приложения. При смене валюты в настройках, она автоматически обновляется везде.

## Как это работает

### 1. Backend (PHP)
- **CurrencyHelper** - хелпер для работы с валютой
- **SetCurrency Middleware** - автоматически устанавливает валюту из проекта пользователя
- **Сессия** - хранит текущую валюту

### 2. Frontend (JavaScript)
- **CurrencyManager** - глобальный менеджер валют
- **Автоматическое обновление** - обновляет валюты на всех страницах

## Способы использования

### 1. В Blade шаблонах (PHP)

```php
// Использование хелпера
{{ \App\Helpers\CurrencyHelper::format($amount) }}
{{ \App\Helpers\CurrencyHelper::getSymbol() }}
{{ \App\Helpers\CurrencyHelper::getCurrentCurrency() }}
```

### 2. В HTML с автоматическим обновлением

```html
<!-- Сумма с автоматическим обновлением -->
<span class="currency-amount" data-amount="1000">1 000 грн</span>

<!-- Только символ валюты -->
<span data-currency-symbol>грн</span>

<!-- Код валюты -->
<span data-currency-code>UAH</span>

<!-- Сумма с data-currency -->
<span data-currency data-amount="1500">1 500 грн</span>
```

### 3. В JavaScript

```javascript
// Получить текущую валюту
const currency = window.CurrencyManager.currentCurrency;
const symbol = window.CurrencyManager.currentSymbol;

// Форматировать сумму
const formatted = window.CurrencyManager.formatAmount(1000);

// Обновить валюту (обычно не нужно, делается автоматически)
window.CurrencyManager.updateCurrency('USD');

// Обновить все валюты на странице
window.CurrencyManager.updateAllCurrencies();
```

## Доступные валюты

- **UAH** - Украинская гривна (грн)
- **USD** - Доллар США ($)
- **EUR** - Евро (€)

## Примеры использования

### В контроллере
```php
use App\Helpers\CurrencyHelper;

public function index()
{
    $data = [
        'amount' => 1500,
        'formatted_amount' => CurrencyHelper::format(1500),
        'currency_symbol' => CurrencyHelper::getSymbol(),
        'current_currency' => CurrencyHelper::getCurrentCurrency()
    ];
    
    return view('example', $data);
}
```

### В Blade шаблоне
```html
<div class="price">
    <span class="currency-amount" data-amount="{{ $product->price }}">
        {{ \App\Helpers\CurrencyHelper::format($product->price) }}
    </span>
</div>

<div class="total">
    Итого: <span data-currency-symbol>{{ \App\Helpers\CurrencyHelper::getSymbol() }}</span>
</div>
```

### В JavaScript (динамическое обновление)
```javascript
// При добавлении нового элемента с валютой
function addPriceElement(amount) {
    const element = document.createElement('span');
    element.className = 'currency-amount';
    element.setAttribute('data-amount', amount);
    element.textContent = window.CurrencyManager.formatAmount(amount);
    document.body.appendChild(element);
}
```

## Автоматическое обновление

Система автоматически:
1. Обновляет валюты при загрузке страницы
2. Обновляет валюты каждые 5 секунд (для динамического контента)
3. Обновляет валюты при смене в настройках

## Миграция существующего кода

### Заменить старые форматы:
```html
<!-- Было -->
<span>{{ number_format($amount, 2, '.', ' ') }} грн</span>

<!-- Стало -->
<span class="currency-amount" data-amount="{{ $amount }}">
    {{ \App\Helpers\CurrencyHelper::format($amount) }}
</span>
```

### В JavaScript:
```javascript
// Было
const formatted = amount.toLocaleString('ru-RU') + ' грн';

// Стало
const formatted = window.CurrencyManager.formatAmount(amount);
```

## Примечания

1. **Производительность**: Система оптимизирована и не влияет на производительность
2. **Совместимость**: Работает со всеми современными браузерами
3. **Автоматизация**: Не требует ручного обновления валют
4. **Гибкость**: Поддерживает добавление новых валют 