<?php
/**
 * Тест аутентификации
 */

// Подключаем автозагрузчик Laravel
require_once 'vendor/autoload.php';

// Загружаем приложение
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Http\Request;

echo "=== Тест аутентификации ===\n\n";

// Создаем тестовый запрос
$request = Request::create('/products', 'GET');

// Обрабатываем запрос
$response = $app->handle($request);

echo "Статус ответа для /products: " . $response->getStatusCode() . "\n";

if ($response->getStatusCode() === 302) {
    echo "Перенаправление на: " . $response->headers->get('Location') . "\n";
    echo "Это означает, что пользователь не аутентифицирован\n";
} else {
    echo "Содержимое ответа:\n";
    echo substr($response->getContent(), 0, 500) . "...\n";
}

echo "\n=== Тест завершен ===\n"; 