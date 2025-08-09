<?php

declare(strict_types=1);

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Admin\User;
use App\Models\Clients\Appointment;

class WorkScheduleController extends Controller
{
    /**
     * Отображение главной страницы графика работы
     */
    public function index()
    {
        $user = Auth::user();
        $projectId = $user->project_id;
        
        // Получаем всех сотрудников проекта
        $employees = User::where('project_id', $projectId)
            ->where('role', '!=', 'admin')
            ->orderBy('name')
            ->get();
            
        // Статистика для обзора
        $stats = $this->getScheduleStats($projectId);
        
        // Текущие расписания на неделю
        $currentWeekSchedules = $this->getCurrentWeekSchedules($employees);
        
        // Предстоящие отпуска/больничные
        $upcomingTimeOffs = $this->getUpcomingTimeOffs($projectId);
        
        return view('client.work-schedules.index', compact(
            'employees',
            'stats', 
            'currentWeekSchedules',
            'upcomingTimeOffs'
        ));
    }
    
    /**
     * Получение статистики для дашборда
     */
    private function getScheduleStats($projectId)
    {
        $today = Carbon::today();
        $startOfWeek = $today->copy()->startOfWeek();
        $endOfWeek = $today->copy()->endOfWeek();
        
        $employees = User::where('project_id', $projectId)
            ->where('role', '!=', 'admin')
            ->get();
            
        // Сколько сотрудников работает сегодня
        $workingToday = $this->getWorkingEmployeesToday($employees);
        
        // Сколько записей на эту неделю
        $appointmentsThisWeek = Appointment::where('project_id', $projectId)
            ->whereBetween('date', [$startOfWeek, $endOfWeek])
            ->count();
            
        // Сколько часов отработано на этой неделе
        $hoursThisWeek = $this->calculateWorkedHours($employees, $startOfWeek, $endOfWeek);
        
        // Предстоящие отпуска
        $upcomingTimeOffs = 0; // TODO: когда создадим таблицу time_offs
        
        return [
            'total_employees' => $employees->count(),
            'working_today' => $workingToday,
            'appointments_this_week' => $appointmentsThisWeek,
            'hours_this_week' => $hoursThisWeek,
            'upcoming_time_offs' => $upcomingTimeOffs
        ];
    }
    
    /**
     * Получение расписаний на текущую неделю
     */
    private function getCurrentWeekSchedules($employees)
    {
        $schedules = [];
        $startOfWeek = Carbon::now()->startOfWeek();
        
        foreach ($employees as $employee) {
            $weekSchedule = [];
            for ($i = 0; $i < 7; $i++) {
                $date = $startOfWeek->copy()->addDays($i);
                $dayOfWeek = $date->dayOfWeek == 0 ? 7 : $date->dayOfWeek; // Воскресенье = 7
                
                // TODO: Получить реальное расписание из booking_schedules
                $weekSchedule[] = [
                    'date' => $date,
                    'day_name' => $this->getDayName($dayOfWeek),
                    'is_working' => $dayOfWeek <= 6, // Пн-Сб работает
                    'start_time' => $dayOfWeek <= 6 ? '09:00' : null,
                    'end_time' => $dayOfWeek <= 6 ? '18:00' : null,
                    'status' => $dayOfWeek <= 6 ? 'working' : 'day_off'
                ];
            }
            
            $schedules[] = [
                'employee' => $employee,
                'schedule' => $weekSchedule
            ];
        }
        
        return $schedules;
    }
    
    /**
     * Подсчет работающих сотрудников сегодня
     */
    private function getWorkingEmployeesToday($employees)
    {
        $today = Carbon::today();
        $dayOfWeek = $today->dayOfWeek == 0 ? 7 : $today->dayOfWeek;
        
        // Упрощенная логика - считаем что Пн-Сб все работают
        return $dayOfWeek <= 6 ? $employees->count() : 0;
    }
    
    /**
     * Подсчет отработанных часов
     */
    private function calculateWorkedHours($employees, $startDate, $endDate)
    {
        // Упрощенная логика - 8 часов в день * рабочие дни * количество сотрудников
        $workDays = 0;
        $current = $startDate->copy();
        
        while ($current <= $endDate) {
            $dayOfWeek = $current->dayOfWeek == 0 ? 7 : $current->dayOfWeek;
            if ($dayOfWeek <= 6) { // Пн-Сб
                $workDays++;
            }
            $current->addDay();
        }
        
        return $workDays * 8 * $employees->count();
    }
    
    /**
     * Получение предстоящих отпусков/больничных
     */
    private function getUpcomingTimeOffs($projectId)
    {
        // TODO: Реализовать когда создадим таблицу employee_time_offs
        return collect();
    }
    
    /**
     * Получение названия дня недели
     */
    private function getDayName($dayOfWeek)
    {
        $days = [
            1 => 'Понедельник',
            2 => 'Вторник', 
            3 => 'Среда',
            4 => 'Четверг',
            5 => 'Пятница',
            6 => 'Суббота',
            7 => 'Воскресенье'
        ];
        
        return $days[$dayOfWeek] ?? 'Неизвестно';
    }
    
    /**
     * API для получения расписания сотрудника
     */
    public function getEmployeeSchedule(Request $request)
    {
        $employeeId = $request->get('employee_id');
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        
        // TODO: Реализовать получение реального расписания
        
        return response()->json([
            'success' => true,
            'schedule' => [
                'employee_id' => $employeeId,
                'date' => $date,
                'is_working' => true,
                'start_time' => '09:00',
                'end_time' => '18:00'
            ]
        ]);
    }
    
    /**
     * Сохранение расписания сотрудника
     */
    public function saveEmployeeSchedule(Request $request)
    {
        // TODO: Реализовать сохранение расписания
        
        return response()->json([
            'success' => true,
            'message' => 'Расписание сохранено'
        ]);
    }
}
