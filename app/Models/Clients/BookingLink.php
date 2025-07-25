<?php

declare(strict_types=1);

namespace App\Models\Clients;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BookingLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'project_id',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Связь с проектом
     */
    public function project()
    {
        return $this->belongsTo(\App\Models\Admin\Project::class);
    }

    /**
     * Связь с услугами (многие-ко-многим)
     */
    public function services()
    {
        return $this->belongsToMany(Service::class, 'booking_link_services');
    }

    /**
     * Связь с мастерами (многие-ко-многим)
     */
    public function users()
    {
        return $this->belongsToMany(\App\Models\Admin\User::class, 'booking_link_users');
    }

    /**
     * Получить публичный URL
     */
    public function getPublicUrlAttribute()
    {
        return url('/booking/' . $this->slug);
    }

    /**
     * Автоматически генерировать slug при создании
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($bookingLink) {
            if (empty($bookingLink->slug)) {
                $bookingLink->slug = Str::slug($bookingLink->title);
            }
        });
    }

    /**
     * Получить активные ссылки для проекта
     */
    public static function getActiveForProject($projectId)
    {
        return static::where('project_id', $projectId)
            ->where('is_active', true)
            ->with(['services', 'users'])
            ->get();
    }
}
