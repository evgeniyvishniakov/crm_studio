<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\KnowledgeArticle;

echo "Тест загрузки советов:\n\n";

// Тест 1: Загрузка без связей
echo "1. Загрузка без связей:\n";
$article1 = KnowledgeArticle::find(18);
echo "ID: {$article1->id}, Заголовок: {$article1->title}\n";
echo "Советы загружены: " . ($article1->relationLoaded('tips') ? 'Да' : 'Нет') . "\n";
echo "Количество советов: " . $article1->tips->count() . "\n\n";

// Тест 2: Загрузка с связями
echo "2. Загрузка с связями:\n";
$article2 = KnowledgeArticle::with(['steps', 'tips'])->find(18);
echo "ID: {$article2->id}, Заголовок: {$article2->title}\n";
echo "Советы загружены: " . ($article2->relationLoaded('tips') ? 'Да' : 'Нет') . "\n";
echo "Количество советов: " . $article2->tips->count() . "\n";

if ($article2->tips->count() > 0) {
    echo "Первый совет: {$article2->tips->first()->content}\n";
}
