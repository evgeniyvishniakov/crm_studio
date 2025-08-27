<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\KnowledgeArticle;

echo "Проверка полезных советов в статьях базы знаний:\n\n";

$articles = KnowledgeArticle::with('tips')->get();

foreach ($articles as $article) {
    echo "ID: {$article->id}\n";
    echo "Заголовок: {$article->title}\n";
    echo "Количество советов: {$article->tips->count()}\n";
    
    if ($article->tips->count() > 0) {
        echo "Советы:\n";
        foreach ($article->tips as $tip) {
            echo "  - {$tip->content}\n";
        }
    }
    
    echo "---\n";
}
