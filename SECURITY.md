# 🔒 Безопасность системы тикетов и уведомлений

## Rate Limiting (Ограничение запросов)

### Настройки по умолчанию:
- **Тикеты**: 10 созданий в минуту
- **Сообщения**: 30 отправок в минуту  
- **Уведомления**: 60 отметок "прочитано" в минуту

### Настройка через .env:
```env
RATE_LIMIT_TICKETS=10
RATE_LIMIT_TICKETS_DECAY=1
RATE_LIMIT_MESSAGES=30
RATE_LIMIT_MESSAGES_DECAY=1
RATE_LIMIT_NOTIFICATIONS=60
RATE_LIMIT_NOTIFICATIONS_DECAY=1
```

## Логирование безопасности

### Что логируется:
- Создание тикетов (user_id, ticket_id, subject, IP)
- Отправка сообщений (user_id, ticket_id, message_id, IP)
- Смена статуса тикетов (admin_id, ticket_id, old_status, new_status, IP)
- Удаление тикетов (admin_id, ticket_id, subject, IP)
- Очистка уведомлений (deleted_count, cutoff_date)

### Настройка логирования:
```env
SECURITY_LOGGING_ENABLED=true
SECURITY_LOG_LEVEL=info
SECURITY_LOG_CHANNEL=daily
```

## Права доступа

### Клиенты:
- Видят только свои тикеты
- Видят только уведомления по своим проектам
- Не могут изменять статус тикетов

### Администраторы:
- Видят все тикеты
- Видят все уведомления
- Могут изменять статус тикетов
- Могут удалять тикеты

## Middleware защита

### Используемые middleware:
- `auth:client` - аутентификация клиентов
- `admin.only` - доступ только для админов
- `rate.limit` - ограничение запросов
- `VerifyCsrfToken` - защита от CSRF

## Очистка данных

### Автоматическая очистка уведомлений:
```bash
# Ручной запуск
php artisan notifications:cleanup --days=30

# Автоматически каждый день в 2:00
```

### Настройка очистки:
```env
NOTIFICATIONS_CLEANUP_DAYS=30
MAX_NOTIFICATIONS_PER_USER=1000
```

## Ограничения контента

### Тикеты:
```env
MAX_TICKET_SUBJECT_LENGTH=255
MAX_TICKET_MESSAGE_LENGTH=5000
MAX_TICKETS_PER_USER=100
```

## Мониторинг безопасности

### Проверка логов:
```bash
# Просмотр логов безопасности
tail -f storage/logs/laravel.log | grep "Support ticket"

# Просмотр логов очистки
tail -f storage/logs/notifications-cleanup.log
```

### Проверка rate limiting:
```bash
# Проверка текущих лимитов
php artisan tinker
>>> RateLimiter::remaining('user:1:tickets', 10)
```

## Рекомендации по безопасности

1. **Регулярно обновляйте Laravel** до последней версии
2. **Используйте HTTPS** в продакшене
3. **Мониторьте логи** на подозрительную активность
4. **Настройте бэкапы** базы данных
5. **Используйте сильные пароли** для админов
6. **Ограничьте доступ к логам** только администраторам

## Аварийные процедуры

### При подозрении на взлом:
1. Проверьте логи на подозрительную активность
2. Временно увеличьте rate limiting
3. Проверьте права доступа пользователей
4. При необходимости заблокируйте подозрительные IP

### Команды для диагностики:
```bash
# Проверка последних действий
php artisan tinker
>>> \App\Models\Clients\SupportTicket::latest()->take(10)->get()

# Проверка rate limiting
>>> RateLimiter::clear('user:1:tickets')
``` 