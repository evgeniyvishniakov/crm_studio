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

        // Проверяем, существует ли уже такая связь
        $existingUserService = UserService::where('user_id', $request->user_id)
                                         ->where('service_id', $request->service_id)
                                         ->first();

        if ($existingUserService) {
            return response()->json([
                'success' => false,
                'message' => 'У мастера "' . $user->name . '" уже есть услуга "' . $service->name . '"'
            ], 422);
        }

        // Создаем новую связь
        $userService = UserService::create([
            'user_id' => $request->user_id,
            'service_id' => $request->service_id,
            'is_active_for_booking' => $request->boolean('is_active_for_booking', true),
            'price' => $request->price,
            'duration' => $request->duration,
            'description' => $request->description
        ]);

        // Загружаем связи для получения имен
        $userService->load(['user', 'service']);

        return response()->json([
            'success' => true,
            'message' => 'Услуга мастеру успешно добавлена',
            'userService' => [
                'id' => $userService->id,
                'user_name' => $userService->user->name,
                'service_name' => $userService->service->name,
                'price' => $userService->price,
                'duration' => $userService->duration,
                'service_price' => $userService->service->price,
                'service_duration' => $userService->service->duration,
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
                'price' => $userService->price,
                'duration' => $userService->duration,
                'service_price' => $userService->service->price,
                'service_duration' => $userService->service->duration,
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
        
        if ($userId === 'all') {
            // Получаем все услуги мастеров для проекта
            $userServices = UserService::whereHas('user', function($query) use ($project) {
                $query->where('project_id', $project->id);
            })->with(['user', 'service'])->get();
            
            return response()->json([
                'success' => true,
                'userServices' => $userServices->map(function($us) {
                    return [
                        'id' => $us->id,
                        'user_id' => $us->user_id,
                        'service_id' => $us->service_id,
                        'user_name' => $us->user->name,
                        'service_name' => $us->service->name,
                        'user_role' => $us->user->role,
                        'price' => $us->price,
                        'duration' => $us->duration,
                        'is_active_for_booking' => $us->is_active_for_booking
                    ];
                })
            ]);
        }
        
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
     * Получить конкретную услугу мастера по ID
     */
    public function show($id)
    {
        $project = Auth::user()->project;
        $userService = UserService::whereHas('user', function($query) use ($project) {
            $query->where('project_id', $project->id);
        })->with(['user', 'service'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'userServices' => [[
                'id' => $userService->id,
                'user_id' => $userService->user_id,
                'service_id' => $userService->service_id,
                'user_name' => $userService->user->name,
                'service_name' => $userService->service->name,
                'user_role' => $userService->user->role,
                'price' => $userService->price,
                'duration' => $userService->duration,
                'description' => $userService->description,
                'is_active_for_booking' => $userService->is_active_for_booking
            ]]
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