<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Admin\User;
use App\Models\Admin\Project;

// Проверяем проект с ID 45
$project = Project::find(45);

if (!$project) {
    echo "Проект с ID 45 не найден\n";
    exit;
}

echo "Проект: {$project->name}\n";
echo "Всего пользователей в проекте: " . User::where('project_id', 45)->count() . "\n\n";

$users = User::where('project_id', 45)->get();

foreach ($users as $user) {
    echo "ID: {$user->id}, Имя: {$user->name}, Роль: {$user->role}, Email: {$user->email}\n";
}

echo "\nАдминистраторы:\n";
$admins = User::where('project_id', 45)->where('role', 'admin')->get();

foreach ($admins as $admin) {
    echo "ID: {$admin->id}, Имя: {$admin->name}, Email: {$admin->email}\n";
} 