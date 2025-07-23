<?php

require_once 'vendor/autoload.php';

// Загружаем Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== ТЕСТ СИСТЕМЫ ЯЗЫКОВ ===\n\n";

// 1. Проверяем языки в базе
echo "1. Языки в базе данных:\n";
$languages = \App\Models\Language::all();
foreach ($languages as $lang) {
    echo "- ID: {$lang->id}, Код: {$lang->code}, Название: {$lang->name} ({$lang->native_name}) - " . 
         ($lang->is_active ? 'активен' : 'неактивен') . 
         ($lang->is_default ? ', по умолчанию' : '') . "\n";
}

// 2. Проверяем проекты
echo "\n2. Проекты с языками:\n";
$projects = \App\Models\Admin\Project::with('language')->get();
foreach ($projects as $project) {
    $languageName = $project->language ? $project->language->name : 'не установлен';
    echo "- Проект {$project->id}: {$project->project_name} - язык ID: {$project->language_id}, название: {$languageName}\n";
}

// 3. Проверяем хелпер
echo "\n3. Тест LanguageHelper:\n";
$currentLang = \App\Helpers\LanguageHelper::getCurrentLanguage();
echo "Текущий язык: {$currentLang}\n";

$defaultLang = \App\Helpers\LanguageHelper::getDefaultLanguage();
echo "Язык по умолчанию: " . ($defaultLang ? $defaultLang->code : 'не найден') . "\n";

echo "\n=== ТЕСТ ЗАВЕРШЕН ===\n"; 