-- Добавляем поле trial_ends_at в существующую таблицу subscriptions
ALTER TABLE `subscriptions` 
ADD COLUMN `trial_ends_at` timestamp NULL DEFAULT NULL AFTER `starts_at`;

-- Добавляем индекс для оптимизации запросов по пробному периоду
ALTER TABLE `subscriptions` 
ADD INDEX `subscriptions_trial_ends_at_index` (`trial_ends_at`);
