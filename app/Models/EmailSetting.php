<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'email_host',
        'email_port',
        'email_username',
        'email_password',
        'email_encryption',
        'email_from_name',
        'email_notifications_enabled',
    ];

    protected $casts = [
        'email_port' => 'integer',
        'email_notifications_enabled' => 'boolean',
    ];

    /**
     * Связь с проектом
     */
    public function project()
    {
        return $this->belongsTo(\App\Models\Admin\Project::class);
    }

    /**
     * Получить или создать настройки email для проекта
     */
    public static function getOrCreateForProject($projectId)
    {
        return static::firstOrCreate(
            ['project_id' => $projectId],
            [
                'email_host' => null,
                'email_port' => 587,
                'email_username' => null,
                'email_password' => null,
                'email_encryption' => 'tls',
                'email_from_name' => null,
                'email_notifications_enabled' => false
            ]
        );
    }
}
