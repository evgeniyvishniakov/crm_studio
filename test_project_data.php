<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Admin\Project;
use App\Models\Language;
use App\Models\Currency;

echo "=== ТЕСТ ДАННЫХ ПРОЕКТА ===\n";

// Проверяем проект
$project = Project::first();
if ($project) {
    echo "1. Проект:\n";
    echo "   ID: {$project->id}\n";
    echo "   Language ID: " . ($project->language_id ?? 'null') . "\n";
    echo "   Currency ID: " . ($project->currency_id ?? 'null') . "\n";
    
    // Проверяем связанные данные
    if ($project->language_id) {
        $language = Language::find($project->language_id);
        if ($language) {
            echo "   Language: {$language->name} ({$language->code})\n";
        } else {
            echo "   Language: НЕ НАЙДЕН!\n";
        }
    }
    
    if ($project->currency_id) {
        $currency = Currency::find($project->currency_id);
        if ($currency) {
            echo "   Currency: {$currency->code} ({$currency->symbol})\n";
        } else {
            echo "   Currency: НЕ НАЙДЕН!\n";
        }
    }
} else {
    echo "Проект не найден!\n";
}

// Проверяем языки
echo "\n2. Активные языки:\n";
$languages = Language::where('is_active', true)->get();
foreach ($languages as $language) {
    $isSelected = $project && $project->language_id == $language->id;
    $selectedText = $isSelected ? ' (ВЫБРАН)' : '';
    echo "   ID: {$language->id}, Код: {$language->code}, Название: {$language->name}{$selectedText}\n";
}

// Проверяем валюты
echo "\n3. Активные валюты:\n";
$currencies = Currency::where('is_active', true)->get();
foreach ($currencies as $currency) {
    $isSelected = $project && $project->currency_id == $currency->id;
    $selectedText = $isSelected ? ' (ВЫБРАНА)' : '';
    echo "   ID: {$currency->id}, Код: {$currency->code}, Символ: {$currency->symbol}{$selectedText}\n";
}

// Симулируем код из Blade для селектора языков
echo "\n4. Симуляция селектора языков:\n";
$selectedLanguageId = $project->language_id ?? 1;
echo "   Выбранная валюта ID: {$selectedLanguageId}\n";

echo "   Опции для select:\n";
foreach ($languages as $language) {
    $isSelected = $selectedLanguageId == $language->id;
    $selectedText = $isSelected ? ' (ВЫБРАНА)' : '';
    echo "     value=\"{$language->id}\" {$language->name} ({$language->native_name}){$selectedText}\n";
} 