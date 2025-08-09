<?php

declare(strict_types=1);

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Admin\User;
use App\Models\Clients\Appointment;
use App\Models\Clients\UserSchedule;

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
        $endOfWeek = Carbon::now()->endOfWeek();
        
        // Получаем все записи на эту неделю для всех сотрудников
        $appointments = Appointment::whereBetween('date', [$startOfWeek, $endOfWeek])
            ->whereIn('user_id', $employees->pluck('id'))
            ->get()
            ->groupBy(['user_id', 'date']);
        
        foreach ($employees as $employee) {
            // Получаем расписание сотрудника из базы данных
            $userSchedules = UserSchedule::where('user_id', $employee->id)
                ->orderBy('day_of_week')
                ->get()
                ->keyBy('day_of_week');
            
            $weekSchedule = [];
            for ($i = 0; $i < 7; $i++) {
                $date = $startOfWeek->copy()->addDays($i);
                $dayOfWeek = $date->dayOfWeek; // 0 = воскресенье, 1 = понедельник
                $dateString = $date->format('Y-m-d');
                
                $daySchedule = $userSchedules->get($dayOfWeek);
                
                // Подсчитываем записи на этот день
                $appointmentsForDay = $appointments->get($employee->id, collect())->get($dateString, collect());
                $appointmentsCount = $appointmentsForDay->count();
                
                if ($daySchedule && $daySchedule->is_working) {
                    // Вычисляем свободное время
                    $totalMinutes = $this->calculateWorkingMinutes($daySchedule->start_time, $daySchedule->end_time);
                    $bookedMinutes = $appointmentsForDay->sum('duration') ?: 0; // Защита от null
                    $freeMinutes = max(0, $totalMinutes - $bookedMinutes);
                    
                    $weekSchedule[] = [
                        'date' => $date,
                        'day_name' => $this->getDayName($dayOfWeek),
                        'is_working' => true,
                        'start_time' => $daySchedule->start_time_formatted,
                        'end_time' => $daySchedule->end_time_formatted,
                        'status' => 'working',
                        'appointments_count' => $appointmentsCount,
                        'free_hours' => round($freeMinutes / 60, 1)
                    ];
                } else {
                    $weekSchedule[] = [
                        'date' => $date,
                        'day_name' => $this->getDayName($dayOfWeek),
                        'is_working' => false,
                        'start_time' => null,
                        'end_time' => null,
                        'status' => 'day_off',
                        'appointments_count' => 0,
                        'free_hours' => 0
                    ];
                }
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
        $dayOfWeek = $today->dayOfWeek;
        
        $workingCount = 0;
        foreach ($employees as $employee) {
            $schedule = UserSchedule::where('user_id', $employee->id)
                ->where('day_of_week', $dayOfWeek)
                ->where('is_working', true)
                ->first();
                
            if ($schedule) {
                $workingCount++;
            }
        }
        
        return $workingCount;
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
            0 => 'Воскресенье',
            1 => 'Понедельник',
            2 => 'Вторник', 
            3 => 'Среда',
            4 => 'Четверг',
            5 => 'Пятница',
            6 => 'Суббота'
        ];
        
        return $days[$dayOfWeek] ?? 'Неизвестно';
    }
    
    /**
     * Расчет рабочих минут между двумя временами
     */
    private function calculateWorkingMinutes($startTime, $endTime)
    {
        if (!$startTime || !$endTime) {
            return 0;
        }
        
        try {
            // Время может быть в разных форматах - Carbon объект или строка
            if ($startTime instanceof Carbon) {
                $start = $startTime;
            } else {
                $start = Carbon::parse($startTime);
            }
            
            if ($endTime instanceof Carbon) {
                $end = $endTime;
            } else {
                $end = Carbon::parse($endTime);
            }
            
            // Если конец раньше начала, значит работа переходит на следующий день
            if ($end->lessThan($start)) {
                $end->addDay();
            }
            
            return $start->diffInMinutes($end);
        } catch (\Exception $e) {
            // В случае ошибки парсинга возвращаем стандартную 8-часовую смену
            \Log::error('Ошибка парсинга времени: ' . $e->getMessage() . ', startTime: ' . $startTime . ', endTime: ' . $endTime);
            return 480; // 8 часов * 60 минут
        }
    }
    
    /**
     * API для получения расписания сотрудника
     */
    public function getEmployeeSchedule(Request $request)
    {
        $employeeId = $request->get('employee_id');
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Пользователь не авторизован'], 401);
        }
        
        // Проверяем, что сотрудник принадлежит тому же проекту
        $targetEmployee = User::where('id', $employeeId)
            ->where('project_id', $user->project_id)
            ->first();
            
        if (!$targetEmployee) {
            return response()->json(['success' => false, 'message' => 'Сотрудник не найден'], 404);
        }

        $schedules = UserSchedule::where('user_id', $employeeId)
            ->orderBy('day_of_week')
            ->get();

        // Преобразуем в формат, ожидаемый JavaScript
        $scheduleData = [];
        foreach ($schedules as $schedule) {
            $scheduleData[$schedule->day_of_week] = [
                'is_working' => $schedule->is_working,
                'start_time' => $schedule->start_time_formatted,
                'end_time' => $schedule->end_time_formatted,
                'notes' => $schedule->notes,
                'booking_interval' => $schedule->booking_interval
            ];
        }

        return response()->json([
            'success' => true,
            'schedule' => $scheduleData
        ]);
    }
    
    /**
     * Сохранение расписания сотрудника
     */
    public function saveEmployeeSchedule(Request $request)
    {
        $user = Auth::user();
        $employeeId = $request->input('employee_id');
        
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Пользователь не авторизован'], 401);
        }
        
        // Проверяем, что сотрудник принадлежит тому же проекту
        $targetEmployee = User::where('id', $employeeId)
            ->where('project_id', $user->project_id)
            ->first();
            
        if (!$targetEmployee) {
            return response()->json(['success' => false, 'message' => 'Сотрудник не найден'], 404);
        }

        $scheduleData = $request->input('schedule', []);
        
        // Удаляем старые записи
        UserSchedule::where('user_id', $employeeId)->delete();

        // Создаем новые записи
        foreach ($scheduleData as $dayOfWeek => $schedule) {
            if (isset($schedule['is_working']) && $schedule['is_working']) {
                UserSchedule::create([
                    'user_id' => $employeeId,
                    'day_of_week' => $dayOfWeek,
                    'start_time' => $schedule['start_time'] ?? '09:00',
                    'end_time' => $schedule['end_time'] ?? '18:00',
                    'is_working' => true,
                    'notes' => $schedule['notes'] ?? null,
                    'booking_interval' => $schedule['booking_interval'] ?? 30,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Расписание сохранено'
        ]);
    }
    
    /**
     * API для обновления данных на вкладке "Обзор"
     */
    public function refreshOverview()
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
        
        return response()->json([
            'success' => true,
            'stats' => $stats,
            'currentWeekSchedules' => $currentWeekSchedules
        ]);
    }
}
