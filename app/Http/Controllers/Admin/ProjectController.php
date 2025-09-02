<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Project;
use Illuminate\Support\Facades\Storage;
use App\Models\Subscription;
use App\Models\Admin\User;
use App\Mail\RegistrationWelcomeMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;
use App\Models\Language;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = \App\Models\Admin\Project::orderByDesc('id');
        if (request()->filled('status')) {
            $query->where('status', request('status'));
        }
        if (request()->filled('q')) {
            $q = request('q');
            $query->where(function($sub) use ($q) {
                $sub->where('name', 'like', "%$q%")
                    ->orWhere('email', 'like', "%$q%") ;
            });
        }
        $projects = $query->get();
        if (request()->ajax()) {
            return view('admin.project._table', compact('projects'))->render();
        }
        return view('admin.project.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'logo' => 'nullable|image|max:2048',
            'name' => 'required|string|max:255', // Имя
            'project_name' => 'required|string|max:255', // Название проекта
            'email' => 'required|email|max:255',
            'registered_at' => 'required|date',
            'language' => 'required|string|max:10',
            'currency_id' => 'required|exists:currencies,id',
            'status' => 'required|string|max:20',
            'phone' => 'nullable|string|max:50',
            'website' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'map_latitude' => 'nullable|string|max:20',
            'map_longitude' => 'nullable|string|max:20',
            'map_zoom' => 'nullable|integer|min:1|max:20',
            'about' => 'nullable|string|max:1000',
            'social_links' => 'nullable|string',
        ], [
            'map_zoom.integer' => __('messages.map_zoom_invalid'),
            'map_zoom.min' => __('messages.map_zoom_invalid'),
            'map_zoom.max' => __('messages.map_zoom_invalid'),
            'about.max' => __('messages.about_max_length'),
        ]);

        // Обработка загрузки логотипа
        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('projects/logos', 'public');
        }

        // Преобразование соцсетей в массив
        if (!empty($validated['social_links'])) {
            $validated['social_links'] = array_map('trim', explode(',', $validated['social_links']));
        }

        // Найти language_id по коду языка
        $language = Language::where('code', $validated['language'])->first();
        if ($language) {
            $validated['language_id'] = $language->id;
            $validated['booking_language_id'] = $language->id;
        }
        unset($validated['language']); // Убираем поле language, оставляем только language_id

        $project = Project::create($validated);

        // Создать пользователя-админа для проекта
        $adminUser = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => null,
            'project_id' => $project->id,
            'role' => 'admin',
            'status' => 'active',
            'registered_at' => now(),
        ]);

        // Создать пробную подписку для нового проекта
        Subscription::create([
            'project_id' => $project->id,
            'admin_user_id' => $adminUser->id,
            'plan_type' => 'trial',
            'amount' => 0.00,
            'currency' => 'USD',
            'paid_at' => now(),
            'starts_at' => now(),
            'trial_ends_at' => now()->addDays(7), // Пробный период 7 дней
            'expires_at' => null, // Для пробной подписки не устанавливаем дату окончания
            'status' => 'trial',
            'notes' => 'Автоматически создана при создании проекта через админку'
        ]);

        // Генерируем токен для создания пароля
        $token = Password::broker('admin_users')->createToken($adminUser);

        // Отправка письма с ссылкой на создание пароля
        Log::info('Attempting to send registration email to: ' . $validated['email']);
        try {
            Mail::to($validated['email'])->send(new RegistrationWelcomeMail(
                $validated['email'],
                $validated['project_name'],
                $validated['phone'] ?? null,
                $token
            ));
            Log::info('Registration email sent successfully to: ' . $validated['email']);
        } catch (\Exception $e) {
            Log::error('Failed to send registration email: ' . $e->getMessage());
        }

        // Рассылка уведомлений всем админам, кроме создателя
        $currentAdmin = auth()->user();
        $adminUsers = \App\Models\Admin\User::where('role', 'admin')->where('id', '!=', $currentAdmin->id)->get();
        foreach ($adminUsers as $adminUser) {
            \App\Models\Notification::create([
                'user_id' => $adminUser->id,
                'type' => 'project',
                'title' => 'Создан новый проект',
                'body' => 'Создан новый проект: ' . $validated['project_name'],
                'url' => route('admin.projects.index'),
            ]);
        }

        return redirect()->route('admin.projects.index')->with('success', __('messages.project_successfully_created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $project = Project::findOrFail($id);

        $validated = $request->validate([
            'logo' => 'nullable|image|max:2048',
            'name' => 'required|string|max:255', // Имя
            'project_name' => 'required|string|max:255', // Название проекта
            'email' => 'required|email|max:255',
            'registered_at' => 'required|date',
            'language' => 'required|string|max:10',
            'currency_id' => 'required|exists:currencies,id',
            'status' => 'required|string|max:20',
            'phone' => 'nullable|string|max:50',
            'website' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'map_latitude' => 'nullable|string|max:20',
            'map_longitude' => 'nullable|string|max:20',
            'map_zoom' => 'nullable|integer|min:1|max:20',
            'about' => 'nullable|string|max:1000',
            'social_links' => 'nullable|string',
        ], [
            'map_zoom.integer' => __('messages.map_zoom_invalid'),
            'map_zoom.min' => __('messages.map_zoom_invalid'),
            'map_zoom.max' => __('messages.map_zoom_invalid'),
            'about.max' => __('messages.about_max_length'),
        ]);

        // Обработка загрузки логотипа
        if ($request->hasFile('logo')) {
            // Удалить старый логотип, если есть
            if ($project->logo) {
                Storage::disk('public')->delete($project->logo);
            }
            $validated['logo'] = $request->file('logo')->store('projects/logos', 'public');
        }

        // Преобразование соцсетей в массив
        if (!empty($validated['social_links'])) {
            $validated['social_links'] = array_map('trim', explode(',', $validated['social_links']));
        }

        // Найти language_id по коду языка
        $language = Language::where('code', $validated['language'])->first();
        if ($language) {
            $validated['language_id'] = $language->id;
            $validated['booking_language_id'] = $language->id;
        }
        unset($validated['language']); // Убираем поле language, оставляем только language_id

        $project->update($validated);

        return redirect()->route('admin.projects.index')->with('success', __('messages.project_successfully_updated'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $project = Project::findOrFail($id);
            
            // Удаляем логотип проекта, если он есть
            if ($project->logo) {
                Storage::disk('public')->delete($project->logo);
            }
            
            // Удаляем связанные данные в правильном порядке
            // 1. Удаляем подписки
            $project->subscriptions()->delete();
            
            // 2. Удаляем администраторов проекта
            $project->adminUsers()->delete();
            
            // 3. Удаляем клиентов проекта
            $project->clients()->delete();
            
            // 4. Удаляем записи проекта
            $project->appointments()->delete();
            
            // 5. Удаляем настройки проекта
            $project->bookingSettings()->delete();
            $project->widgetSettings()->delete();
            $project->emailSettings()->delete();
            $project->telegramSettings()->delete();
            
            // 6. Удаляем сам проект
            $project->delete();
            
            return response()->json(['success' => true, 'message' => 'Проект успешно удален']);
            
        } catch (\Exception $e) {
            Log::error('Ошибка при удалении проекта: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Ошибка при удалении проекта'], 500);
        }
    }
}
