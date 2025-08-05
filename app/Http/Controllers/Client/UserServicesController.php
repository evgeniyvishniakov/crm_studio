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
                'user_name' => $userService->user ? $userService->user->name : 'Удаленный пользователь',
                'user_email' => $userService->user ? $userService->user->email : '',
                'service_name' => $userService->service ? $userService->service->name : 'Удаленная услуга',
                'service_description' => $userService->service ? $userService->service->description : '',
                'price' => $userService->price ?? ($userService->service ? $userService->service->price : 0),
                'duration' => $userService->duration ?? ($userService->service ? $userService->service->duration : null),
                'service_price' => $userService->service ? $userService->service->price : 0,
                'service_duration' => $userService->service ? $userService->service->duration : null,
                'is_custom_price' => $userService->price !== null,
                'is_custom_duration' => $userService->duration !== null,
                'is_active_for_booking' => $userService->is_active_for_booking
            ]
        ]);
    }

    /**
     * Обновить связь мастера с услугой
     */
    public function update(Request $request, $id)
    {
        \Log::info('UserServicesController::update - Входящие данные:', [
            'id' => $id,
            'request_data' => $request->all()
        ]);

        $request->validate([
            'user_id' => 'required|exists:admin_users,id',
            'service_id' => 'required|exists:services,id',
            'is_active_for_booking' => 'boolean',
            'price' => 'nullable|numeric|min:0',
            'duration' => 'nullable|integer|min:1',
            'description' => 'nullable|string|max:1000'
        ]);

        $project = Auth::user()->project;
        
        // Проверяем, что новый пользователь и услуга принадлежат текущему проекту
        $user = User::where('id', $request->user_id)
                   ->where('project_id', $project->id)
                   ->firstOrFail();
        
        $service = Service::where('id', $request->service_id)
                         ->where('project_id', $project->id)
                         ->firstOrFail();
        $userService = UserService::whereHas('user', function($query) use ($project) {
            $query->where('project_id', $project->id);
        })->find($id);

        if (!$userService) {
            return response()->json([
                'success' => false,
                'message' => 'Запись не найдена'
            ], 404);
        }

        \Log::info('UserServicesController::update - Найдена запись до обновления:', [
            'userService' => $userService->toArray()
        ]);
        
        // Проверяем, существует ли уже такая связь (кроме текущей записи)
        $existingUserService = UserService::where('user_id', $request->user_id)
                                         ->where('service_id', $request->service_id)
                                         ->where('id', '!=', $id)
                                         ->first();

        if ($existingUserService) {
            return response()->json([
                'success' => false,
                'message' => 'У мастера "' . $user->name . '" уже есть услуга "' . $service->name . '"'
            ], 422);
        }

        $updateData = [
            'user_id' => $request->user_id,
            'service_id' => $request->service_id,
            'is_active_for_booking' => $request->boolean('is_active_for_booking', true),
            'price' => $request->price,
            'duration' => $request->duration,
            'description' => $request->description
        ];

        \Log::info('UserServicesController::update - Данные для обновления:', $updateData);

        $userService->update($updateData);

        // Загружаем связи для получения имен
        $userService->load(['user', 'service']);

        \Log::info('UserServicesController::update - Запись после обновления:', [
            'userService' => $userService->toArray()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Услуга мастеру успешно обновлена',
            'userService' => [
                'id' => $userService->id,
                'user_name' => $userService->user ? $userService->user->name : 'Удаленный пользователь',
                'user_email' => $userService->user ? $userService->user->email : '',
                'service_name' => $userService->service ? $userService->service->name : 'Удаленная услуга',
                'service_description' => $userService->service ? $userService->service->description : '',
                'price' => $userService->price ?? ($userService->service ? $userService->service->price : 0),
                'duration' => $userService->duration ?? ($userService->service ? $userService->service->duration : null),
                'service_price' => $userService->service ? $userService->service->price : 0,
                'service_duration' => $userService->service ? $userService->service->duration : null,
                'is_custom_price' => $userService->price !== null,
                'is_custom_duration' => $userService->duration !== null,
                'is_active_for_booking' => $userService->is_active_for_booking
            ]
        ]);
    }

    /**
     * Удалить связь мастера с услугой
     */
    public function destroy($id)
    {
        try {
            $project = Auth::user()->project;
            $userService = UserService::whereHas('user', function($query) use ($project) {
                $query->where('project_id', $project->id);
            })->find($id);

            if (!$userService) {
                \Log::warning('UserServicesController::destroy - Запись не найдена', ['id' => $id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Запись не найдена или уже удалена'
                ], 404);
            }

            $userService->delete();
            \Log::info('UserServicesController::destroy - Запись удалена', ['id' => $id]);

            return response()->json([
                'success' => true,
                'message' => 'Связь мастера с услугой удалена'
            ]);
        } catch (\Exception $e) {
            \Log::error('Ошибка при удалении UserService:', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при удалении записи'
            ], 500);
        }
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
                        'user_name' => $us->user ? $us->user->name : 'Удаленный пользователь',
                        'service_name' => $us->service ? $us->service->name : 'Удаленная услуга',
                        'user_role' => $us->user ? ($us->user->role ?? 'user') : 'user',
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
        try {
            $project = Auth::user()->project;
            $userService = UserService::whereHas('user', function($query) use ($project) {
                $query->where('project_id', $project->id);
            })->with(['user', 'service'])->find($id);

            if (!$userService) {
                return response()->json([
                    'success' => false,
                    'message' => 'Запись не найдена'
                ], 404);
            }

            \Log::info('UserServicesController::show - Данные записи:', [
                'id' => $id,
                'userService' => $userService->toArray()
            ]);

            $responseData = [
                'id' => $userService->id,
                'user_id' => $userService->user_id,
                'service_id' => $userService->service_id,
                'user_name' => $userService->user ? $userService->user->name : 'Удаленный пользователь',
                'service_name' => $userService->service ? $userService->service->name : 'Удаленная услуга',
                'user_role' => $userService->user ? ($userService->user->role ?? 'user') : 'user',
                'price' => $userService->price,
                'duration' => $userService->duration,
                'description' => $userService->description,
                'is_active_for_booking' => $userService->is_active_for_booking
            ];

            \Log::info('UserServicesController::show - Отправляемые данные:', $responseData);

            return response()->json([
                'success' => true,
                'userServices' => [$responseData]
            ]);
        } catch (\Exception $e) {
            \Log::error('Ошибка при получении UserService:', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при получении данных'
            ], 500);
        }
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