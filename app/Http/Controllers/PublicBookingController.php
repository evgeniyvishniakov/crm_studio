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
        $project = Project::where('project_name', 'like', '%' . str_replace('-', ' ', $slug) . '%')
            ->orWhere('project_name', 'like', '%' . str_replace('-', ' ', $slug) . '%')
            ->first();

        if (!$project || !$project->booking_enabled) {
            abort(404, 'Страница не найдена');
        }

        // Получаем настройки бронирования
        $bookingSettings = $project->getOrCreateBookingSettings();
        
        // Получаем услуги проекта
        $services = Service::where('project_id', $project->id)->get();
        
        // Получаем мастеров проекта
        $users = User::where('project_id', $project->id)->get();

        return view('public.booking.index', compact(
            'project',
            'bookingSettings',
            'services',
            'users'
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
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;
        $schedule = UserSchedule::where('user_id', $userId)
            ->where('day_of_week', $dayOfWeek)
            ->first();

        if (!$schedule || !$schedule->is_working) {
            return response()->json([
                'success' => false,
                'message' => 'Мастер не работает в этот день'
            ]);
        }

        // Генерируем слоты времени
        $slots = $this->generateTimeSlots(
            $schedule->start_time,
            $schedule->end_time,
            $bookingSettings->booking_interval,
            $date,
            $userId,
            $service->duration ?? 60
        );

        return response()->json([
            'success' => true,
            'slots' => $slots
        ]);
    }

    /**
     * Создать запись
     */
    public function store(Request $request)
    {
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
            'appointment_id' => $appointment->id
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
        $serviceDuration = Carbon::parse($serviceDuration)->format('H:i');

        while ($currentTime->lt($endTime)) {
            $slotEnd = $currentTime->copy()->addMinutes($serviceDuration);
            
            if ($slotEnd->lte($endTime)) {
                // Проверяем, нет ли уже записи в это время
                $existingAppointment = Appointment::where('user_id', $userId)
                    ->where('date', $date)
                    ->where('time', $currentTime->format('H:i'))
                    ->first();

                if (!$existingAppointment) {
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
