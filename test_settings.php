<?php

require_once 'vendor/autoload.php';

// Загружаем Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== ТЕСТ СТРАНИЦЫ НАСТРОЕК ===\n\n";

// 1. Проверяем пользователей
echo "1. Пользователи:\n";
$users = \App\Models\Admin\User::all();
foreach ($users as $user) {
    echo "- ID: {$user->id}, Email: {$user->email}, Project ID: {$user->project_id}\n";
}

// 2. Проверяем проекты
echo "\n2. Проекты:\n";
$projects = \App\Models\Admin\Project::with(['currency', 'language'])->get();
foreach ($projects as $project) {
    $currencyName = $project->currency ? $project->currency->code : 'не установлена';
    $languageName = $project->language ? $project->language->name : 'не установлен';
    echo "- ID: {$project->id}, Название: {$project->project_name}, Валюта: {$currencyName}, Язык: {$languageName}\n";
}

// 3. Проверяем языки
echo "\n3. Языки:\n";
$languages = \App\Models\Language::getActive();
foreach ($languages as $language) {
    echo "- ID: {$language->id}, Код: {$language->code}, Название: {$language->name}\n";
}

// 4. Симулируем загрузку страницы настроек
echo "\n4. Симуляция загрузки страницы настроек:\n";
$user = \App\Models\Admin\User::first();
if ($user) {
    echo "Пользователь: {$user->email}\n";
    $project = \App\Models\Admin\Project::with(['currency', 'language'])->where('id', $user->project_id ?? null)->first();
    if ($project) {
        echo "Проект: {$project->project_name}\n";
        echo "Язык ID: " . ($project->language_id ?? 'null') . "\n";
        echo "Язык название: " . ($project->language ? $project->language->name : 'null') . "\n";
        
        // Проверяем метод getActive
        $activeLanguages = \App\Models\Language::getActive();
        echo "Активных языков: " . $activeLanguages->count() . "\n";
        foreach ($activeLanguages as $lang) {
            echo "  - {$lang->id}: {$lang->name}\n";
        }
    } else {
        echo "Проект не найден\n";
    }
} else {
    echo "Пользователи не найдены\n";
}

echo "\n=== ТЕСТ ЗАВЕРШЕН ===\n"; 