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

class RegisterController extends Controller
{
    public function store(Request $request)
    {
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
        ]);

        // Ручная проверка на дубль телефона только в projects
        if (!empty($validated['phone'])) {
            $phoneExists = 
                \App\Models\Admin\Project::where('phone', $validated['phone'])->exists();
            if ($phoneExists) {
                return back()->withErrors(['phone' => 'Пользователь с таким телефоном уже зарегистрирован'])->withInput();
            }
        }

        // Создание проекта и пользователя-админа в транзакции
        DB::transaction(function () use ($validated) {
            $project = Project::create([
                'name' => $validated['fullname'],
                'project_name' => $validated['salon'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'registered_at' => now(),
                'status' => 'active',
                'language' => 'ru',
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

            // Генерируем токен для создания пароля
            $token = Password::broker('admin_users')->createToken($admin);
            // Сохраняем токен для передачи в письмо
            $GLOBALS['reset_token'] = $token;
        });

        // Получаем токен из глобальной переменной
        $token = $GLOBALS['reset_token'] ?? null;

        // Отправка письма с ссылкой на создание пароля
        Mail::to($validated['email'])->send(new RegistrationWelcomeMail(
            $validated['email'],
            $validated['salon'],
            $validated['phone'] ?? null,
            $token
        ));

        return back()->with('success', 'Регистрация отправлена!');
    }
}
