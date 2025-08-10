<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\Admin\User;

class EmployeeTimeOff extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'admin_user_id',
        'type',
        'start_date',
        'end_date',
        'status',
        'reason',
        'admin_notes',
        'approved_by',
        'approved_at'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime'
    ];

    /**
     * Получить дату начала в формате для HTML input
     */
    public function getStartDateInputAttribute()
    {
        return $this->start_date ? $this->start_date->format('Y-m-d') : null;
    }

    /**
     * Получить дату окончания в формате для HTML input
     */
    public function getEndDateInputAttribute()
    {
        return $this->end_date ? $this->end_date->format('Y-m-d') : null;
    }

    /**
     * Связь с сотрудником
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }

    /**
     * Связь с проектом
     */
    public function project()
    {
        return $this->belongsTo(\App\Models\Admin\Project::class);
    }

    /**
     * Связь с утвердившим отпуск
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Получить текстовое описание типа отпуска
     */
    public function getTypeTextAttribute()
    {
        $types = [
            'vacation' => 'Отпуск',
            'sick_leave' => 'Больничный',
            'personal_leave' => 'Личный отпуск',
            'unpaid_leave' => 'Отпуск без содержания'
        ];

        return $types[$this->type] ?? 'Неизвестно';
    }

    /**
     * Получить текстовое описание статуса
     */
    public function getStatusTextAttribute()
    {
        $statuses = [
            'pending' => 'Ожидает',
            'approved' => 'Одобрено',
            'rejected' => 'Отклонено',
            'cancelled' => 'Отменено'
        ];

        return $statuses[$this->status] ?? 'Неизвестно';
    }

    /**
     * Проверить, можно ли редактировать отпуск
     */
    public function canEdit()
    {
        return in_array($this->status, ['pending', 'rejected']);
    }

    /**
     * Проверить, можно ли удалить отпуск
     */
    public function canDelete()
    {
        return $this->status !== 'approved' || $this->start_date > Carbon::today();
    }

    /**
     * Проверить, можно ли одобрить отпуск
     */
    public function canApprove()
    {
        return $this->status === 'pending';
    }

    /**
     * Получить количество дней отпуска
     */
    public function getDaysCountAttribute()
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    /**
     * Скоуп для предстоящих отпусков
     */
    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', Carbon::today())
                    ->where('status', '!=', 'cancelled');
    }

    /**
     * Скоуп для текущих отпусков
     */
    public function scopeCurrent($query)
    {
        $today = Carbon::today();
        return $query->where('start_date', '<=', $today)
                    ->where('end_date', '>=', $today)
                    ->where('status', 'approved');
    }
}