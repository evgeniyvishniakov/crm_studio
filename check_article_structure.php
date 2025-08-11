<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "Структура таблицы knowledge_articles:\n";
$columns = Schema::getColumnListing('knowledge_articles');
foreach ($columns as $column) {
    $columnInfo = DB::select("SHOW COLUMNS FROM knowledge_articles WHERE Field = ?", [$column])[0];
    echo $column . " - " . $columnInfo->Type . " - " . ($columnInfo->Null === 'NO' ? 'NOT NULL' : 'NULL') . " - Default: " . ($columnInfo->Default ?? 'NULL') . "\n";
}
