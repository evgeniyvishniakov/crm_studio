<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Notification;
use App\Models\Admin\User;

echo "Проверка уведомлений:\n\n";

// Проверяем все уведомления
$notifications = Notification::all();

echo "Всего уведомлений: " . $notifications->count() . "\n\n";

foreach ($notifications as $notification) {
    echo "ID: {$notification->id}\n";
    echo "User ID: {$notification->user_id}\n";
    echo "Type: {$notification->type}\n";
    echo "Title: {$notification->title}\n";
    echo "Body: {$notification->body}\n";
    echo "URL: {$notification->url}\n";
    echo "Is Read: " . ($notification->is_read ? 'Yes' : 'No') . "\n";
    echo "Project ID: {$notification->project_id}\n";
    echo "Created: {$notification->created_at}\n";
    echo "---\n";
}

// Проверяем уведомления для администратора (ID 52)
echo "\nУведомления для администратора (ID 52):\n";
$adminNotifications = Notification::where('user_id', 52)->get();

echo "Количество: " . $adminNotifications->count() . "\n\n";

foreach ($adminNotifications as $notification) {
    echo "ID: {$notification->id}\n";
    echo "Type: {$notification->type}\n";
    echo "Title: {$notification->title}\n";
    echo "Body: {$notification->body}\n";
    echo "Is Read: " . ($notification->is_read ? 'Yes' : 'No') . "\n";
    echo "---\n";
} 