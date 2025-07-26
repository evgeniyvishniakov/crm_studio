<?php

namespace App\Http\Controllers;

use App\Models\Admin\Project;
use App\Models\Clients\Service;
use App\Models\Clients\UserSchedule;
use App\Models\Clients\Appointment;
use App\Models\Clients\Client;
use App\Models\Admin\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PublicBookingController extends Controller
{
    /**
     * Показать страницу бронирования
     */
    public function show($slug)
    {
        // Находим проект по slug
        $project = Project::where('booking_enabled', true)
            ->get()
            ->first(function($project) use ($slug) {
                return $project->slug === $slug;
            });

        if (!$project) {
            abort(404, 'Страница не найдена');
        }

        // Получаем настройки бронирования
        $bookingSettings = $project->getOrCreateBookingSettings();
        
        // Получаем активные услуги для веб-записи
        $userServices = \App\Models\Clients\UserService::whereHas('user', function($query) use ($project) {
            $query->where('project_id', $project->id);
        })
        ->where('is_active_for_booking', true)
        ->with(['service', 'user'])
        ->get();
        
        // Получаем уникальные услуги из активных UserService
        $services = $userServices->pluck('service')->unique('id')->values();
        
        // Получаем мастеров, у которых есть активные услуги
        $users = $userServices->pluck('user')->unique('id')->values();

        return view('public.booking.index', compact(
            'project',
            'bookingSettings',
            'services',
            'users',
            'userServices'
        ));
    }

    /**
     * Получить доступные слоты времени
     */
    public function getAvailableSlots(Request $request)
    {
        $projectId = $request->input('project_id');
        $userId = $request->input('user_id');
        $date = $request->input('date');
        $serviceId = $request->input('service_id');



        $project = Project::findOrFail($projectId);
        $bookingSettings = $project->getOrCreateBookingSettings();
        $service = Service::findOrFail($serviceId);
        $user = User::findOrFail($userId);

        // Проверяем, можно ли записаться на эту дату
        if (!$bookingSettings->canBookOnDate($date)) {
            return response()->json([
                'success' => false,
                'message' => 'Запись на эту дату недоступна'
            ]);
        }

        // Получаем расписание мастера на этот день
        $carbonDayOfWeek = Carbon::parse($date)->dayOfWeek;
        // Конвертируем Carbon день недели (0=воскресенье) в наш формат (1=понедельник)
        $dayOfWeek = $carbonDayOfWeek === 0 ? 7 : $carbonDayOfWeek;
        $schedule = UserSchedule::where('user_id', $userId)
            ->where('day_of_week', $dayOfWeek)
            ->first();

        if (!$schedule) {
            return response()->json([
                'success' => false,
                'message' => 'Мастер не работает в этот день (нет расписания)'
            ]);
        }

        if (!$schedule->is_working) {
            return response()->json([
                'success' => false,
                'message' => 'Мастер не работает в этот день (выходной)'
            ]);
        }

        // Получаем UserService для проверки доступности
        $userService = \App\Models\Clients\UserService::where('user_id', $userId)
            ->where('service_id', $serviceId)
            ->where('is_active_for_booking', true)
            ->first();

        if (!$userService) {
            return response()->json([
                'success' => false,
                'message' => 'Услуга недоступна для этого мастера'
            ]);
        }

        // Получаем длительность из UserService или базовой услуги
        $serviceDuration = $userService->duration ?: $service->duration ?: 60;

        // Используем интервал мастера (по умолчанию 30 минут)
        $masterInterval = $schedule->booking_interval ?: 30;
        
        // Генерируем слоты времени
        $slots = $this->generateTimeSlots(
            $schedule->start_time,
            $schedule->end_time,
            $masterInterval,
            $date,
            $userId,
            $serviceDuration
        );



        return response()->json([
            'success' => true,
            'slots' => $slots
        ]);
    }

    /**
     * Получить расписание мастера
     */
    public function getMasterSchedule(Request $request, $slug)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer'
        ]);

        $project = Project::where('booking_enabled', true)
            ->get()
            ->first(function($project) use ($slug) {
                return $project->slug === $slug;
            });

        if (!$project) {
            return response()->json([
                'success' => false,
                'message' => 'Проект не найден'
            ]);
        }

        $userId = $validated['user_id'];
        
        // Получаем расписание мастера
        $schedules = UserSchedule::where('user_id', $userId)->get();
        
        $scheduleData = [];
        foreach ($schedules as $schedule) {
            $scheduleData[$schedule->day_of_week] = [
                'is_working' => $schedule->is_working,
                'start_time' => $schedule->start_time,
                'end_time' => $schedule->end_time
            ];
        }

        return response()->json([
            'success' => true,
            'schedule' => $scheduleData
        ]);
    }

    /**
     * Создать запись
     */
    public function store(Request $request)
    {
        // Логируем входящие данные
        \Log::info('PublicBooking store - входящие данные:', $request->all());
        
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'service_id' => 'required|exists:services,id',
            'user_id' => 'required|exists:admin_users,id',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|date_format:H:i',
            'client_name' => 'required|string|max:255',
            'client_phone' => 'required|string|max:20',
            'client_email' => 'nullable|email|max:255',
        ]);
        
        \Log::info('PublicBooking store - валидированные данные:', $validated);

        $project = Project::findOrFail($validated['project_id']);
        $service = Service::findOrFail($validated['service_id']);
        $user = User::findOrFail($validated['user_id']);

        // Проверяем, что все принадлежат одному проекту
        if ($project->id !== $user->project_id || $service->project_id !== $project->id) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации данных'
            ], 422);
        }

        // Проверяем доступность времени
        $existingAppointment = Appointment::where('user_id', $validated['user_id'])
            ->where('date', $validated['date'])
            ->where('time', $validated['time'])
            ->first();

        if ($existingAppointment) {
            return response()->json([
                'success' => false,
                'message' => 'Это время уже занято'
            ], 422);
        }

        // Создаем или находим клиента
        $client = Client::firstOrCreate(
            ['phone' => $validated['client_phone'], 'project_id' => $project->id],
            [
                'name' => $validated['client_name'],
                'email' => $validated['client_email'],
                'project_id' => $project->id
            ]
        );

        // Создаем запись
        $appointment = Appointment::create([
            'client_id' => $client->id,
            'service_id' => $validated['service_id'],
            'user_id' => $validated['user_id'],
            'date' => $validated['date'],
            'time' => $validated['time'],
            'price' => $service->price,
            'duration' => $service->duration ?? 60,
            'status' => 'pending',
            'project_id' => $project->id,
            'notes' => 'Запись через онлайн-бронирование'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Запись успешно создана! Мы свяжемся с вами для подтверждения.',
            'booking' => [
                'service_name' => $service->name,
                'master_name' => $user->name,
                'date' => $validated['date'],
                'time' => $validated['time']
            ]
        ]);
    }

    /**
     * Генерировать слоты времени
     */
    private function generateTimeSlots($startTime, $endTime, $interval, $date, $userId, $serviceDuration)
    {
        $slots = [];
        $currentTime = Carbon::parse($startTime);
        $endTime = Carbon::parse($endTime);
        
        // $serviceDuration - это количество минут (число)
        $serviceDurationMinutes = (int) $serviceDuration;

        // Получаем все существующие записи на эту дату для этого мастера
        $existingAppointments = Appointment::where('user_id', $userId)
            ->where('date', $date)
            ->get();

        while ($currentTime->lt($endTime)) {
            $slotEnd = $currentTime->copy()->addMinutes($serviceDurationMinutes);
            
            if ($slotEnd->lte($endTime)) {
                // Проверяем, не пересекается ли этот слот с существующими записями
                $isAvailable = true;
                
                foreach ($existingAppointments as $appointment) {
                    $appointmentStart = Carbon::parse($appointment->time);
                    $appointmentEnd = $appointmentStart->copy()->addMinutes($appointment->duration ?? 60);
                    
                    // Проверяем пересечение интервалов
                    // Новый слот: [currentTime, slotEnd]
                    // Существующая запись: [appointmentStart, appointmentEnd]
                    // Пересечение: max(currentTime, appointmentStart) < min(slotEnd, appointmentEnd)
                    
                    $overlapStart = max($currentTime, $appointmentStart);
                    $overlapEnd = min($slotEnd, $appointmentEnd);
                    
                    if ($overlapStart < $overlapEnd) {
                        $isAvailable = false;
                        break;
                    }
                    
                    // Дополнительная проверка: новый слот не должен начинаться раньше, чем закончится предыдущая запись + интервал
                    $appointmentEndWithInterval = $appointmentEnd->copy()->addMinutes($interval);
                    if ($currentTime < $appointmentEndWithInterval) {
                        $isAvailable = false;
                        break;
                    }
                }

                if ($isAvailable) {
                    $slots[] = [
                        'time' => $currentTime->format('H:i'),
                        'available' => true
                    ];
                }
            }

            $currentTime->addMinutes($interval);
        }

        return $slots;
    }
}
