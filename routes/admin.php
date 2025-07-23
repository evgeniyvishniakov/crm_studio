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

Route::get('/login', [\App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/login', [\App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::post('/logout', function () {
    auth()->logout();
    return redirect('/panel/login');
})->name('admin.logout');

// Все остальные маршруты админки защищены middleware 'admin.only'
Route::middleware(['admin.only'])->name('admin.')->group(function () {
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
    Route::get('/users/create', [\App\Http\Controllers\Admin\UserController::class, 'create'])->name('users.create');
    Route::post('/users', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('users.store');
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
    Route::get('/logs', [\App\Http\Controllers\Admin\LogController::class, 'index'])->name('logs.index');
    Route::get('/logs/{id}', [\App\Http\Controllers\Admin\LogController::class, 'show'])->name('logs.show');
    Route::post('/logs/{id}/fix', [\App\Http\Controllers\Admin\LogController::class, 'fix'])->name('logs.fix');
    
    // Выход из системы
    Route::resource('projects', ProjectController::class);

    // Тикеты поддержки (сообщения)
    Route::get('/tickets', [\App\Http\Controllers\Admin\TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/{ticket}', [\App\Http\Controllers\Admin\TicketController::class, 'show'])->name('tickets.show');
    // AJAX-роуты для сообщений тикета
    Route::get('/tickets/{ticket}/messages', [\App\Http\Controllers\Admin\TicketController::class, 'messages']);
    Route::post('/tickets/{ticket}/messages', [\App\Http\Controllers\Admin\TicketController::class, 'sendMessage'])
        ->middleware('rate.limit:messages'); // Максимум 30 сообщений в минуту
    // AJAX: смена статуса тикета
    Route::post('/tickets/{ticket}/status', [\App\Http\Controllers\Admin\TicketController::class, 'updateStatus'])->name('tickets.status');
    Route::delete('/tickets/{ticket}', [\App\Http\Controllers\Admin\TicketController::class, 'destroy'])->name('tickets.destroy');

    // Уведомления
    Route::get('/notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAsRead'])
        ->name('notifications.read')
        ->middleware('rate.limit:notifications'); // Максимум 60 отметок "прочитано" в минуту

    // Управление валютами
    Route::get('/currencies', [\App\Http\Controllers\Admin\CurrencyController::class, 'index'])->name('currencies.index');
    Route::get('/currencies/create', [\App\Http\Controllers\Admin\CurrencyController::class, 'create'])->name('currencies.create');
    Route::post('/currencies', [\App\Http\Controllers\Admin\CurrencyController::class, 'store'])->name('currencies.store');
    Route::get('/currencies/{currency}/edit', [\App\Http\Controllers\Admin\CurrencyController::class, 'edit'])->name('currencies.edit');
    Route::put('/currencies/{currency}', [\App\Http\Controllers\Admin\CurrencyController::class, 'update'])->name('currencies.update');
    Route::delete('/currencies/{currency}', [\App\Http\Controllers\Admin\CurrencyController::class, 'destroy'])->name('currencies.destroy');
    Route::post('/currencies/{currency}/set-default', [\App\Http\Controllers\Admin\CurrencyController::class, 'setDefault'])->name('currencies.set-default');
    Route::post('/currencies/{currency}/toggle-active', [\App\Http\Controllers\Admin\CurrencyController::class, 'toggleActive'])->name('currencies.toggle-active');
    Route::post('/currencies/clear-cache', [\App\Http\Controllers\Admin\CurrencyController::class, 'clearCache'])->name('currencies.clear-cache');
}); 