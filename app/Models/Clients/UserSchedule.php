<?php

declare(strict_types=1);

namespace App\Models\Clients;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'day_of_week',
        'start_time',
        'end_time',
        'is_working',
        'notes',
        'booking_interval'
    ];

    protected $casts = [
        'day_of_week' => 'integer',
        'is_working' => 'boolean',
        'start_time' => 'datetime',
        'end_time' => 'datetime'
    ];

    /**
     * Связь с пользователем (мастером)
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\Admin\User::class);
    }

    /**
     * Получить расписание для пользователя
     */
    public static function getForUser($userId)
    {
        return static::where('user_id', $userId)
            ->orderBy('day_of_week')
            ->get();
    }

    /**
     * Получить расписание для пользователя на конкретный день
     */
    public static function getForUserAndDay($userId, $dayOfWeek)
    {
        return static::where('user_id', $userId)
            ->where('day_of_week', $dayOfWeek)
            ->first();
    }

    /**
     * Получить название дня недели
     */
    public function getDayNameAttribute()
    {
        $days = [
            0 => 'Воскресенье',
            1 => 'Понедельник',
            2 => 'Вторник',
            3 => 'Среда',
            4 => 'Четверг',
            5 => 'Пятница',
            6 => 'Суббота'
        ];

        return $days[$this->day_of_week] ?? 'Неизвестно';
    }

    /**
     * Получить короткое название дня недели
     */
    public function getDayShortNameAttribute()
    {
        $days = [
            0 => 'Вс',
            1 => 'Пн',
            2 => 'Вт',
            3 => 'Ср',
            4 => 'Чт',
            5 => 'Пт',
            6 => 'Сб'
        ];

        return $days[$this->day_of_week] ?? '??';
    }

    /**
     * Проверить, работает ли мастер в указанное время
     */
    public function isWorkingAt($time)
    {
        if (!$this->is_working) {
            return false;
        }

        $time = \Carbon\Carbon::parse($time);
        $startTime = \Carbon\Carbon::parse($this->start_time);
        $endTime = \Carbon\Carbon::parse($this->end_time);

        return $time->between($startTime, $endTime);
    }

    /**
     * Получить время начала в формате H:i
     */
    public function getStartTimeFormattedAttribute()
    {
        if (!$this->start_time) {
            return '09:00';
        }
        
        if ($this->start_time instanceof \Carbon\Carbon) {
            return $this->start_time->format('H:i');
        }
        
        // Если это строка, попробуем распарсить
        try {
            return \Carbon\Carbon::parse($this->start_time)->format('H:i');
        } catch (\Exception $e) {
            return '09:00';
        }
    }

    /**
     * Получить время окончания в формате H:i
     */
    public function getEndTimeFormattedAttribute()
    {
        if (!$this->end_time) {
            return '18:00';
        }
        
        if ($this->end_time instanceof \Carbon\Carbon) {
            return $this->end_time->format('H:i');
        }
        
        // Если это строка, попробуем распарсить
        try {
            return \Carbon\Carbon::parse($this->end_time)->format('H:i');
        } catch (\Exception $e) {
            return '18:00';
        }
    }
}
