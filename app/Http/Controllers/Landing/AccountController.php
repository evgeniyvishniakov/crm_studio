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

        // Ищем пользователя по email
        $adminUser = \App\Models\Admin\User::where('email', $request->email)->first();
        
        if (!$adminUser) {
            return back()->withErrors(['email' => 'Пользователь с таким email не найден']);
        }

        // Проверяем пароль
        if (!password_verify($request->password, $adminUser->password)) {
            return back()->withErrors(['password' => 'Неверный пароль']);
        }

        // Проверяем, что это руководитель проекта (role = 'admin')
        if ($adminUser->role !== 'admin') {
            return back()->withErrors(['email' => 'Доступ к личному кабинету только для руководителей проектов']);
        }

        // Проверяем, что у пользователя есть проект
        if (!$adminUser->project_id) {
            return back()->withErrors(['email' => 'Пользователь не привязан к проекту']);
        }

        // Получаем проект
        $project = $adminUser->project;
        if (!$project) {
            return back()->withErrors(['email' => 'Проект не найден']);
        }

        // Создаем сессию для клиента
        session([
            'client_logged_in' => true,
            'client_project_id' => $project->id,
            'client_admin_id' => $adminUser->id,
            'client_email' => $request->email
        ]);

        return redirect()->route('landing.account.dashboard');
    }

    /**
     * Показать личный кабинет
     */
    public function dashboard()
    {
        if (!session('client_logged_in')) {
            return redirect()->route('landing.account.login');
        }

        $project = Project::with(['subscriptions', 'clients', 'appointments'])
            ->find(session('client_project_id'));

        if (!$project) {
            session()->forget(['client_logged_in', 'client_project_id', 'client_admin_id', 'client_email']);
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
        if (!session('client_logged_in')) {
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
        if (!session('client_logged_in')) {
            return redirect()->route('landing.account.login');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20'
        ]);

        $project = Project::find(session('client_project_id'));
        $project->update([
            'name' => $request->name,
            'phone' => $request->phone
        ]);

        return back()->with('success', 'Профиль успешно обновлен');
    }

    /**
     * Обновить пароль
     */
    public function updatePassword(Request $request)
    {
        if (!session('client_logged_in')) {
            return redirect()->route('landing.account.login');
        }

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
            'new_password_confirmation' => 'required'
        ]);

        $adminUser = \App\Models\Admin\User::find(session('client_admin_id'));
        
        if (!password_verify($request->current_password, $adminUser->password)) {
            return back()->withErrors(['current_password' => 'Текущий пароль неверен']);
        }

        $adminUser->update([
            'password' => bcrypt($request->new_password)
        ]);

        return back()->with('success', 'Пароль успешно изменен');
    }

    /**
     * Выход
     */
    public function logout()
    {
        session()->forget(['client_logged_in', 'client_project_id', 'client_admin_id', 'client_email']);
        return redirect()->route('landing.home');
    }

    /**
     * Переход в CRM
     */
    public function goToCrm()
    {
        if (!session('client_logged_in')) {
            return redirect()->route('landing.account.login');
        }

        $project = Project::find(session('client_project_id'));
        
        // Генерируем URL для входа в CRM
        $crmUrl = url('/client/login');
        
        return redirect($crmUrl);
    }
}
