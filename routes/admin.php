<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Auth\AdminForgotPasswordController;
use App\Http\Controllers\Auth\AdminResetPasswordController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Здесь размещаются маршруты для административной части приложения
| Все маршруты требуют аутентификации и прав администратора
|
*/

Route::name('admin.')->group(function () {
    // Главная страница админки
    Route::get('/', function () {
        return view('admin.dashboard.index');
    })->name('dashboard');
    
    // Маршруты сброса пароля для админских пользователей
    Route::get('/password/reset', [AdminForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/password/email', [AdminForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/password/reset/{token}', [AdminResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/password/reset', [AdminResetPasswordController::class, 'reset'])->name('password.update');
    
    // Управление пользователями
    Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
    Route::put('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');
    
    // Роли и права
    Route::get('/roles', function () {
        return view('admin.roles.index');
    })->name('roles.index');
    
    // Настройки системы
    Route::get('/settings', function () {
        return view('admin.settings.index');
    })->name('settings.index');
    
    // Email шаблоны
    Route::get('/email-templates', function () {
        return view('admin.email-templates.index');
    })->name('email-templates.index');
    
    // Безопасность
    Route::get('/security', function () {
        return view('admin.security.index');
    })->name('security.index');
    
    // Логи системы
    Route::get('/logs', function () {
        return view('admin.logs.index');
    })->name('logs.index');
    
    // Выход из системы
    Route::post('/logout', function () {
        auth()->logout();
        return redirect('/');
    })->name('logout');

    Route::resource('projects', ProjectController::class);
}); 