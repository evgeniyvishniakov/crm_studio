<?php

namespace App\Http\Controllers;

use App\Models\Admin\Project;
use App\Models\Clients\Service;
use App\Models\Clients\UserSchedule;
use App\Models\Clients\Appointment;
use App\Models\Clients\Client;
use App\Models\Admin\User;
use App\Models\Notification;
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

        // Устанавливаем язык для веб-записи
        $bookingLanguageCode = $project->booking_language_code ?? $project->language_code ?? 'ua';
        app()->setLocale($bookingLanguageCode);

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
                'message' => __('messages.booking_validation_error')
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
                'message' => __('messages.time_already_booked')
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

        // Проверяем, не существует ли уже такая запись (защита от дублирования)
        $existingAppointment = Appointment::where('client_id', $client->id)
            ->where('service_id', $validated['service_id'])
            ->where('user_id', $validated['user_id'])
            ->where('date', $validated['date'])
            ->where('time', $validated['time'])
            ->where('project_id', $project->id)
            ->where('created_at', '>=', now()->subMinutes(5)) // Проверяем записи за последние 5 минут
            ->first();

        if ($existingAppointment) {
            \Log::info('Duplicate appointment detected', [
                'existing_appointment_id' => $existingAppointment->id,
                'client_id' => $client->id,
                'user_id' => $validated['user_id'],
                'date' => $validated['date'],
                'time' => $validated['time']
            ]);
            
            return response()->json([
                'success' => true,
                'message' => __('messages.booking_successful') . ' ' . __('messages.we_will_contact_you'),
                'booking' => [
                    'service_name' => $service->name,
                    'master_name' => $user->name,
                    'date' => $validated['date'],
                    'time' => $validated['time']
                ]
            ]);
        }

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
            'notes' => __('messages.booking_created_via_web')
        ]);
        
        \Log::info('Appointment created', [
            'appointment_id' => $appointment->id,
            'client_id' => $client->id,
            'user_id' => $validated['user_id'],
            'project_id' => $project->id
        ]);

        try {
            \Log::info('Before notifications block', [
                'project_id' => $project->id,
                'appointment_id' => $appointment->id,
                'user_id' => $user->id
            ]);
            
            // Создаем уникальный ключ для этой записи
            $bookingKey = md5($client->id . $validated['service_id'] . $validated['user_id'] . $validated['date'] . $validated['time'] . $project->id);
            
            // Создаем уведомление для мастера
            $notificationBody = __('messages.new_web_booking_notification_body', [
                'client_name' => $client->name,
                'service_name' => $service->name,
                'master_name' => $user->name,
                'date' => $validated['date'],
                'time' => $validated['time']
            ]) . ' [ID:' . $bookingKey . ']';

            // Уведомления создаются в цикле ниже для всех пользователей

            // Создаем уведомления только для мастера, который будет выполнять запись
            $allUsers = \App\Models\Admin\User::where('project_id', $project->id)
                ->where('id', $validated['user_id']) // Только для мастера, который будет выполнять запись
                ->get();

            \Log::info('Creating notifications for master', [
                'project_id' => $project->id,
                'master_id' => $validated['user_id'],
                'master_name' => $user->name
            ]);

            foreach ($allUsers as $notifyUser) {
                // Проверяем, не создали ли мы уже уведомление для мастера в рамках текущего запроса
                $cacheKey = 'notification_' . $notifyUser->id . '_' . $bookingKey;
                if (\Cache::has($cacheKey)) {
                    \Log::info('Notification already created for user in this request', [
                        'user_id' => $notifyUser->id,
                        'booking_key' => $bookingKey
                    ]);
                    continue;
                }
                
                // Проверяем, не создали ли мы уже уведомление для мастера
                // Улучшенная проверка: ищем уведомления для этой записи за последние 10 минут
                $existingNotification = \App\Models\Notification::where('user_id', $notifyUser->id)
                    ->where('type', 'web_booking')
                    ->where('project_id', $project->id)
                    ->where('created_at', '>=', now()->subMinutes(10)) // Увеличиваем интервал до 10 минут
                    ->where(function($query) use ($client, $service, $user, $validated, $bookingKey) {
                        // Проверяем по содержимому уведомления
                        $query->where('body', 'LIKE', '%' . $client->name . '%')
                              ->where('body', 'LIKE', '%' . $service->name . '%')
                              ->where('body', 'LIKE', '%' . $user->name . '%')
                              ->where('body', 'LIKE', '%' . $validated['date'] . '%')
                              ->where('body', 'LIKE', '%' . $validated['time'] . '%')
                              // Также проверяем по уникальному ключу записи
                              ->orWhere('body', 'LIKE', '%' . $bookingKey . '%');
                    })
                    ->first();

                if ($existingNotification) {
                    \Log::info('Notification already exists for user', [
                        'user_id' => $notifyUser->id,
                        'existing_notification_id' => $existingNotification->id,
                        'appointment_id' => $appointment->id
                    ]);
                    continue;
                }

                \Log::info('Attempting to create notification for master', [
                    'master_id' => $notifyUser->id,
                    'master_name' => $notifyUser->name,
                    'project_id' => $project->id,
                    'notification_body' => $notificationBody
                ]);
                try {
                    $notification = \App\Models\Notification::create([
                        'user_id' => $notifyUser->id,
                        'type' => 'web_booking',
                        'title' => __('messages.new_web_booking_notification_title'),
                        'body' => $notificationBody,
                        'url' => route('appointments.index'),
                        'is_read' => false,
                        'project_id' => $project->id
                    ]);
                    
                    // Устанавливаем кэш на 10 минут, чтобы предотвратить создание дублирующихся уведомлений
                    \Cache::put($cacheKey, true, now()->addMinutes(10));
                    
                    \Log::info('Notification created for master', [
                        'notification_id' => $notification->id,
                        'master_id' => $notifyUser->id,
                        'booking_key' => $bookingKey
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Failed to create notification for master', [
                        'master_id' => $notifyUser->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error creating notifications: ' . $e->getMessage());
            // Не прерываем выполнение, если уведомления не создались
        }

        return response()->json([
            'success' => true,
            'message' => __('messages.booking_successful') . ' ' . __('messages.we_will_contact_you'),
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
            
        \Log::info('Generating time slots', [
            'date' => $date,
            'userId' => $userId,
            'startTime' => $startTime,
            'endTime' => $endTime,
            'interval' => $interval,
            'serviceDuration' => $serviceDurationMinutes,
            'existingAppointments' => $existingAppointments->count()
        ]);

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
                    
                    // Проверяем интервал мастера только для записей, которые заканчиваются ДО начала текущего слота
                    // Если запись заканчивается до начала текущего слота, то проверяем интервал
                    if ($appointmentEnd <= $currentTime) {
                        $appointmentEndWithInterval = $appointmentEnd->copy()->addMinutes($interval);
                        if ($currentTime < $appointmentEndWithInterval) {
                            $isAvailable = false;
                            break;
                        }
                    }
                    
                                // Проверяем, не нарушит ли новая запись интервал мастера для записей, которые идут ПОСЛЕ
            // Если новая запись заканчивается слишком близко к началу существующей записи
            if ($appointmentStart >= $slotEnd) {
                $timeBetween = $appointmentStart->diffInMinutes($slotEnd);
                if ($timeBetween < $interval) {
                    $isAvailable = false;
                    break;
                }
            }
            
            // Проверяем, не будет ли новая запись слишком близко к существующей записи
            // Если существующая запись начинается слишком близко к концу новой записи
            if ($appointmentStart <= $slotEnd && $appointmentStart > $currentTime) {
                $timeBetween = $appointmentStart->diffInMinutes($slotEnd);
                if ($timeBetween < $interval) {
                    $isAvailable = false;
                    break;
                }
            }
                }

                if ($isAvailable) {
                    $slots[] = [
                        'time' => $currentTime->format('H:i'),
                        'available' => true
                    ];
                    \Log::info('Slot available', ['time' => $currentTime->format('H:i')]);
                } else {
                    \Log::info('Slot unavailable', ['time' => $currentTime->format('H:i')]);
                }
            }

            $currentTime->addMinutes($interval);
        }

        return $slots;
    }
}
