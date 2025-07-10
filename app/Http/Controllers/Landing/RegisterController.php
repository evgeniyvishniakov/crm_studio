<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Mail\RegistrationWelcomeMail;
use Illuminate\Support\Facades\Mail;
use App\Models\Admin\Project;
use Illuminate\Support\Facades\DB;

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

        // Ручная проверка на дубль телефона
        if (!empty($validated['phone'])) {
            $phoneExists = 
                \App\Models\Admin\Project::where('phone', $validated['phone'])->exists() ||
                \DB::table('admin_users')->where('phone', $validated['phone'])->exists();
            if ($phoneExists) {
                return back()->withErrors(['phone' => 'Пользователь с таким телефоном уже зарегистрирован'])->withInput();
            }
        }

        // Создание проекта и пользователя-админа в транзакции
        DB::transaction(function () use ($validated) {
            $project = Project::create([
                'name' => $validated['fullname'], // Имя
                'project_name' => $validated['salon'], // Название проекта
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'registered_at' => now(),
                'status' => 'active',
                'language' => 'ru', // или по умолчанию
            ]);

            // Создать пользователя-админа
            DB::table('admin_users')->insert([
                'name' => $validated['fullname'],
                'project_id' => $project->id,
                'role' => 'admin',
                'status' => 'active',
                'registered_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        // Отправка письма
        Mail::to($validated['email'])->send(new RegistrationWelcomeMail(
            $validated['email'],
            $validated['salon'],
            $validated['phone'] ?? null
        ));

        return back()->with('success', 'Регистрация отправлена!');
    }
}
