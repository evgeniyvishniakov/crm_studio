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
        $project = Project::with(['bookingSettings'])->findOrFail($user->project_id);
        
        // Получаем или создаем настройки бронирования
        $bookingSettings = $project->getOrCreateBookingSettings();
        
        // Получаем услуги проекта
        $services = Service::where('project_id', $user->project_id)->get();
        
        // Получаем мастеров проекта
        $users = User::where('project_id', $user->project_id)->get();
        
        // Получаем все услуги мастеров для веб-записи
        $userServices = \App\Models\Clients\UserService::whereHas('user', function($query) use ($user) {
            $query->where('project_id', $user->project_id);
        })->with(['user', 'service'])->get();
        
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
        $project = Project::findOrFail($user->project_id);
        
        $validated = $request->validate([
            'booking_enabled' => 'boolean',
            'booking_interval' => 'required|integer|min:15|max:120',
            'working_hours_start' => 'required|date_format:H:i',
            'working_hours_end' => 'required|date_format:H:i|after:working_hours_start',
            'advance_booking_days' => 'required|integer|min:1|max:365',
            'allow_same_day_booking' => 'boolean',
            'require_confirmation' => 'boolean',
            'booking_instructions' => 'nullable|string|max:1000',
        ]);

        // Если включаем онлайн-запись и ссылки еще нет - генерируем её
        $bookingUrl = null;
        if ($validated['booking_enabled'] && !$project->booking_url) {
            $bookingUrl = url('/book/' . $project->slug);
        }

        // Обновляем проект
        $project->update([
            'booking_enabled' => $validated['booking_enabled'],
            'booking_url' => $bookingUrl ?? $project->booking_url
        ]);

        // Обновляем или создаем настройки бронирования
        $bookingSettings = $project->getOrCreateBookingSettings();
        $bookingSettings->update($validated);

        // Обновляем проект из базы, чтобы получить актуальные данные
        $project->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Настройки бронирования обновлены',
            'booking_url' => $project->booking_url
        ]);
    }

    /**
     * Показать страницу расписания мастеров
     */
    public function schedules()
    {
        $user = Auth::user();
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
                'notes' => $schedule->notes
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
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Расписание сохранено'
        ]);
    }
}
