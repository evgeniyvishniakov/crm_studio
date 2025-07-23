<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Currency;
use App\Models\Admin\Project;

echo "=== ТЕСТ ВАЛЮТЫ ===\n";

// Проверяем валюты
echo "1. Все валюты:\n";
$currencies = Currency::all();
foreach ($currencies as $currency) {
    echo "   ID: {$currency->id}, Код: {$currency->code}, Символ: {$currency->symbol}, Активна: " . ($currency->is_active ? 'да' : 'нет') . "\n";
}

// Проверяем активные валюты
echo "\n2. Активные валюты:\n";
$activeCurrencies = Currency::where('is_active', true)->get();
foreach ($activeCurrencies as $currency) {
    echo "   ID: {$currency->id}, Код: {$currency->code}, Символ: {$currency->symbol}\n";
}

// Проверяем проект
echo "\n3. Проект:\n";
$project = Project::first();
if ($project) {
    echo "   ID проекта: {$project->id}\n";
    echo "   Валюта проекта: {$project->currency_id}\n";
    echo "   Язык проекта: {$project->language_id}\n";
    
    // Проверяем связанную валюту
    if ($project->currency_id) {
        $projectCurrency = Currency::find($project->currency_id);
        if ($projectCurrency) {
            echo "   Валюта проекта найдена: {$projectCurrency->code} ({$projectCurrency->symbol})\n";
        } else {
            echo "   Валюта проекта НЕ найдена!\n";
        }
    }
} else {
    echo "   Проект не найден!\n";
}

// Симулируем код из Blade
echo "\n4. Симуляция кода из Blade:\n";
$currencies = Currency::where('is_active', true)->get();
if ($currencies->isEmpty()) {
    echo "   Валюты не загружены, используем fallback\n";
    $currencies = collect([
        (object)['id' => 1, 'code' => 'UAH', 'symbol' => '₴'],
        (object)['id' => 2, 'code' => 'USD', 'symbol' => '$'],
        (object)['id' => 3, 'code' => 'EUR', 'symbol' => '€']
    ]);
}

$selectedCurrencyId = $project->currency_id ?? 1;
echo "   Выбранная валюта ID: {$selectedCurrencyId}\n";

echo "   Опции для select:\n";
foreach ($currencies as $currency) {
    $isSelected = $selectedCurrencyId == $currency->id;
    $selectedText = $isSelected ? ' (ВЫБРАНА)' : '';
    echo "     value=\"{$currency->id}\" {$currency->code} ({$currency->symbol}){$selectedText}\n";
} 