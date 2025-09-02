# Конфигурация доменов

## Переменные окружения для доменов

Добавьте следующие переменные в ваш `.env` файл:

### Для локальной разработки:
```env
# Основной URL приложения
APP_URL=http://localhost

# Домены для разных частей приложения
LANDING_DOMAIN=localhost
CRM_DOMAIN=localhost
PANEL_DOMAIN=localhost

# Полные URL с протоколом
LANDING_URL=http://localhost
CRM_URL=http://localhost
PANEL_URL=http://localhost
```

### Для продакшена (trimora.app):
```env
# Основной URL приложения
APP_URL=https://trimora.app

# Домены для разных частей приложения
LANDING_DOMAIN=trimora.app
CRM_DOMAIN=crm.trimora.app
PANEL_DOMAIN=panel.trimora.app

# Полные URL с протоколом
LANDING_URL=https://trimora.app
CRM_URL=https://crm.trimora.app
PANEL_URL=https://panel.trimora.app
```

## Использование в коде

### В PHP (контроллерах, моделях):
```php
use App\Helpers\DomainHelper;

// Получить URL лендинга
$landingUrl = DomainHelper::getLandingUrl();

// Получить URL CRM
$crmUrl = DomainHelper::getCrmUrl();

// Получить URL админ панели
$panelUrl = DomainHelper::getPanelUrl();

// Проверить текущий домен
if (DomainHelper::isCrmDomain()) {
    // Код для CRM
}

// Создать URL для конкретного домена
$url = DomainHelper::url('landing', 'pricing');
$url = DomainHelper::url('crm', 'dashboard');
$url = DomainHelper::url('panel', 'projects');

// Редирект на другой домен
return DomainHelper::redirectTo('landing', 'pricing');
```

### В Blade шаблонах:
```blade
{{-- Получить URL лендинга --}}
<a href="{{ DomainHelper::getLandingUrl() }}/pricing">Тарифы</a>

{{-- Получить URL CRM --}}
<a href="{{ DomainHelper::getCrmUrl() }}/dashboard">CRM</a>

{{-- Получить URL админ панели --}}
<a href="{{ DomainHelper::getPanelUrl() }}/projects">Проекты</a>

{{-- Создать URL для конкретного домена --}}
<a href="{{ DomainHelper::url('landing', 'pricing') }}">Тарифы</a>
```

### В JavaScript:
```javascript
// Получить URL лендинга
const landingUrl = '{{ DomainHelper::getLandingUrl() }}';

// Получить URL CRM
const crmUrl = '{{ DomainHelper::getCrmUrl() }}';

// Получить URL админ панели
const panelUrl = '{{ DomainHelper::getPanelUrl() }}';

// Использование в AJAX запросах
fetch(landingUrl + '/api/some-endpoint', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify(data)
});
```

## Преимущества

1. **Централизованная конфигурация** - все домены в одном месте
2. **Легкое переключение** между локальной разработкой и продакшеном
3. **Безопасность** - CORS настроен автоматически для ваших доменов
4. **Удобство** - простые методы для работы с доменами
5. **Гибкость** - легко добавить новые домены или изменить существующие

## Миграция существующего кода

Замените хардкод доменов на использование `DomainHelper`:

```php
// Было:
$url = 'https://trimora.app/pricing';

// Стало:
$url = DomainHelper::url('landing', 'pricing');
```

```blade
{{-- Было: --}}
<a href="https://trimora.app/pricing">Тарифы</a>

{{-- Стало: --}}
<a href="{{ DomainHelper::url('landing', 'pricing') }}">Тарифы</a>
```
