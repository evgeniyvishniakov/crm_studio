<?php
/**
 * Тест работы SoftDeletes для товаров
 * 
 * Этот файл демонстрирует как работает мягкое удаление товаров
 */

// Подключаем автозагрузчик Laravel
require_once 'vendor/autoload.php';

// Загружаем приложение
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Clients\Product;

echo "=== Тест работы SoftDeletes для товаров ===\n\n";

// Проверяем есть ли удаленные товары
echo "1. Проверяем есть ли удаленные товары в базе...\n";
$deletedProducts = Product::withTrashed()->whereNotNull('deleted_at')->get();
echo "Найдено удаленных товаров: " . $deletedProducts->count() . "\n";

if ($deletedProducts->count() > 0) {
    echo "Удаленные товары:\n";
    foreach ($deletedProducts as $product) {
        echo "- ID: {$product->id}, Название: {$product->name}, Удален: {$product->deleted_at}\n";
    }
} else {
    echo "Удаленных товаров нет.\n";
}

echo "\n";

// Проверяем активные товары
echo "2. Проверяем активные товары...\n";
$activeProducts = Product::all();
echo "Найдено активных товаров: " . $activeProducts->count() . "\n";

if ($activeProducts->count() > 0) {
    echo "Активные товары:\n";
    foreach ($activeProducts->take(5) as $product) {
        echo "- ID: {$product->id}, Название: {$product->name}\n";
    }
    if ($activeProducts->count() > 5) {
        echo "... и еще " . ($activeProducts->count() - 5) . " товаров\n";
    }
}

echo "\n";

// Проверяем структуру таблицы
echo "3. Проверяем структуру таблицы products...\n";
$columns = \Illuminate\Support\Facades\Schema::getColumnListing('products');
echo "Колонки в таблице products:\n";
foreach ($columns as $column) {
    echo "- {$column}\n";
}

echo "\n";

// Проверяем есть ли поле deleted_at
if (in_array('deleted_at', $columns)) {
    echo "✅ Поле deleted_at найдено в таблице products\n";
} else {
    echo "❌ Поле deleted_at НЕ найдено в таблице products\n";
}

echo "\n=== Тест завершен ===\n"; 