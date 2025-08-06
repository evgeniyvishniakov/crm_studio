<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Admin\Project;
use App\Models\Clients\BookingSetting;
use App\Models\Clients\UserSchedule;
use App\Models\Clients\Service;
use App\Models\Admin\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingManagementController extends Controller
{
    /**
     * Показать страницу настроек бронирования
     */
    public function index()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        $project = Project::with(['bookingSettings'])->findOrFail($user->project_id);
        
        // Получаем или создаем настройки бронирования
        $bookingSettings = $project->getOrCreateBookingSettings();
        
        // Получаем услуги проекта
        $services = Service::where('project_id', $user->project_id)->get();
        
        // Получаем мастеров проекта
        $users = User::where('project_id', $user->project_id)->get();
        
        // Получаем все услуги мастеров для веб-записи (только с валидными связями)
        $userServices = \App\Models\Clients\UserService::whereHas('user', function($query) use ($user) {
            $query->where('project_id', $user->project_id);
        })->whereHas('service', function($query) use ($user) {
            $query->where('project_id', $user->project_id);
        })->with(['user', 'service'])->get();
        
        \Log::info('BookingManagementController::index - Загруженные userServices:', [
            'count' => $userServices->count(),
            'data' => $userServices->map(function($us) {
                return [
                    'id' => $us->id,
                    'user_id' => $us->user_id,
                    'service_id' => $us->service_id,
                    'price' => $us->price,
                    'duration' => $us->duration,
                    'is_active_for_booking' => $us->is_active_for_booking,
                    'user_name' => $us->user ? $us->user->name : 'null',
                    'service_name' => $us->service ? $us->service->name : 'null'
                ];
            })->toArray()
        ]);
        
        // Получаем расписание мастеров
        $userSchedules = UserSchedule::whereIn('user_id', $users->pluck('id'))
            ->with('user')
            ->get()
            ->groupBy('user_id');

        return view('client.booking.index', compact(
            'project',
            'bookingSettings',
            'services',
            'users',
            'userSchedules',
            'userServices'
        ));
    }

    /**
     * Обновить настройки бронирования
     */
    public function updateSettings(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Пользователь не авторизован'], 401);
        }
        
        $project = Project::findOrFail($user->project_id);
        
        $validated = $request->validate([
            'booking_enabled' => 'boolean',
            'working_hours_start' => 'required|date_format:H:i',
            'working_hours_end' => 'required|date_format:H:i|after:working_hours_start',
            'advance_booking_days' => 'required|integer|min:1|max:365',
            'allow_same_day_booking' => 'boolean',
            'require_confirmation' => 'boolean',
        ], [
            'working_hours_start.required' => __('messages.working_hours_start_required'),
            'working_hours_start.date_format' => __('messages.working_hours_start_invalid_format'),
            'working_hours_end.required' => __('messages.working_hours_end_required'),
            'working_hours_end.date_format' => __('messages.working_hours_end_invalid_format'),
            'working_hours_end.after' => __('messages.working_hours_end_must_be_after_start'),
            'advance_booking_days.required' => __('messages.advance_booking_days_required'),
            'advance_booking_days.integer' => __('messages.advance_booking_days_must_be_integer'),
            'advance_booking_days.min' => __('messages.advance_booking_days_min_value'),
            'advance_booking_days.max' => __('messages.advance_booking_days_max_value'),
        ]);

        // Получаем или создаем настройки бронирования
        $bookingSettings = $project->getOrCreateBookingSettings();
        
        // Обрабатываем boolean поля, которые могут отсутствовать в запросе
        $bookingEnabled = $validated['booking_enabled'] ?? false;
        $allowSameDayBooking = $validated['allow_same_day_booking'] ?? false;
        $requireConfirmation = $validated['require_confirmation'] ?? false;
        
        // Если включаем онлайн-запись и ссылки еще нет - генерируем её
        $bookingUrl = null;
        if ($bookingEnabled && !$bookingSettings->booking_url) {
            $bookingUrl = url('/book/' . $project->slug);
        }

        // Обновляем настройки бронирования
        $bookingSettings->update([
            'booking_enabled' => $bookingEnabled,
            'booking_url' => $bookingUrl ?? $bookingSettings->booking_url,
            'working_hours_start' => $validated['working_hours_start'],
            'working_hours_end' => $validated['working_hours_end'],
            'advance_booking_days' => $validated['advance_booking_days'],
            'allow_same_day_booking' => $allowSameDayBooking,
            'require_confirmation' => $requireConfirmation,
        ]);

        // Обновляем проект из базы, чтобы получить актуальные данные
        $project->refresh();

        return response()->json([
            'success' => true,
            'message' => __('messages.changes_successfully_saved'),
            'booking_url' => $project->booking_url
        ]);
    }

    /**
     * Показать страницу расписания мастеров
     */
    public function schedules()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        $users = User::where('project_id', $user->project_id)->get();
        
        $userSchedules = UserSchedule::whereIn('user_id', $users->pluck('id'))
            ->with('user')
            ->get()
            ->groupBy('user_id');

        return view('client.booking.schedules', compact('users', 'userSchedules'));
    }

    /**
     * Получить расписание пользователя
     */
    public function getUserSchedule(Request $request)
    {
        $userId = $request->input('user_id');
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Пользователь не авторизован'], 401);
        }
        
        // Проверяем, что пользователь принадлежит тому же проекту
        $targetUser = User::where('id', $userId)
            ->where('project_id', $user->project_id)
            ->firstOrFail();

        $schedules = UserSchedule::where('user_id', $userId)
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
     * Сохранить расписание пользователя
     */
    public function saveUserSchedule(Request $request)
    {
        $user = Auth::user();
        $userId = $request->input('user_id');
        
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Пользователь не авторизован'], 401);
        }
        
        // Проверяем, что пользователь принадлежит тому же проекту
        $targetUser = User::where('id', $userId)
            ->where('project_id', $user->project_id)
            ->firstOrFail();

        $scheduleData = $request->input('schedule', []);
        
        // Удаляем старые записи
        UserSchedule::where('user_id', $userId)->delete();

        // Создаем новые записи
        foreach ($scheduleData as $dayOfWeek => $schedule) {
            if (isset($schedule['is_working']) && $schedule['is_working']) {
                UserSchedule::create([
                    'user_id' => $userId,
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
}
