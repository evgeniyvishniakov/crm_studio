<?php
/**
 * Скрипт для обновления домена при переносе на сервер
 * Заменяет все вхождения локального домена на продакшн домен
 */

// Конфигурация
$oldDomain = 'http://127.0.0.1:8000';
$newDomain = 'https://your-domain.com'; // ЗАМЕНИТЕ НА ВАШ РЕАЛЬНЫЙ ДОМЕН

// Список файлов для обновления
$files = [
    'public/widget-loader.js',
    'public/simple-widget-demo.html',
    'public/fixed-widget-test.html',
    'public/test-widget.html',
    'CLIENT_WIDGET_GUIDE.md',
    'WIDGET_INSTRUCTIONS.md',
    'WIDGET_TESTING_GUIDE.md'
];

echo "🔄 Обновление домена с {$oldDomain} на {$newDomain}\n\n";

$totalReplaced = 0;

foreach ($files as $file) {
    if (!file_exists($file)) {
        echo "❌ Файл не найден: {$file}\n";
        continue;
    }
    
    $content = file_get_contents($file);
    $originalContent = $content;
    
    // Заменяем домен
    $content = str_replace($oldDomain, $newDomain, $content);
    
    // Считаем количество замен
    $replaced = substr_count($originalContent, $oldDomain);
    
    if ($replaced > 0) {
        file_put_contents($file, $content);
        echo "✅ {$file}: заменено {$replaced} вхождений\n";
        $totalReplaced += $replaced;
    } else {
        echo "ℹ️  {$file}: изменений не найдено\n";
    }
}

echo "\n🎉 Обновление завершено!\n";
echo "📊 Всего заменено: {$totalReplaced} вхождений\n";
echo "\n⚠️  ВАЖНО: Не забудьте обновить домен в настройках проекта в CRM!\n";
echo "   Перейдите в: Настройки проекта → Виджет → и проверьте все URL\n";
?>