# 🎛️ Руководство по системным настройкам

## 📋 Обзор

Система настроек позволяет администраторам управлять основными параметрами сайта, включая:
- Название и описание сайта
- Email администратора
- Часовой пояс
- Логотип лендинга
- Фавикон

## 🚀 Установка

### 1. Миграция базы данных
```bash
php artisan migrate --path=database/migrations/2025_01_15_150000_create_system_settings_table.php
```

### 2. Заполнение начальными данными
```bash
php artisan db:seed --class=SystemSettingsSeeder
```

### 3. Обновление автозагрузчика (если нужно)
```bash
composer dump-autoload
```

## 📁 Структура файлов

```
app/
├── Models/SystemSetting.php          # Модель настроек
├── Http/Controllers/Admin/SettingsController.php  # Контроллер
└── Helpers/SystemHelper.php          # Хелпер для лендинга

resources/views/admin/settings/
├── index.blade.php                   # Страница настроек
└── test.blade.php                    # Тестовая страница

database/migrations/
└── 2025_01_15_150000_create_system_settings_table.php

database/seeders/
└── SystemSettingsSeeder.php
```

## 🎯 Использование

### В админ-панели
1. Перейдите в **Настройки** → **Системные настройки**
2. Заполните основные параметры
3. Загрузите логотип и фавикон
4. Нажмите **Сохранить**

### В лендинге
Система автоматически использует настройки:

```php
// Получить название сайта
$siteName = \App\Helpers\SystemHelper::getSiteName();

// Получить описание
$description = \App\Helpers\SystemHelper::getSiteDescription();

// Проверить наличие логотипа
if (\App\Helpers\SystemHelper::hasLandingLogo()) {
    $logo = \App\Helpers\SystemHelper::getLandingLogo();
}

// Проверить наличие фавикона
if (\App\Helpers\SystemHelper::hasFavicon()) {
    $favicon = \App\Helpers\SystemHelper::getFavicon();
}
```

## 🖼️ Требования к изображениям

### Логотип
- **Форматы:** JPEG, PNG, GIF, SVG
- **Размер:** 200x60px (рекомендуется)
- **Максимум:** 2MB
- **Фон:** Прозрачный (PNG/SVG)

### Фавикон
- **Форматы:** ICO, PNG, JPG
- **Размер:** 32x32px или 16x16px
- **Максимум:** 1MB
- **Форма:** Квадратная

## 🔧 Тестирование

Для проверки работы настроек:
1. Перейдите в **Настройки** → **Тест настроек**
2. Убедитесь, что все значения корректно отображаются
3. Проверьте работу хелпера

## 📝 Маршруты

```php
// Просмотр настроек
GET /panel/settings

// Обновление настроек
POST /panel/settings

// Тест настроек
GET /panel/settings/test
```

## 🎨 Кастомизация

### Добавление новых полей
1. Обновите миграцию
2. Добавьте поле в модель `SystemSetting`
3. Обновите контроллер
4. Добавьте поле в форму настроек
5. Создайте метод в `SystemHelper`

### Пример добавления поля
```php
// В миграции
$table->string('new_field')->nullable();

// В модели
protected $fillable = [..., 'new_field'];

// В хелпере
public static function getNewField()
{
    return SystemSetting::getValue('new_field', 'default_value');
}
```

## 🚨 Важные замечания

1. **Безопасность:** Все загружаемые файлы проходят валидацию
2. **Производительность:** Настройки кэшируются в базе данных
3. **Совместимость:** Система работает с Laravel 10+
4. **Резервное копирование:** Регулярно создавайте резервные копии

## 🆘 Поддержка

При возникновении проблем:
1. Проверьте логи Laravel
2. Убедитесь, что миграция выполнена
3. Проверьте права доступа к папке storage
4. Убедитесь, что символическая ссылка storage создана

---

**Версия:** 1.0  
**Дата:** 15.01.2025  
**Автор:** CRM Studio Team



