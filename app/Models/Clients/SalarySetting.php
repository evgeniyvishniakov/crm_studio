<?php

namespace App\Models\Clients;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalarySetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'user_id',
        'salary_type',
        'fixed_salary',
        'service_percentage',
        'sales_percentage',
        'min_salary',
        'max_salary',
    ];

    protected $casts = [
        'fixed_salary' => 'decimal:2',
        'service_percentage' => 'decimal:2',
        'sales_percentage' => 'decimal:2',
        'min_salary' => 'decimal:2',
        'max_salary' => 'decimal:2',
    ];

    /**
     * Связь с проектом
     */
    public function project()
    {
        return $this->belongsTo(\App\Models\Admin\Project::class);
    }

    /**
     * Связь с пользователем
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\Admin\User::class);
    }

    /**
     * Получить текстовое представление типа зарплаты
     */
    public function getSalaryTypeTextAttribute()
    {
        return match($this->salary_type) {
            'fixed' => __('messages.fixed_salary'),
            'percentage' => __('messages.percentage_salary'),
            'mixed' => __('messages.mixed_salary'),
            default => __('messages.unknown')
        };
    }

    /**
     * Рассчитать зарплату за период
     */
    public function calculateSalary($services, $sales, $periodStart, $periodEnd, $bonuses = 0, $penalties = 0, $notes = null)
    {
        $servicesAmount = $services->sum('price');
        $salesAmount = $sales->sum('total_amount');

        $fixedSalary = $this->fixed_salary ?? 0;
        $percentageSalary = 0;

        if ($this->service_percentage) {
            $percentageSalary += ($servicesAmount * $this->service_percentage / 100);
        }

        if ($this->sales_percentage) {
            $percentageSalary += ($salesAmount * $this->sales_percentage / 100);
        }

        $totalSalary = $fixedSalary + $percentageSalary + $bonuses - $penalties;

        // Применяем минимальную и максимальную зарплату
        if ($this->min_salary && $totalSalary < $this->min_salary) {
            $totalSalary = $this->min_salary;
        }

        if ($this->max_salary && $totalSalary > $this->max_salary) {
            $totalSalary = $this->max_salary;
        }

        // Создаем запись расчета
        $calculation = \App\Models\Clients\SalaryCalculation::create([
            'project_id' => $this->project_id,
            'user_id' => $this->user_id,
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
            'services_count' => $services->count(),
            'services_amount' => $servicesAmount,
            'sales_count' => $sales->count(),
            'sales_amount' => $salesAmount,
            'fixed_salary' => $fixedSalary,
            'percentage_salary' => $percentageSalary,
            'bonuses' => $bonuses,
            'penalties' => $penalties,
            'total_salary' => $totalSalary,
            'status' => 'calculated',
            'notes' => $notes,
        ]);

        return $calculation;
    }
}
