<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Admin\User;
use App\Models\Clients\Service;
use App\Models\Clients\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserServicesController extends Controller
{
    /**
     * Показать страницу управления услугами мастеров
     */
    public function index()
    {
        $project = Auth::user()->project;
        $users = User::where('project_id', $project->id)->get();
        $services = Service::where('project_id', $project->id)->get();
        
        // Получаем все связи мастер-услуга
        $userServices = UserService::whereHas('user', function($query) use ($project) {
            $query->where('project_id', $project->id);
        })->with(['user', 'service'])->get();
        
        return view('client.user-services.index', compact('users', 'services', 'userServices'));
    }

    /**
     * Сохранить связь мастера с услугой
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:admin_users,id',
            'service_id' => 'required|exists:services,id',
            'is_active_for_booking' => 'boolean',
            'price' => 'nullable|numeric|min:0',
            'duration' => 'nullable|integer|min:1',
            'description' => 'nullable|string|max:1000'
        ]);

        // Проверяем, что пользователь и услуга принадлежат текущему проекту
        $project = Auth::user()->project;
        $user = User::where('id', $request->user_id)
                   ->where('project_id', $project->id)
                   ->firstOrFail();
        
        $service = Service::where('id', $request->service_id)
                         ->where('project_id', $project->id)
                         ->firstOrFail();

        // Создаем или обновляем связь
        $userService = UserService::updateOrCreate(
            [
                'user_id' => $request->user_id,
                'service_id' => $request->service_id
            ],
            [
                'is_active_for_booking' => $request->boolean('is_active_for_booking', true),
                'price' => $request->price,
                'duration' => $request->duration,
                'description' => $request->description
            ]
        );

        // Загружаем связи для получения имен
        $userService->load(['user', 'service']);

        return response()->json([
            'success' => true,
            'message' => 'Услуга мастеру успешно добавлена',
            'userService' => [
                'id' => $userService->id,
                'user_name' => $userService->user->name,
                'service_name' => $userService->service->name,
                'active_price' => $userService->active_price,
                'active_duration' => $userService->active_duration,
                'is_active_for_booking' => $userService->is_active_for_booking
            ]
        ]);
    }

    /**
     * Обновить связь мастера с услугой
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'is_active_for_booking' => 'boolean',
            'price' => 'nullable|numeric|min:0',
            'duration' => 'nullable|integer|min:1',
            'description' => 'nullable|string|max:1000'
        ]);

        $project = Auth::user()->project;
        $userService = UserService::whereHas('user', function($query) use ($project) {
            $query->where('project_id', $project->id);
        })->findOrFail($id);

        $userService->update([
            'is_active_for_booking' => $request->boolean('is_active_for_booking', true),
            'price' => $request->price,
            'duration' => $request->duration,
            'description' => $request->description
        ]);

        // Загружаем связи для получения имен
        $userService->load(['user', 'service']);

        return response()->json([
            'success' => true,
            'message' => 'Услуга мастеру успешно обновлена',
            'userService' => [
                'id' => $userService->id,
                'user_name' => $userService->user->name,
                'service_name' => $userService->service->name,
                'active_price' => $userService->active_price,
                'active_duration' => $userService->active_duration,
                'is_active_for_booking' => $userService->is_active_for_booking
            ]
        ]);
    }

    /**
     * Удалить связь мастера с услугой
     */
    public function destroy($id)
    {
        $project = Auth::user()->project;
        $userService = UserService::whereHas('user', function($query) use ($project) {
            $query->where('project_id', $project->id);
        })->findOrFail($id);

        $userService->delete();

        return response()->json([
            'success' => true,
            'message' => 'Связь мастера с услугой удалена'
        ]);
    }

    /**
     * Получить услуги мастера
     */
    public function getUserServices($userId)
    {
        $project = Auth::user()->project;
        $user = User::where('id', $userId)
                   ->where('project_id', $project->id)
                   ->firstOrFail();

        $userServices = $user->userServices()->with('service')->get();

        return response()->json([
            'success' => true,
            'userServices' => $userServices
        ]);
    }

    /**
     * Получить мастеров услуги
     */
    public function getServiceMasters($serviceId)
    {
        $project = Auth::user()->project;
        $service = Service::where('id', $serviceId)
                         ->where('project_id', $project->id)
                         ->firstOrFail();

        $masters = $service->masters()->get();

        return response()->json([
            'success' => true,
            'masters' => $masters
        ]);
    }
} 