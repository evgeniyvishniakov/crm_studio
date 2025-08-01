<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Clients\Service;
use Illuminate\Validation\Rule;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $currentProjectId = auth()->user()->project_id;
        $query = Service::where('project_id', $currentProjectId)->orderBy('created_at', 'desc');

        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where('name', 'like', "%$search%");
        }

        if ($request->ajax()) {
            $services = $query->paginate(11);
            return response()->json([
                'data' => $services->items(),
                'meta' => [
                    'current_page' => $services->currentPage(),
                    'last_page' => $services->lastPage(),
                    'per_page' => $services->perPage(),
                    'total' => $services->total(),
                ],
            ]);
        }

        $services = $query->paginate(11);
        return view('client.services.list', compact('services'));
    }

    public function store(Request $request)
    {
        $currentProjectId = auth()->user()->project_id;
        try {
            $validated = $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('services', 'name')->where(function ($query) use ($currentProjectId) {
                        return $query->where('project_id', $currentProjectId);
                    }),
                ],
                'price' => 'nullable|numeric|min:0',
                'duration_hours' => 'nullable|integer|min:0|max:12',
                'duration_minutes' => 'nullable|integer|min:0|max:59',
            ], [
                'name.required' => 'Поле "Название" обязательно для заполнения.',
                'name.unique' => 'Услуга с таким названием уже существует.',
                'price.numeric' => 'Цена должна быть числом.',
                'price.min' => 'Цена не может быть отрицательной.'
            ]);
            $hours = (int)($request->input('duration_hours', 0));
            $minutes = (int)($request->input('duration_minutes', 0));
            $duration = $hours * 60 + $minutes;
            $service = Service::create([
                'name' => $validated['name'],
                'price' => $validated['price'] ?? null,
                'duration' => $duration,
                'project_id' => $currentProjectId,
            ]);
            return response()->json([
                'success' => true,
                'service' => $service
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \App\Models\SystemLog::create([
                'level' => 'error',
                'module' => 'ServiceController@store',
                'user_email' => auth()->user()->email ?? null,
                'user_id' => auth()->id(),
                'ip' => request()->ip(),
                'action' => 'create_service',
                'message' => $e->getMessage(),
                'context' => json_encode([
                    'trace' => $e->getTraceAsString(),
                    'input' => request()->except(['password', 'password_confirmation']),
                ]),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Внутренняя ошибка сервера. Пожалуйста, попробуйте позже.'
            ], 500);
        }
    }

    public function destroy(Service $service)
    {
        $currentProjectId = auth()->user()->project_id;
        if ($service->project_id !== $currentProjectId) {
            return response()->json(['success' => false, 'message' => 'Нет доступа к услуге'], 403);
        }
        try {
            $service->delete();
            return response()->json([
                'success' => true,
                'message' => 'Услуга успешно удалена'
            ]);
        } catch (\Exception $e) {
            \App\Models\SystemLog::create([
                'level' => 'error',
                'module' => 'ServiceController@destroy',
                'user_email' => auth()->user()->email ?? null,
                'user_id' => auth()->id(),
                'ip' => request()->ip(),
                'action' => 'delete_service',
                'message' => $e->getMessage(),
                'context' => json_encode([
                    'trace' => $e->getTraceAsString(),
                    'input' => request()->except(['password', 'password_confirmation']),
                ]),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Внутренняя ошибка сервера. Пожалуйста, попробуйте позже.'
            ], 500);
        }
    }

    public function edit(Service $service)
    {
        return response()->json($service);
    }

    public function update(Request $request, Service $service)
    {
        $currentProjectId = auth()->user()->project_id;
        try {
            $validated = $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('services', 'name')->where(function ($query) use ($currentProjectId) {
                        return $query->where('project_id', $currentProjectId);
                    })->ignore($service->id),
                ],
                'price' => 'nullable|numeric|min:0',
                'duration_hours' => 'nullable|integer|min:0|max:12',
                'duration_minutes' => 'nullable|integer|min:0|max:59',
            ], [
                'name.required' => 'Поле "Название" обязательно для заполнения.',
                'name.unique' => 'Услуга с таким названием уже существует.',
                'price.numeric' => 'Цена должна быть числом.',
                'price.min' => 'Цена не может быть отрицательной.'
            ]);
            if ($service->project_id !== $currentProjectId) {
                return response()->json(['success' => false, 'message' => 'Нет доступа к услуге'], 403);
            }
            $hours = (int)($request->input('duration_hours', 0));
            $minutes = (int)($request->input('duration_minutes', 0));
            $duration = $hours * 60 + $minutes;
            $service->update([
                'name' => $validated['name'],
                'price' => $validated['price'] ?? null,
                'duration' => $duration,
            ]);
            return response()->json([
                'success' => true,
                'service' => $service
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \App\Models\SystemLog::create([
                'level' => 'error',
                'module' => 'ServiceController@update',
                'user_email' => auth()->user()->email ?? null,
                'user_id' => auth()->id(),
                'ip' => request()->ip(),
                'action' => 'update_service',
                'message' => $e->getMessage(),
                'context' => json_encode([
                    'trace' => $e->getTraceAsString(),
                    'input' => request()->except(['password', 'password_confirmation']),
                ]),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Внутренняя ошибка сервера. Пожалуйста, попробуйте позже.'
            ], 500);
        }
    }
}
