<?php
/**
 * Тест маршрута удаленных товаров
 */

// Подключаем автозагрузчик Laravel
require_once 'vendor/autoload.php';

// Загружаем приложение
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Http\Request;
use App\Models\Admin\User;

echo "=== Тест маршрута удаленных товаров ===\n\n";

try {
    // Находим первого пользователя для тестирования
    $user = User::first();
    
    if (!$user) {
        echo "Ошибка: Нет пользователей в базе данных\n";
        exit;
    }
    
    echo "Используем пользователя: ID={$user->id}, Email={$user->email}, Project ID={$user->project_id}\n\n";
    
    // Аутентифицируем пользователя
    auth()->guard('client')->login($user);
    
    // Тестируем разные варианты URL
    $urls = [
        '/products/trashed',
        '/panel/products/trashed',
        '/beautyflow/products/trashed'
    ];
    
    foreach ($urls as $url) {
        echo "Тестируем URL: {$url}\n";
        
        // Создаем тестовый запрос
        $request = Request::create($url, 'GET');
        
        // Обрабатываем запрос
        $response = $app->handle($request);
        
        echo "Статус ответа: " . $response->getStatusCode() . "\n";
        
        if ($response->getStatusCode() === 200) {
            echo "✅ Успех! URL работает: {$url}\n";
            echo "Содержимое ответа:\n";
            echo $response->getContent() . "\n";
            break;
        } else {
            echo "❌ Не работает: {$url}\n\n";
        }
    }
    
} catch (Exception $e) {
    echo "Ошибка: " . $e->getMessage() . "\n";
    echo "Файл: " . $e->getFile() . "\n";
    echo "Строка: " . $e->getLine() . "\n";
    echo "Стек вызовов:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== Тест завершен ===\n"; 