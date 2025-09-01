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

// Авторизация в админку
Route::get('/login', [\App\Http\Controllers\Auth\AdminLoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/login', [\App\Http\Controllers\Auth\AdminLoginController::class, 'login'])->name('admin.login.post');
Route::post('/logout', [\App\Http\Controllers\Auth\AdminLoginController::class, 'logout'])->name('admin.logout');

// Все маршруты админки защищены авторизацией через guard 'panel'
Route::middleware(['auth:panel'])->name('admin.')->group(function () {
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
    Route::delete('/settings/image/{type}', [\App\Http\Controllers\Admin\SettingsController::class, 'removeImage'])->name('settings.remove-image');
    Route::delete('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'removeImage'])->name('settings.remove-image-general');
    Route::get('/settings/test', function () {
        return view('admin.settings.test');
    })->name('settings.test');
    Route::get('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');
    
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

    // Управление подписками
    Route::get('/subscriptions', [\App\Http\Controllers\Admin\SubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::get('/subscriptions/{subscription}', [\App\Http\Controllers\Admin\SubscriptionController::class, 'show'])->name('subscriptions.show');
    Route::post('/subscriptions/{subscription}/extend', [\App\Http\Controllers\Admin\SubscriptionController::class, 'extend'])->name('subscriptions.extend');
    Route::post('/subscriptions/{subscription}/cancel', [\App\Http\Controllers\Admin\SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');

    // Управление тарифами
    Route::resource('plans', \App\Http\Controllers\Admin\PlanController::class);
    Route::post('/plans/{plan}/update-prices', [\App\Http\Controllers\Admin\PlanController::class, 'updatePrices'])->name('plans.update-prices');

    // Настройки платежных систем
    Route::get('/payment-settings', [\App\Http\Controllers\Admin\PaymentSettingController::class, 'index'])->name('payment-settings.index');
    Route::post('/payment-settings/liqpay', [\App\Http\Controllers\Admin\PaymentSettingController::class, 'updateLiqPay'])->name('payment-settings.liqpay');
    Route::post('/payment-settings/stripe', [\App\Http\Controllers\Admin\PaymentSettingController::class, 'updateStripe'])->name('payment-settings.stripe');
    Route::post('/payment-settings/paypal', [\App\Http\Controllers\Admin\PaymentSettingController::class, 'updatePayPal'])->name('payment-settings.paypal');
    Route::post('/payment-settings/{method}/toggle', [\App\Http\Controllers\Admin\PaymentSettingController::class, 'toggleActive'])->name('payment-settings.toggle');

    // Управление валютами
    Route::get('/currencies', [\App\Http\Controllers\Admin\CurrencyController::class, 'index'])->name('currencies.index');
    Route::get('/currencies/create', [\App\Http\Controllers\Admin\CurrencyController::class, 'create'])->name('currencies.create');
    Route::post('/currencies', [\App\Http\Controllers\Admin\CurrencyController::class, 'store'])->name('currencies.store');
    Route::get('/currencies/{currency}/edit', [\App\Http\Controllers\Admin\CurrencyController::class, 'edit'])->name('currencies.edit');
    Route::get('/currencies/{currency}/data', [\App\Http\Controllers\Admin\CurrencyController::class, 'getCurrencyData'])->name('currencies.data');
    Route::put('/currencies/{currency}', [\App\Http\Controllers\Admin\CurrencyController::class, 'update'])->name('currencies.update');
    Route::delete('/currencies/{currency}', [\App\Http\Controllers\Admin\CurrencyController::class, 'destroy'])->name('currencies.destroy');
    Route::post('/currencies/{currency}/set-default', [\App\Http\Controllers\Admin\CurrencyController::class, 'setDefault'])->name('currencies.set-default');
    Route::post('/currencies/{currency}/toggle-active', [\App\Http\Controllers\Admin\CurrencyController::class, 'toggleActive'])->name('currencies.toggle-active');

    // Резервные копии
    Route::get('/backups', [\App\Http\Controllers\Admin\BackupController::class, 'index'])->name('backups.index');
    Route::post('/backups/database', [\App\Http\Controllers\Admin\BackupController::class, 'createDatabaseBackup'])->name('backups.database.create');
    Route::post('/backups/files', [\App\Http\Controllers\Admin\BackupController::class, 'createFilesBackup'])->name('backups.files.create');
    Route::get('/backups/{type}/{filename}/download', [\App\Http\Controllers\Admin\BackupController::class, 'downloadBackup'])->name('backups.download');
    Route::delete('/backups/{type}/{filename}', [\App\Http\Controllers\Admin\BackupController::class, 'deleteBackup'])->name('backups.delete');
    Route::post('/backups/database/restore', [\App\Http\Controllers\Admin\BackupController::class, 'restoreDatabase'])->name('backups.database.restore');
    Route::post('/currencies/clear-cache', [\App\Http\Controllers\Admin\CurrencyController::class, 'clearCache'])->name('currencies.clear-cache');

    // Управление языками
    Route::get('/languages', [\App\Http\Controllers\Admin\LanguageController::class, 'index'])->name('languages.index');
    Route::post('/languages', [\App\Http\Controllers\Admin\LanguageController::class, 'store'])->name('languages.store');
    Route::get('/languages/{language}/data', [\App\Http\Controllers\Admin\LanguageController::class, 'getLanguageData'])->name('languages.data');
    Route::put('/languages/{language}', [\App\Http\Controllers\Admin\LanguageController::class, 'update'])->name('languages.update');
    Route::delete('/languages/{language}', [\App\Http\Controllers\Admin\LanguageController::class, 'destroy'])->name('languages.destroy');
    Route::post('/languages/{language}/set-default', [\App\Http\Controllers\Admin\LanguageController::class, 'setDefault'])->name('languages.set-default');
    Route::post('/languages/{language}/toggle-active', [\App\Http\Controllers\Admin\LanguageController::class, 'toggleActive'])->name('languages.toggle-active');
    Route::post('/languages/clear-cache', [\App\Http\Controllers\Admin\LanguageController::class, 'clearCache'])->name('languages.clear-cache');

    // База знаний
    Route::resource('knowledge', \App\Http\Controllers\Admin\KnowledgeController::class);
    Route::post('/knowledge/{article}/toggle-publish', [\App\Http\Controllers\Admin\KnowledgeController::class, 'togglePublish'])
        ->name('knowledge.toggle-publish');
    Route::get('/knowledge/{article}/translations/{language}', [\App\Http\Controllers\Admin\KnowledgeController::class, 'getTranslation'])->name('knowledge.get-translation');
    Route::post('/knowledge/{article}/save-translation', [\App\Http\Controllers\Admin\KnowledgeController::class, 'saveTranslation'])->name('knowledge.save-translation');
}); 