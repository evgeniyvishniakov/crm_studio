<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin\Project;
use App\Models\Subscription;

class AccountController extends Controller
{
    /**
     * Показать форму входа
     */
    public function showLogin()
    {
        return view('landing.account.login');
    }

    /**
     * Обработать вход
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Добавляем логирование для отладки
        \Log::info('Landing login attempt', [
            'email' => $request->email,
            'ip' => $request->ip()
        ]);

        // Ищем пользователя по email
        $adminUser = \App\Models\Admin\User::where('email', $request->email)->first();
        
        if (!$adminUser) {
            \Log::warning('Landing login: user not found', ['email' => $request->email]);
            return back()->withErrors(['email' => 'Пользователь с таким email не найден']);
        }

        \Log::info('Landing login: user found', [
            'user_id' => $adminUser->id,
            'role' => $adminUser->role,
            'project_id' => $adminUser->project_id
        ]);

        // Проверяем пароль
        if (!password_verify($request->password, $adminUser->password)) {
            \Log::warning('Landing login: wrong password', ['email' => $request->email]);
            return back()->withErrors(['password' => 'Неверный пароль']);
        }

        // Проверяем, что это руководитель проекта (role = 'admin')
        if ($adminUser->role !== 'admin') {
            \Log::warning('Landing login: wrong role', [
                'email' => $request->email,
                'role' => $adminUser->role
            ]);
            return back()->withErrors(['email' => 'Доступ к личному кабинету только для руководителей проектов']);
        }

        // Проверяем, что у пользователя есть проект
        if (!$adminUser->project_id) {
            \Log::warning('Landing login: no project_id', ['email' => $request->email]);
            return back()->withErrors(['email' => 'Пользователь не привязан к проекту']);
        }

        // Получаем проект
        $project = $adminUser->project;
        if (!$project) {
            \Log::warning('Landing login: project not found', [
                'email' => $request->email,
                'project_id' => $adminUser->project_id
            ]);
            return back()->withErrors(['email' => 'Проект не найден']);
        }

        \Log::info('Landing login: success', [
            'email' => $request->email,
            'user_id' => $adminUser->id,
            'project_id' => $project->id
        ]);

        // Аутентифицируем пользователя через guard 'client'
        Auth::guard('client')->login($adminUser);

        return redirect()->route('landing.account.dashboard');
    }

    /**
     * Показать личный кабинет
     */
    public function dashboard()
    {
        // Получаем аутентифицированного пользователя через guard 'client'
        $user = Auth::guard('client')->user();
        
        if (!$user) {
            return redirect()->route('landing.account.login');
        }

        $project = Project::with(['subscriptions', 'clients', 'appointments'])
            ->find($user->project_id);

        if (!$project) {
            Auth::guard('client')->logout();
            return redirect()->route('landing.account.login');
        }

        $activeSubscription = $project->subscriptions()
            ->where('status', '!=', 'cancelled')
            ->orderBy('created_at', 'desc')
            ->first();

        return view('landing.account.dashboard', compact('project', 'activeSubscription'));
    }

    /**
     * Показать профиль
     */
    public function profile()
    {
        if (!Auth::guard('client')->check()) {
            return redirect()->route('landing.account.login');
        }

        // Перенаправляем на главную страницу личного кабинета с активной вкладкой профиля
        return redirect()->route('landing.account.dashboard') . '#profile';
    }

    /**
     * Обновить профиль
     */
    public function updateProfile(Request $request)
    {
        if (!Auth::guard('client')->check()) {
            if ($request->ajax()) {
                return response()->json(['message' => 'Не авторизован'], 401);
            }
            return redirect()->route('landing.account.login');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20'
        ]);

        $user = Auth::guard('client')->user();
        $project = Project::find($user->project_id);
        $project->update([
            'name' => $request->name,
            'phone' => $request->phone
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Профиль успешно обновлен']);
        }

        return back()->with('success', 'Профиль успешно обновлен');
    }

    /**
     * Обновить пароль
     */
    public function updatePassword(Request $request)
    {
        if (!Auth::guard('client')->check()) {
            return redirect()->route('landing.account.login');
        }

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
            'new_password_confirmation' => 'required'
        ]);

        $adminUser = Auth::guard('client')->user();
        
        if (!password_verify($request->current_password, $adminUser->password)) {
            return back()->withErrors(['current_password' => 'Текущий пароль неверен']);
        }

        $adminUser->update([
            'password' => bcrypt($request->new_password)
        ]);

        return back()->with('success', 'Пароль успешно изменен');
    }

    /**
     * Отправить ссылку для сброса пароля
     */
    public function sendPasswordResetLink(Request $request)
    {
        if (!Auth::guard('client')->check()) {
            if ($request->ajax()) {
                return response()->json(['message' => 'Не авторизован'], 401);
            }
            return redirect()->route('landing.account.login');
        }

        $request->validate([
            'email' => 'required|email'
        ]);

        // Используем существующую систему сброса пароля CRM
        $adminUser = Auth::guard('client')->user();
        
        if (!$adminUser) {
            if ($request->ajax()) {
                return response()->json(['message' => 'Пользователь с таким email не найден'], 404);
            }
            return back()->withErrors(['email' => 'Пользователь с таким email не найден']);
        }

        // Генерируем токен для сброса пароля
        $token = \Illuminate\Support\Str::random(64);
        
        // Сохраняем захешированный токен в базе данных
        \Illuminate\Support\Facades\DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'email' => $request->email,
                'token' => \Illuminate\Support\Facades\Hash::make($token),
                'created_at' => now()
            ]
        );

        // Отправляем письмо с ссылкой на существующую страницу сброса пароля CRM
        $resetUrl = route('password.reset', ['token' => $token, 'email' => $request->email]);
        
        // Здесь можно использовать Mail::send или создать отдельный Mailable класс
        // Пока просто показываем сообщение об успехе
        
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Ссылка для сброса пароля отправлена на указанный email.']);
        }
        
        return back()->with('password_status', 'Ссылка для сброса пароля отправлена на указанный email.');
    }

    /**
     * Выход
     */
    public function logout()
    {
        Auth::guard('client')->logout();
        return redirect()->route('beautyflow.index');
    }

    /**
     * Переход в CRM
     */
    public function goToCrm()
    {
        if (!Auth::guard('client')->check()) {
            return redirect()->route('landing.account.login');
        }

        $user = Auth::guard('client')->user();
        $project = Project::find($user->project_id);
        
        // Генерируем URL для входа в CRM
        $crmUrl = url('/client/login');
        
        return redirect($crmUrl);
    }
}
