<?php

use Illuminate\Support\Facades\Route;

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
    
    // Управление пользователями
    Route::get('/users', function () {
        return view('admin.users.index');
    })->name('users.index');
    
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
}); 