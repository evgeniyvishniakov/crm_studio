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
use App\Models\EmployeeTimeOff;

class WorkScheduleController extends Controller
{
    /**
     * Отображение главной страницы графика работы
     */
    public function index()
    {
        $user = Auth::user();
        $projectId = $user->project_id;
        
        // Получаем всех сотрудников проекта (для настройки расписания)
        $allEmployees = User::where('project_id', $projectId)
            ->orderBy('name')
            ->get();
            
        // Получаем сотрудников с расписанием для отображения в таблице
        $employeesWithSchedule = $this->getEmployeesWithSchedule($projectId);
        
        // Статистика для обзора
        $stats = $this->getScheduleStats($projectId);
        
        // Текущие расписания на неделю - показываем только тех, у кого есть расписание
        $currentWeekSchedules = $this->getWeekSchedulesWithSmartLogic($employeesWithSchedule, 0);
        
        // Предстоящие отпуска/больничные
        $upcomingTimeOffs = $this->getUpcomingTimeOffs($projectId);
        
        return view('client.work-schedules.index', compact(
            'employeesWithSchedule',
            'allEmployees',
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
        
        $employees = $this->getEmployeesWithSchedule($projectId);
            
        // Сколько сотрудников работает сегодня
        $workingToday = $this->getWorkingEmployeesToday($employees);
        
        // Сколько записей на эту неделю
        $appointmentsThisWeek = Appointment::where('project_id', $projectId)
            ->whereBetween('date', [$startOfWeek, $endOfWeek])
            ->count();
            
        // Сколько часов отработано на этой неделе
        $hoursThisWeek = $this->calculateWorkedHours($employees, $startOfWeek, $endOfWeek);
        
        // Предстоящие отпуска
        $upcomingTimeOffsCount = EmployeeTimeOff::where('project_id', $projectId)
            ->where('status', '!=', 'cancelled')
            ->where('status', '!=', 'rejected')
            ->where('start_date', '>=', $today)
            ->count();
        
        return [
            'total_employees' => $employees->count(),
            'working_today' => $workingToday,
            'appointments_this_week' => $appointmentsThisWeek,
            'hours_this_week' => $hoursThisWeek,
            'upcoming_time_offs' => $upcomingTimeOffsCount
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
        
        // Получаем сотрудников проекта с расписанием
        $employees = $this->getEmployeesWithSchedule($projectId);
            
        // Статистика для обзора
        $stats = $this->getScheduleStats($projectId);
        
        // Используем ту же логику что и для навигации по неделям (offset = 0 = текущая неделя)
        $currentWeekSchedules = $this->getWeekSchedulesWithSmartLogic($employees, 0);
        
        return response()->json([
            'success' => true,
            'stats' => $stats,
            'currentWeekSchedules' => $currentWeekSchedules
        ]);
    }
    
    /**
     * API для получения расписания конкретной недели
     */
    public function getWeekSchedule(Request $request)
    {
        $user = Auth::user();
        $projectId = $user->project_id;
        $offset = (int) $request->get('offset', 0);
        
        // Получаем сотрудников проекта с расписанием
        $employees = $this->getEmployeesWithSchedule($projectId);
            
        // Получаем расписания на конкретную неделю с умной логикой
        $weekSchedules = $this->getWeekSchedulesWithSmartLogic($employees, $offset);
        
        $response = [
            'success' => true,
            'schedules' => $weekSchedules
        ];
        
        // Добавляем предупреждение для старых периодов
        if ($offset < -4) {
            $response['warning'] = 'Внимание: Для периодов старше месяца расписание определяется по записям клиентов.';
        }
        

        
        return response()->json($response);
    }
    
    /**
     * Получение расписаний на конкретную неделю с умной логикой
     */
    private function getWeekSchedulesWithSmartLogic($employees, $offset)
    {
        $schedules = [];
        // ИСПРАВЛЕНО: Правильный расчет дат для offset с учетом часового пояса
        $today = Carbon::now()->setTimezone('Europe/Moscow');
        
        // Начинаем с текущей недели (offset = 0)
        $currentWeekStart = $today->copy()->startOfWeek(Carbon::MONDAY);
        
        // Добавляем нужное количество недель
        $startOfWeek = $currentWeekStart->copy()->addWeeks($offset);
        $endOfWeek = $startOfWeek->copy()->endOfWeek(Carbon::SUNDAY);
        
        // Получаем все записи на эту неделю для всех сотрудников
        $appointmentsRaw = Appointment::whereBetween('date', [$startOfWeek, $endOfWeek])
            ->whereIn('user_id', $employees->pluck('id'))
            ->orderBy('date')
            ->orderBy('time')
            ->get();
            
        $appointments = $appointmentsRaw->groupBy(['user_id', function($item) {
            return $item->date->format('Y-m-d'); // Приводим дату к формату Y-m-d
        }]);
        
        // Получаем отпуска на эту неделю для всех сотрудников
        // ИСПРАВЛЕНО: Теперь загружаем только отпуска, которые действительно пересекаются с текущей неделей
        // ИСПРАВЛЕНО: Загружаем ВСЕ отпуска для проекта, а фильтруем потом
        $rawTimeOffs = EmployeeTimeOff::where('project_id', Auth::user()->project_id)
            ->where('status', '!=', 'cancelled')
            ->where('status', '!=', 'rejected')
            ->with('user')
            ->get();
            
        $timeOffs = $rawTimeOffs->groupBy('admin_user_id');
            

        
        // ФИЛЬТРУЕМ ОТПУСКА: оставляем только те, что пересекаются с текущей неделей
        $filteredTimeOffs = collect();
        foreach ($timeOffs as $employeeId => $employeeTimeOffs) {
            $filteredEmployeeTimeOffs = collect();
            foreach ($employeeTimeOffs as $timeOff) {
                // Отпуск пересекается с неделей если:
                // - начало отпуска <= конец недели И конец отпуска >= начало недели
                if ($timeOff->start_date->lte($endOfWeek) && $timeOff->end_date->gte($startOfWeek)) {
                    $filteredEmployeeTimeOffs->push($timeOff);
                }
            }
            if ($filteredEmployeeTimeOffs->count() > 0) {
                $filteredTimeOffs[$employeeId] = $filteredEmployeeTimeOffs;
            }
        }
        $timeOffs = $filteredTimeOffs;
        
        // Получаем текущие расписания для сравнения
        $currentSchedules = [];
        foreach ($employees as $employee) {
            $userSchedules = UserSchedule::where('user_id', $employee->id)
                ->orderBy('day_of_week')
                ->get()
                ->keyBy('day_of_week');
            $currentSchedules[$employee->id] = $userSchedules;
        }
        
        // Для прошлых периодов проверяем, есть ли у сотрудника записи вообще
        $employeesWithHistoricalAppointments = [];
        if ($offset < 0) {
            foreach ($employees as $employee) {
                $hasAnyAppointments = Appointment::where('user_id', $employee->id)->exists();
                $employeesWithHistoricalAppointments[$employee->id] = $hasAnyAppointments;
            }
        }
        
        foreach ($employees as $employee) {
            $weekSchedule = [];
            
            for ($i = 0; $i < 7; $i++) {
                $date = $startOfWeek->copy()->addDays($i);
                $dayOfWeek = $date->dayOfWeek; // 0 = воскресенье, 1 = понедельник
                $dateString = $date->format('Y-m-d');
                
                // Проверяем есть ли отпуск на этот день - ИСПРАВЛЕНО: используем тот же подход что и для записей
                $timeOffForDay = null;
                $timeOffsForDay = $timeOffs->get($employee->id, collect())->filter(function($timeOff) use ($date) {
                    // Простая проверка: дата попадает в период отпуска
                    return $date->gte($timeOff->start_date) && 
                           $date->lte($timeOff->end_date) && 
                           in_array($timeOff->status, ['pending', 'approved']);
                });
                
                if ($timeOffsForDay->count() > 0) {
                    $timeOffForDay = $timeOffsForDay->first();
                }
                
                // Если есть отпуск - показываем его
                if ($timeOffForDay) {
                    $weekSchedule[] = [
                        'date' => $date,
                        'day_name' => $this->getDayName($dayOfWeek),
                        'is_working' => false,
                        'start_time' => null,
                        'end_time' => null,
                        'status' => 'time_off',
                        'time_off_type' => $timeOffForDay->type,
                        'time_off_reason' => $timeOffForDay->reason,
                        'time_off_status' => $timeOffForDay->status,
                        'appointments_count' => 0,
                        'free_hours' => 0,
                        'source' => 'time_off'
                    ];
                    continue; // Переходим к следующему дню
                }
                
                // Получаем записи на этот день
                $appointmentsForDay = $appointments->get($employee->id, collect())->get($dateString, collect());
                $appointmentsCount = $appointmentsForDay->count();
                
                if ($appointmentsCount > 0) {
                    // ЕСТЬ ЗАПИСИ = РАБОЧИЙ ДЕНЬ
                    
                    // Берем время работы из расписания сотрудника (если есть)
                    $daySchedule = null;
                    if (isset($currentSchedules[$employee->id])) {
                        $daySchedule = $currentSchedules[$employee->id]->get($dayOfWeek);
                    }
                    
                    if ($daySchedule && $daySchedule->is_working) {
                        // Используем расписание для расчета свободного времени
                        $totalMinutes = $this->calculateWorkingMinutes($daySchedule->start_time, $daySchedule->end_time);
                        
                        // Считаем занятое время: длительность услуг + интервалы
                        $serviceDuration = $appointmentsForDay->sum('duration') ?: ($appointmentsCount * 60);
                        $intervalMinutes = $appointmentsCount * ($daySchedule->booking_interval ?? 30); // Интервал для каждой записи
                        $bookedMinutes = $serviceDuration + $intervalMinutes;
                        
                        $freeMinutes = max(0, $totalMinutes - $bookedMinutes);
                        
                        $weekSchedule[] = [
                            'date' => $date,
                            'day_name' => $this->getDayName($dayOfWeek),
                            'is_working' => true,
                            'start_time' => $daySchedule->start_time_formatted,
                            'end_time' => $daySchedule->end_time_formatted,
                            'status' => 'working',
                            'appointments_count' => $appointmentsCount,
                            'free_hours' => round($freeMinutes / 60, 1),
                            'source' => 'schedule_with_appointments'
                        ];
                    } else {
                        // Нет расписания - определяем время по записям
                        $firstTime = $appointmentsForDay->min('time');
                        $lastAppointment = $appointmentsForDay->sortByDesc('time')->first();
                        $lastTime = Carbon::parse($lastAppointment->time)->addMinutes($lastAppointment->duration ?? 60);
                        $totalMinutes = Carbon::parse($firstTime)->diffInMinutes($lastTime);
                        
                        // Считаем занятое время: длительность услуг + интервалы (используем стандартный интервал 30 мин)
                        $serviceDuration = $appointmentsForDay->sum('duration') ?: ($appointmentsCount * 60);
                        $intervalMinutes = $appointmentsCount * 30; // Стандартный интервал 30 минут
                        $bookedMinutes = $serviceDuration + $intervalMinutes;
                        
                        $freeMinutes = max(0, $totalMinutes - $bookedMinutes);
                        
                        $weekSchedule[] = [
                            'date' => $date,
                            'day_name' => $this->getDayName($dayOfWeek),
                            'is_working' => true,
                            'start_time' => Carbon::parse($firstTime)->format('H:i'),
                            'end_time' => $lastTime->format('H:i'),
                            'status' => 'working',
                            'appointments_count' => $appointmentsCount,
                            'free_hours' => round($freeMinutes / 60, 1),
                            'source' => 'appointments_only'
                        ];
                    }
                } else {
                    // НЕТ ЗАПИСЕЙ - проверяем есть ли текущее расписание
                    $daySchedule = null;
                    if (isset($currentSchedules[$employee->id])) {
                        $daySchedule = $currentSchedules[$employee->id]->get($dayOfWeek);
                    }
                    
                    // Проверяем нужно ли показывать рабочий день
                    $shouldShowWorkingDay = false;
                    
                    if ($offset >= -4) {
                        // Текущая и недавние недели (последний месяц) - показываем по текущему расписанию
                        $shouldShowWorkingDay = $daySchedule && $daySchedule->is_working;
                    } else {
                        // Старые недели (больше месяца назад) - показываем только если есть записи в истории
                        $hasHistoricalAppointments = $employeesWithHistoricalAppointments[$employee->id] ?? false;
                        $shouldShowWorkingDay = $daySchedule && $daySchedule->is_working && $hasHistoricalAppointments;
                    }
                    
                    if ($shouldShowWorkingDay) {
                        $totalMinutes = $this->calculateWorkingMinutes($daySchedule->start_time, $daySchedule->end_time);
                        
                        $weekSchedule[] = [
                            'date' => $date,
                            'day_name' => $this->getDayName($dayOfWeek),
                            'is_working' => true,
                            'start_time' => $daySchedule->start_time_formatted,
                            'end_time' => $daySchedule->end_time_formatted,
                            'status' => 'working',
                            'appointments_count' => $appointmentsCount, // Используем реальное количество записей
                            'free_hours' => round($totalMinutes / 60, 1),
                            'source' => $offset >= 0 ? 'schedule' : 'historical'
                        ];
                    } else {
                        // НЕТ ЗАПИСЕЙ = ВЫХОДНОЙ
                        $weekSchedule[] = [
                            'date' => $date,
                            'day_name' => $this->getDayName($dayOfWeek),
                            'is_working' => false,
                            'start_time' => null,
                            'end_time' => null,
                            'status' => 'day_off',
                            'appointments_count' => 0,
                            'free_hours' => 0,
                            'source' => 'auto' // Автоматически определено
                        ];
                    }
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
     * Получение всех сотрудников проекта, у которых есть расписание
     */
    private function getEmployeesWithSchedule($projectId)
    {
        // Получаем всех сотрудников проекта
        $projectEmployees = User::where('project_id', $projectId)
            ->orderBy('name')
            ->get();
            
        // Фильтруем только тех, у кого есть расписание
        return $projectEmployees->filter(function($employee) {
            return UserSchedule::where('user_id', $employee->id)->exists();
        });
    }
    
    /**
     * Получение списка отпусков
     */
    public function getTimeOffs()
    {
        $user = Auth::user();
        $projectId = $user->project_id;
        
        $timeOffs = EmployeeTimeOff::where('project_id', $projectId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json([
            'success' => true,
            'timeOffs' => $timeOffs
        ]);
    }
    
    /**
     * Получение конкретного отпуска
     */
    public function getTimeOff($id)
    {
        $user = Auth::user();
        $projectId = $user->project_id;
        
        $timeOff = EmployeeTimeOff::where('project_id', $projectId)
            ->where('id', $id)
            ->with('user')
            ->first();
        
        if (!$timeOff) {
            return response()->json([
                'success' => false,
                'message' => 'Отпуск не найден'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'timeOff' => $timeOff
        ]);
    }
    
    /**
     * Создание нового отпуска
     */
    public function storeTimeOff(Request $request)
    {
        $user = Auth::user();
        $projectId = $user->project_id;
        
        $request->validate([
            'employee_id' => 'required|exists:admin_users,id',
            'type' => 'required|in:vacation,sick_leave,personal_leave,unpaid_leave',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string|max:1000'
        ]);
        
        // Проверяем, что сотрудник принадлежит тому же проекту
        $employee = User::where('id', $request->employee_id)
            ->where('project_id', $projectId)
            ->first();
            
        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Сотрудник не найден в вашем проекте'
            ], 400);
        }
        
        // Проверяем пересечение с существующими отпусками
        $existingTimeOff = EmployeeTimeOff::where('admin_user_id', $request->employee_id)
            ->where('status', '!=', 'cancelled')
            ->where(function($query) use ($request) {
                $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                      ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                      ->orWhere(function($q) use ($request) {
                          $q->where('start_date', '<=', $request->start_date)
                            ->where('end_date', '>=', $request->end_date);
                      });
            })
            ->exists();
            
        if ($existingTimeOff) {
            return response()->json([
                'success' => false,
                'message' => 'У сотрудника уже есть отпуск на эти даты'
            ], 400);
        }
        
        $timeOff = EmployeeTimeOff::create([
            'project_id' => $projectId,
            'admin_user_id' => $request->employee_id,
            'type' => $request->type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
            'status' => 'pending'
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Отпуск успешно добавлен',
            'timeOff' => $timeOff->load('user')
        ]);
    }
    
    /**
     * Обновление отпуска
     */
    public function updateTimeOff(Request $request, $id)
    {
        $user = Auth::user();
        $projectId = $user->project_id;
        
        $timeOff = EmployeeTimeOff::where('project_id', $projectId)
            ->where('id', $id)
            ->first();
        
        if (!$timeOff) {
            return response()->json([
                'success' => false,
                'message' => 'Отпуск не найден'
            ], 404);
        }
        
        $request->validate([
            'employee_id' => 'required|exists:admin_users,id',
            'type' => 'required|in:vacation,sick_leave,personal_leave,unpaid_leave',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string|max:1000'
        ]);
        
        // Проверяем, что сотрудник принадлежит тому же проекту
        $employee = User::where('id', $request->employee_id)
            ->where('project_id', $projectId)
            ->first();
            
        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Сотрудник не найден в вашем проекте'
            ], 400);
        }
        
        // Проверяем пересечение с другими отпусками (исключая текущий)
        $existingTimeOff = EmployeeTimeOff::where('admin_user_id', $request->employee_id)
            ->where('id', '!=', $id)
            ->where('status', '!=', 'cancelled')
            ->where(function($query) use ($request) {
                $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                      ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                      ->orWhere(function($q) use ($request) {
                          $q->where('start_date', '<=', $request->start_date)
                            ->where('end_date', '>=', $request->end_date);
                      });
            })
            ->exists();
            
        if ($existingTimeOff) {
            return response()->json([
                'success' => false,
                'message' => 'У сотрудника уже есть отпуск на эти даты'
            ], 400);
        }
        
        $timeOff->update([
            'admin_user_id' => $request->employee_id,
            'type' => $request->type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Отпуск успешно обновлен',
            'timeOff' => $timeOff->load('user')
        ]);
    }
    
    /**
     * Удаление отпуска
     */
    public function destroyTimeOff($id)
    {
        $user = Auth::user();
        $projectId = $user->project_id;
        
        $timeOff = EmployeeTimeOff::where('project_id', $projectId)
            ->where('id', $id)
            ->first();
        
        if (!$timeOff) {
            return response()->json([
                'success' => false,
                'message' => 'Отпуск не найден'
            ], 404);
        }
        
        // Проверяем можно ли удалить отпуск
        if (!$timeOff->canDelete()) {
            return response()->json([
                'success' => false,
                'message' => 'Нельзя удалить одобренный отпуск, который уже начался'
            ], 400);
        }
        
        $timeOff->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Отпуск успешно удален'
        ]);
    }
    
    /**
     * Получение предстоящих отпусков (ближайшие 30 дней)
     */
    private function getUpcomingTimeOffs($projectId)
    {
        $today = Carbon::today();
        $in30Days = $today->copy()->addDays(30);
        
        return EmployeeTimeOff::where('project_id', $projectId)
            ->where('status', '!=', 'cancelled')
            ->where('status', '!=', 'rejected')
            ->where('start_date', '>=', $today)
            ->where('start_date', '<=', $in30Days)
            ->with('user')
            ->orderBy('start_date', 'asc')
            ->get()
            ->map(function($timeOff) {
                // Добавляем текстовые представления
                $typeNames = [
                    'vacation' => 'Отпуск',
                    'sick_leave' => 'Больничный',
                    'personal_leave' => 'Личный отпуск',
                    'unpaid_leave' => 'Отпуск без содержания'
                ];
                
                $statusNames = [
                    'pending' => 'Ожидает',
                    'approved' => 'Одобрено'
                ];
                
                $timeOff->type_text = $typeNames[$timeOff->type] ?? $timeOff->type;
                $timeOff->status_text = $statusNames[$timeOff->status] ?? $timeOff->status;
                
                return $timeOff;
            });
    }
}
