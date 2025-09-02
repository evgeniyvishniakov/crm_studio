<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Mail\RegistrationWelcomeMail;
use Illuminate\Support\Facades\Mail;
use App\Models\Admin\Project;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use App\Models\Admin\User;
use App\Models\Subscription;

class RegisterController extends Controller
{
    public function store(Request $request)
    {
        \Log::info('=== REGISTRATION START ===');
        \Log::info('Request data: ' . json_encode($request->all()));
        
        try {
            $validated = $request->validate([
                'fullname' => 'required|string|max:255',
                'email' => [
                    'required',
                    'email',
                    'max:255',
                    Rule::unique('projects', 'email'),
                ],
                'phone' => [
                    'nullable',
                    'max:32',
                ],
                'salon' => 'required|string|max:255',
                'language' => 'nullable|string|in:ru,en,ua',
                // password убран
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }

        // Ручная проверка на дубль телефона только в projects
        if (!empty($validated['phone'])) {
            $phoneExists = 
                \App\Models\Admin\Project::where('phone', $validated['phone'])->exists();
            if ($phoneExists) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['phone' => ['Пользователь с таким телефоном уже зарегистрирован']]
                    ], 422);
                }
                return back()->withErrors(['phone' => 'Пользователь с таким телефоном уже зарегистрирован'])->withInput();
            }
        }

        // Создание проекта и пользователя-админа в транзакции
        DB::transaction(function () use ($validated) {
            // Определяем язык для проекта
            $languageCode = $validated['language'] ?? 'ru';
            $language = \App\Models\Language::where('code', $languageCode)->first();
            
            $project = Project::create([
                'name' => $validated['fullname'],
                'project_name' => $validated['salon'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'registered_at' => now(),
                'status' => 'active',
                'language_id' => $language ? $language->id : null,
                'booking_language_id' => $language ? $language->id : null,
            ]);

            // Создать пользователя-админа
            $admin = User::create([
                'name' => $validated['fullname'],
                'email' => $validated['email'],
                'password' => null,
                'project_id' => $project->id,
                'role' => 'admin',
                'status' => 'active',
                'registered_at' => now(),
            ]);

            // Создать пробную подписку
            Subscription::create([
                'project_id' => $project->id,
                'admin_user_id' => $admin->id,
                'plan_type' => 'trial',
                'amount' => 0.00,
                'currency' => 'USD',
                'paid_at' => null, // Для пробной подписки нет оплаты
                'starts_at' => null, // Дата начала устанавливается только после оплаты
                'trial_ends_at' => now()->addDays(7), // Пробный период 7 дней
                'expires_at' => null, // Для пробной подписки не устанавливаем дату окончания
                'status' => 'trial',
                'payment_status' => 'no_payment', // Статус оплаты для пробной подписки
                'notes' => 'Автоматически создана при регистрации проекта'
            ]);

            // Рассылка уведомлений всем админам, кроме нового
            $adminUsers = User::where('role', 'admin')
                ->where('id', '!=', $admin->id)
                ->get();
            foreach ($adminUsers as $adminUser) {
                \App\Models\Notification::create([
                    'user_id' => $adminUser->id,
                    'type' => 'project',
                    'title' => 'Зарегистрирован новый проект',
                    'body' => 'Зарегистрирован новый проект: ' . $validated['salon'],
                    'url' => route('admin.projects.index'),
                ]);
            }

            // Генерируем токен для создания пароля
            $token = Password::broker('admin_users')->createToken($admin);
            // Сохраняем токен для передачи в письмо
            $GLOBALS['reset_token'] = $token;
        });

        // Получаем токен из глобальной переменной
        $token = $GLOBALS['reset_token'] ?? null;

        // Отправка письма с ссылкой на создание пароля
        \Log::info('Attempting to send registration email to: ' . $validated['email']);
        try {
            Mail::to($validated['email'])->send(new RegistrationWelcomeMail(
                $validated['email'],
                $validated['salon'],
                $validated['phone'] ?? null,
                $token
            ));
            \Log::info('Registration email sent successfully to: ' . $validated['email']);
        } catch (\Exception $e) {
            \Log::error('Failed to send registration email: ' . $e->getMessage());
        }

        // Возврат JSON-ответа для AJAX
        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Регистрация отправлена!');
    }
}
