<?php
/**
 * Обновленные ключи для русского языка
 * Показывают только русские примеры названий колонок
 */

// Заменить эти строки в resources/lang/ru/messages.php:

// Старые ключи (показывают все языки):
// 'name_column_examples' => 'Название: название, name, title, назва, nazvanie',
// 'category_column_examples' => 'Категория: категория, category, категорія, kategoriia',
// 'brand_column_examples' => 'Бренд: бренд, brand, марка, brend',
// 'purchase_price_examples' => 'Оптовая цена: оптовая цена, purchase_price, оптова ціна, optovaia_cena',
// 'retail_price_examples' => 'Розничная цена: розничная цена, retail_price, рознична ціна, roznicnaia_cena',
// 'photo_column_examples' => 'Фото: фото, photo, зображення, foto',

// Новые ключи (показывают только русские примеры):
$new_keys = [
    'name_column_examples' => 'Название: название, названіе, названиє',
    'category_column_examples' => 'Категория: категория, категорія, категорија',
    'brand_column_examples' => 'Бренд: бренд, брэнд, марка',
    'purchase_price_examples' => 'Оптовая цена: оптовая цена, оптовая стоимость, опт',
    'retail_price_examples' => 'Розничная цена: розничная цена, розничная стоимость, розница',
    'photo_column_examples' => 'Фото: фото, изображение, картинка, фотография',
];

echo "=== ОБНОВЛЕННЫЕ КЛЮЧИ ДЛЯ РУССКОГО ЯЗЫКА ===\n\n";

foreach ($new_keys as $key => $value) {
    echo "    '$key' => '$value',\n";
}

echo "\n=== ИНСТРУКЦИЯ ===\n";
echo "1. Откройте файл resources/lang/ru/messages.php\n";
echo "2. Найдите строки с ключами name_column_examples, category_column_examples и т.д.\n";
echo "3. Замените их на новые значения выше\n";
echo "4. Сохраните файл\n";
echo "\nЭто покажет пользователям только русские примеры названий колонок!\n"; 