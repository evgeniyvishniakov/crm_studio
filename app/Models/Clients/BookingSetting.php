<?php

declare(strict_types=1);

namespace App\Models\Clients;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'booking_interval',
        'working_hours_start',
        'working_hours_end',
        'advance_booking_days',
        'allow_same_day_booking',
        'require_confirmation',
        'booking_instructions'
    ];

    protected $casts = [
        'booking_interval' => 'integer',
        'advance_booking_days' => 'integer',
        'allow_same_day_booking' => 'boolean',
        'require_confirmation' => 'boolean',
        'working_hours_start' => 'datetime',
        'working_hours_end' => 'datetime'
    ];

    /**
     * Связь с проектом
     */
    public function project()
    {
        return $this->belongsTo(\App\Models\Admin\Project::class);
    }

    /**
     * Получить или создать настройки для проекта
     */
    public static function getOrCreateForProject($projectId)
    {
        return static::firstOrCreate(
            ['project_id' => $projectId],
            [
                'booking_interval' => 30,
                'working_hours_start' => '09:00:00',
                'working_hours_end' => '18:00:00',
                'advance_booking_days' => 30,
                'allow_same_day_booking' => true,
                'require_confirmation' => false
            ]
        );
    }

    /**
     * Получить интервал записи в секундах
     */
    public function getIntervalInSeconds()
    {
        return $this->booking_interval * 60;
    }

    /**
     * Проверить, можно ли записаться на указанную дату
     */
    public function canBookOnDate($date)
    {
        $bookingDate = \Carbon\Carbon::parse($date);
        $today = \Carbon\Carbon::today();
        
        // Если запись в тот же день не разрешена
        if (!$this->allow_same_day_booking && $bookingDate->isSameDay($today)) {
            return false;
        }
        
        // Проверка на максимальное количество дней вперед
        $maxDate = $today->copy()->addDays($this->advance_booking_days);
        
        return $bookingDate->between($today, $maxDate);
    }

    /**
     * Получить время начала работы в формате H:i
     */
    public function getWorkingHoursStartFormattedAttribute()
    {
        if (!$this->working_hours_start) {
            return '09:00';
        }
        
        if ($this->working_hours_start instanceof \Carbon\Carbon) {
            return $this->working_hours_start->format('H:i');
        }
        
        // Если это строка, попробуем распарсить
        try {
            return \Carbon\Carbon::parse($this->working_hours_start)->format('H:i');
        } catch (\Exception $e) {
            return '09:00';
        }
    }

    /**
     * Получить время окончания работы в формате H:i
     */
    public function getWorkingHoursEndFormattedAttribute()
    {
        if (!$this->working_hours_end) {
            return '18:00';
        }
        
        if ($this->working_hours_end instanceof \Carbon\Carbon) {
            return $this->working_hours_end->format('H:i');
        }
        
        // Если это строка, попробуем распарсить
        try {
            return \Carbon\Carbon::parse($this->working_hours_end)->format('H:i');
        } catch (\Exception $e) {
            return '18:00';
        }
    }
}
