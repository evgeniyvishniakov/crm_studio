-- SQL запросы для добавления каскадного удаления на сервер
-- Выполните эти запросы на сервере после переноса кода

-- 1. Добавляем каскадное удаление для employee_time_offs
ALTER TABLE `employee_time_offs` 
ADD CONSTRAINT `fk_employee_time_offs_project_id` 
FOREIGN KEY (`project_id`) REFERENCES `projects`(`id`) ON DELETE CASCADE;

-- 2. Добавляем каскадное удаление для clients
ALTER TABLE `clients` 
ADD CONSTRAINT `fk_clients_project_id` 
FOREIGN KEY (`project_id`) REFERENCES `projects`(`id`) ON DELETE CASCADE;

-- 3. Исправляем каскадное удаление для purchase_items
-- Сначала удаляем старый внешний ключ
ALTER TABLE `purchase_items` DROP FOREIGN KEY `purchase_items_product_id_foreign`;

-- Добавляем новый с каскадным удалением
ALTER TABLE `purchase_items` 
ADD CONSTRAINT `purchase_items_product_id_foreign` 
FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE;

-- 4. Проверяем что все внешние ключи настроены правильно
SELECT 
    kcu.TABLE_NAME as 'Таблица',
    kcu.COLUMN_NAME as 'Поле',
    CASE 
        WHEN kcu.CONSTRAINT_NAME IS NULL THEN '❌ НЕТ ВНЕШНЕГО КЛЮЧА'
        ELSE '✅ ЕСТЬ ВНЕШНИЙ КЛЮЧ'
    END as 'Статус',
    kcu.CONSTRAINT_NAME as 'Имя ограничения',
    kcu.REFERENCED_TABLE_NAME as 'Ссылается на таблицу',
    kcu.REFERENCED_COLUMN_NAME as 'Ссылается на поле',
    rc.DELETE_RULE as 'Правило удаления'
FROM information_schema.KEY_COLUMN_USAGE kcu
LEFT JOIN information_schema.REFERENTIAL_CONSTRAINTS rc 
    ON kcu.CONSTRAINT_NAME = rc.CONSTRAINT_NAME 
    AND kcu.TABLE_SCHEMA = rc.CONSTRAINT_SCHEMA
WHERE kcu.TABLE_SCHEMA = DATABASE() 
AND kcu.COLUMN_NAME = 'project_id'
ORDER BY kcu.TABLE_NAME;
