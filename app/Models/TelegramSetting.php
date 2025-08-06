<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TelegramSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'telegram_bot_token',
        'telegram_chat_id',
        'telegram_notifications_enabled',
    ];

    protected $casts = [
        'telegram_notifications_enabled' => 'boolean',
    ];

    /**
     * Связь с проектом
     */
    public function project()
    {
        return $this->belongsTo(\App\Models\Admin\Project::class);
    }

    /**
     * Получить или создать настройки telegram для проекта
     */
    public static function getOrCreateForProject($projectId)
    {
        return static::firstOrCreate(
            ['project_id' => $projectId],
            [
                'telegram_bot_token' => null,
                'telegram_chat_id' => null,
                'telegram_notifications_enabled' => false
            ]
        );
    }
}
