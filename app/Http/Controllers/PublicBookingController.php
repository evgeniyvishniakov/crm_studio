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
use Illuminate\Support\Facades\RateLimiter;

class PublicBookingController extends Controller
{
    public function __construct()
    {
        $this->middleware('web');
    }

    /**
     * Показать страницу бронирования
     */
    public function show($slug)
    {
        // Находим проект по slug
        $project = Project::whereHas('bookingSettings', function($query) {
            $query->where('booking_enabled', true);
        })->get()->first(function($project) use ($slug) {
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
        $services = $userServices->pluck('service')->filter()->unique('id')->values();
        
        // Получаем мастеров, у которых есть активные услуги
        $users = $userServices->pluck('user')->filter()->unique('id')->values();

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

        // Проверяем, не находится ли мастер в отпуске или больничном
        $timeOff = \App\Models\EmployeeTimeOff::where('project_id', $projectId)
            ->where('admin_user_id', $userId)
            ->whereIn('status', ['approved', 'pending']) // Учитываем как одобренные, так и ожидающие
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->first();

        if ($timeOff) {
            return response()->json([
                'success' => false,
                'message' => "Мастер в этот день в отпуске: {$timeOff->type_text}"
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

        $project = Project::whereHas('bookingSettings', function($query) {
            $query->where('booking_enabled', true);
        })->get()->first(function($project) use ($slug) {
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
     * Получить недоступные даты для мастера (отпуска, больничные и т.д.)
     */
    public function getUnavailableDates(Request $request, $slug)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer'
        ]);

        $project = Project::whereHas('bookingSettings', function($query) {
            $query->where('booking_enabled', true);
        })->get()->first(function($project) use ($slug) {
            return $project->slug === $slug;
        });

        if (!$project) {
            return response()->json([
                'success' => false,
                'message' => 'Проект не найден'
            ]);
        }

        $userId = $validated['user_id'];
        
        // Получаем все отпуска, больничные и другие нерабочие дни для мастера
        $timeOffs = \App\Models\EmployeeTimeOff::where('project_id', $project->id)
            ->where('admin_user_id', $userId)
            ->whereIn('status', ['approved', 'pending']) // Учитываем как одобренные, так и ожидающие
            ->where('end_date', '>=', Carbon::today()) // Только будущие и текущие
            ->get();
        
        $unavailableDates = [];
        foreach ($timeOffs as $timeOff) {
            $startDate = Carbon::parse($timeOff->start_date);
            $endDate = Carbon::parse($timeOff->end_date);
            
            // Добавляем все даты в диапазоне
            $currentDate = $startDate->copy();
            while ($currentDate->lte($endDate)) {
                $unavailableDates[] = [
                    'date' => $currentDate->format('Y-m-d'),
                    'type' => $timeOff->type,
                    'reason' => $timeOff->reason,
                    'type_text' => $timeOff->type_text
                ];
                $currentDate->addDay();
            }
        }

        return response()->json([
            'success' => true,
            'unavailable_dates' => $unavailableDates
        ]);
    }

    /**
     * Создать запись
     */
    public function store(Request $request)
    {
        try {
            // Rate limiting для защиты от спама
            $key = 'booking-store:' . request()->ip();
            if (RateLimiter::tooManyAttempts($key, 10)) { // 10 записей в минуту с одного IP
                return response()->json([
                    'success' => false,
                    'message' => 'Слишком много попыток записи. Попробуйте позже.'
                ], 429);
            }
            RateLimiter::hit($key);

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
                \Log::warning('PublicBooking store - попытка подмены данных', [
                    'project_id' => $validated['project_id'],
                    'user_project_id' => $user->project_id,
                    'service_project_id' => $service->project_id,
                    'ip' => request()->ip()
                ]);
                
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

            // Проверяем, не находится ли мастер в отпуске или больничном
            $timeOff = \App\Models\EmployeeTimeOff::where('project_id', $project->id)
                ->where('admin_user_id', $validated['user_id'])
                ->whereIn('status', ['approved', 'pending']) // Учитываем как одобренные, так и ожидающие
                ->where('start_date', '<=', $validated['date'])
                ->where('end_date', '>=', $validated['date'])
                ->first();

            if ($timeOff) {
                return response()->json([
                    'success' => false,
                    'message' => "Мастер в этот день в отпуске: {$timeOff->type_text}"
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
                            'project_id' => $project->id,
                            'appointment_id' => $appointment->id
                        ]);
                        
                        \Log::info('🔔 Уведомление создано с appointment_id', [
                            'notification_id' => $notification->id,
                            'appointment_id' => $notification->appointment_id,
                            'url' => $notification->url,
                            'type' => $notification->type
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

            // Отправляем уведомление в Telegram
            try {
                $appointmentData = [
                    'client_name' => $client->name,
                    'client_phone' => $client->phone,
                    'client_email' => $client->email,
                    'service_name' => $service->name,
                    'master_name' => $user->name,
                    'date' => $validated['date'],
                    'time' => $validated['time'],
                    'price' => $service->price,
                    'notes' => __('messages.booking_created_via_web')
                ];

                \App\Jobs\SendTelegramNotification::dispatch($appointmentData, $project->id);
                
                \Log::info('Telegram notification job dispatched', [
                    'project_id' => $project->id,
                    'appointment_id' => $appointment->id
                ]);
            } catch (\Exception $e) {
                \Log::error('Error dispatching Telegram notification: ' . $e->getMessage());
                // Не прерываем выполнение, если Telegram уведомление не отправилось
            }

            // Отправляем Email подтверждение клиенту
            try {
                if ($project->email_notifications_enabled && $client->email) {
                    $emailService = new \App\Services\EmailNotificationService();
                    $emailService->sendConfirmation($appointment);
                    
                    \Log::info('Email confirmation sent to client', [
                        'project_id' => $project->id,
                        'appointment_id' => $appointment->id,
                        'client_email' => $client->email
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error('Error sending email confirmation: ' . $e->getMessage());
                // Не прерываем выполнение, если Email не отправился
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
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->errors();
            $errorMessages = [];
            
            // Преобразуем ошибки в понятные сообщения
            foreach ($errors as $field => $messages) {
                switch ($field) {
                    case 'client_name':
                        $errorMessages[] = 'Имя содержит недопустимые символы. Используйте только буквы, пробелы, дефисы, точки и апострофы.';
                        break;
                    case 'client_phone':
                        $errorMessages[] = 'Номер телефона содержит недопустимые символы.';
                        break;
                    case 'client_email':
                        $errorMessages[] = 'Email адрес указан некорректно.';
                        break;
                    case 'date':
                        $errorMessages[] = 'Дата должна быть сегодня или позже.';
                        break;
                    case 'time':
                        $errorMessages[] = 'Время указано некорректно.';
                        break;
                    default:
                        $errorMessages[] = implode(', ', $messages);
                }
            }
            
            return response()->json([
                'success' => false,
                'message' => implode(' ', $errorMessages),
                'errors' => $errors
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error in store method: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => __('messages.booking_error')
            ], 500);
        }
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
        // Исключаем отмененные записи - они не должны блокировать время
        // Включаем дочерние записи для правильного расчета времени
        $existingAppointments = Appointment::where('user_id', $userId)
            ->where('date', $date)
            ->where('status', '!=', 'cancelled')
            ->with('childAppointments.service')
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
                    
                    // Рассчитываем общую длительность: основная запись + все дочерние
                    $totalAppointmentDuration = $appointment->duration ?? 60;
                    
                    // Добавляем длительность дочерних записей
                    if ($appointment->childAppointments) {
                        foreach ($appointment->childAppointments as $childAppointment) {
                            if ($childAppointment->service) {
                                $childDuration = $childAppointment->service->duration ?: 60;
                                $totalAppointmentDuration += $childDuration;
                            }
                        }
                    }
                    
                    $appointmentEnd = $appointmentStart->copy()->addMinutes($totalAppointmentDuration);
                    
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
