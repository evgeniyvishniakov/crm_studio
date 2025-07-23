<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Admin\User;
use App\Models\Admin\Project;
use App\Models\Language;
use App\Models\Currency;

echo "=== ТЕСТ ПРОЕКТА ПОЛЬЗОВАТЕЛЯ ===\n";

// Проверяем всех пользователей
echo "1. Все пользователи:\n";
$users = User::all();
foreach ($users as $user) {
    echo "   ID: {$user->id}, Email: {$user->email}, Project ID: " . ($user->project_id ?? 'null') . "\n";
}

// Проверяем все проекты
echo "\n2. Все проекты:\n";
$projects = Project::all();
foreach ($projects as $project) {
    echo "   ID: {$project->id}, Name: {$project->name}, Language ID: " . ($project->language_id ?? 'null') . ", Currency ID: " . ($project->currency_id ?? 'null') . "\n";
}

// Проверяем первого пользователя (предполагаем, что он залогинен)
$user = User::first();
if ($user) {
    echo "\n3. Первый пользователь:\n";
    echo "   ID: {$user->id}\n";
    echo "   Email: {$user->email}\n";
    echo "   Project ID: " . ($user->project_id ?? 'null') . "\n";
    
    if ($user->project_id) {
        $project = Project::find($user->project_id);
        if ($project) {
            echo "   Project Name: {$project->name}\n";
            echo "   Project Language ID: " . ($project->language_id ?? 'null') . "\n";
            echo "   Project Currency ID: " . ($project->currency_id ?? 'null') . "\n";
            
            // Проверяем связанные данные
            if ($project->language_id) {
                $language = Language::find($project->language_id);
                if ($language) {
                    echo "   Language: {$language->name} ({$language->code})\n";
                }
            }
            
            if ($project->currency_id) {
                $currency = Currency::find($project->currency_id);
                if ($currency) {
                    echo "   Currency: {$currency->code} ({$currency->symbol})\n";
                }
            }
        } else {
            echo "   Project: НЕ НАЙДЕН!\n";
        }
    } else {
        echo "   Project ID: НЕ УСТАНОВЛЕН!\n";
    }
} else {
    echo "Пользователи не найдены!\n";
} 