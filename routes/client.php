<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\DashboardController;
use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\Client\ClientTypeController;
use App\Http\Controllers\Client\ProductController;
use App\Http\Controllers\Client\ProductCategoryController;
use App\Http\Controllers\Client\ProductBrandController;
use App\Http\Controllers\Client\ProductImportExportController;
use App\Http\Controllers\Client\SaleController;
use App\Http\Controllers\Client\PurchaseController;
use App\Http\Controllers\Client\SupplierController;
use App\Http\Controllers\Client\ServiceController;
use App\Http\Controllers\Client\AppointmentsController;
use App\Http\Controllers\Client\ExpensesController;
use App\Http\Controllers\Client\WarehouseController;
use App\Http\Controllers\Client\InventoryController;
use App\Http\Controllers\Client\ClientReportController;
use App\Http\Controllers\Client\TurnoverReportController;
use App\Http\Controllers\Auth\AdminForgotPasswordController;
use App\Http\Controllers\Auth\AdminResetPasswordController;
use App\Http\Controllers\Client\SettingsController;
use App\Http\Controllers\Client\SecurityController;

/*
|--------------------------------------------------------------------------
| Client Routes
|--------------------------------------------------------------------------
|
| Здесь размещаются маршруты для клиентской части приложения
| Все маршруты требуют аутентификации
|
*/

Route::get('clients/check', [App\Http\Controllers\Client\ClientController::class, 'checkUnique']);
Route::get('/password/reset', [AdminForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/email', [AdminForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/{token}', [AdminResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [AdminResetPasswordController::class, 'reset'])->name('password.update');

Route::middleware('auth:client')->group(function () {
    
    // Главная страница клиентской части
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    
    
    // Тестовая страница валют
    
    
    // API маршруты для валют
    Route::prefix('api/currencies')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\CurrencyController::class, 'index']);
        Route::get('/current', [\App\Http\Controllers\Api\CurrencyController::class, 'current']);
        Route::post('/set/{code}', [\App\Http\Controllers\Api\CurrencyController::class, 'setCurrency']);
        Route::get('/format/{amount}', [\App\Http\Controllers\Api\CurrencyController::class, 'formatAmount']);
    });
    
    // API маршруты для языков
    Route::prefix('api/languages')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\LanguageController::class, 'index']);
        Route::get('/current', [\App\Http\Controllers\Api\LanguageController::class, 'current']);
        Route::post('/set/{code}', [\App\Http\Controllers\Api\LanguageController::class, 'setLanguage']);
        Route::get('/translations', [\App\Http\Controllers\Api\LanguageController::class, 'translations']);
    });
    
    // API маршруты для настроек
    Route::prefix('api/settings')->group(function () {
        Route::post('/update', [SettingsController::class, 'updateLanguageCurrency']);
    });
    
    // Управление клиентами
    Route::prefix('clients')->name('clients.')->group(function () {
        Route::get('/', [ClientController::class, 'index'])->name('list');
        Route::post('/', [ClientController::class, 'store'])->name('store');
        Route::get('/create', [ClientController::class, 'create'])->name('create');
        Route::get('/{id}', [ClientController::class, 'show'])->name('show');
        Route::get('/check', [ClientController::class, 'checkUnique']);
        Route::delete('/{client}', [ClientController::class, 'destroy'])->name('destroy');
        Route::get('/{client}/edit', [ClientController::class, 'edit'])->name('edit');
        Route::put('/{client}', [ClientController::class, 'update'])->name('update');
    });
    
    // Типы клиентов
    Route::prefix('client-types')->name('client-types.')->group(function () {
        Route::get('/', [ClientTypeController::class, 'index'])->name('index');
        Route::get('/create', [ClientTypeController::class, 'create'])->name('create');
        Route::post('/', [ClientTypeController::class, 'store'])->name('store');
        Route::get('/{clientType}/edit', [ClientTypeController::class, 'edit'])->name('edit');
        Route::put('/{clientType}', [ClientTypeController::class, 'update'])->name('update');
        Route::delete('/{clientType}', [ClientTypeController::class, 'destroy'])->name('destroy');
    });
    
    // Управление товарами
    Route::prefix('products')->name('products.')->group(function () {

        Route::get('/import', [ProductImportExportController::class, 'showImportForm'])->name('import.form');
        Route::post('/import', [ProductImportExportController::class, 'import'])->name('import');
        Route::get('/export', [ProductImportExportController::class, 'export'])->name('export');
    
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/create', [ProductController::class, 'create'])->name('create');
        Route::post('/', [ProductController::class, 'store'])->name('store');
        
        // Новые маршруты для работы с мягким удалением (ПЕРЕД маршрутами с параметрами!)
        Route::get('/trashed', [ProductController::class, 'trashed'])->name('trashed');
        Route::post('/{id}/restore', [ProductController::class, 'restore'])->name('restore');
        Route::delete('/{id}/force', [ProductController::class, 'forceDelete'])->name('force-delete');
        Route::delete('/force-delete-all', [ProductController::class, 'forceDeleteAll'])->name('force-delete-all');
        
        // Маршруты с параметрами (ПОСЛЕ конкретных маршрутов!)
        Route::get('/{product}', [ProductController::class, 'show'])->name('show');
        Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
        Route::put('/{product}', [ProductController::class, 'update'])->name('update');
        Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');
        
        // Импорт/экспорт товаров
    });
    
    // Категории товаров
    Route::prefix('product-categories')->name('product-categories.')->group(function () {
        Route::get('/', [ProductCategoryController::class, 'index'])->name('index');
        Route::get('/create', [ProductCategoryController::class, 'create'])->name('create');
        Route::post('/', [ProductCategoryController::class, 'store'])->name('store');
        Route::get('/{productCategory}/edit', [ProductCategoryController::class, 'edit'])->name('edit');
        Route::put('/{productCategory}', [ProductCategoryController::class, 'update'])->name('update');
        Route::delete('/{productCategory}', [ProductCategoryController::class, 'destroy'])->name('destroy');
    });
    
    // Бренды товаров
    Route::prefix('product-brands')->name('product-brands.')->group(function () {
        Route::get('/', [ProductBrandController::class, 'index'])->name('index');
        Route::get('/create', [ProductBrandController::class, 'create'])->name('create');
        Route::post('/', [ProductBrandController::class, 'store'])->name('store');
        Route::get('/{productBrand}/edit', [ProductBrandController::class, 'edit'])->name('edit');
        Route::put('/{productBrand}', [ProductBrandController::class, 'update'])->name('update');
        Route::delete('/{productBrand}', [ProductBrandController::class, 'destroy'])->name('destroy');
    });
    
    // Продажи
    Route::prefix('sales')->name('sales.')->group(function () {
        Route::get('/', [SaleController::class, 'index'])->name('index');
        Route::get('/create', [SaleController::class, 'create'])->name('create');
        Route::post('/', [SaleController::class, 'store'])->name('store');
        Route::get('/{sale}', [SaleController::class, 'show'])->name('show');
        Route::get('/{sale}/edit', [SaleController::class, 'edit'])->name('edit');
        Route::put('/{sale}', [SaleController::class, 'update'])->name('update');
        Route::delete('/{sale}', [SaleController::class, 'destroy'])->name('destroy');
        Route::delete('/{sale}/items/{item}', [SaleController::class, 'deleteItem'])->name('items.delete');
    });
    
    // Закупки
    Route::prefix('purchases')->name('purchases.')->group(function () {
        Route::get('/', [PurchaseController::class, 'index'])->name('index');
        Route::get('/create', [PurchaseController::class, 'create'])->name('create');
        Route::post('/', [PurchaseController::class, 'store'])->name('store');
        Route::get('/{purchase}', [PurchaseController::class, 'show'])->name('show');
        Route::get('/{purchase}/edit', [PurchaseController::class, 'edit'])->name('edit');
        Route::put('/{purchase}', [PurchaseController::class, 'update'])->name('update');
        Route::delete('/{purchase}', [PurchaseController::class, 'destroy'])->name('destroy');
    });
    
    // Поставщики
    Route::prefix('suppliers')->name('suppliers.')->group(function () {
        Route::get('/', [SupplierController::class, 'index'])->name('index');
        Route::get('/create', [SupplierController::class, 'create'])->name('create');
        Route::post('/', [SupplierController::class, 'store'])->name('store');
        Route::get('/{supplier}/edit', [SupplierController::class, 'edit'])->name('edit');
        Route::put('/{supplier}', [SupplierController::class, 'update'])->name('update');
        Route::delete('/{supplier}', [SupplierController::class, 'destroy'])->name('destroy');
    });
    
    // Услуги
    Route::prefix('services')->name('services.')->group(function () {
        Route::get('/', [ServiceController::class, 'index'])->name('index');
        Route::get('/create', [ServiceController::class, 'create'])->name('create');
        Route::post('/', [ServiceController::class, 'store'])->name('store');
        Route::get('/{service}/edit', [ServiceController::class, 'edit'])->name('edit');
        Route::put('/{service}', [ServiceController::class, 'update'])->name('update');
        Route::delete('/{service}', [ServiceController::class, 'destroy'])->name('destroy');
    });
    
    // Записи на услуги
    Route::prefix('appointments')->name('appointments.')->group(function () {
        // Специальные маршруты ДО ресурсного маршрута
        Route::get('/calendar-events', [AppointmentsController::class, 'calendarEvents'])->name('calendar-events');
        Route::get('/events', [AppointmentsController::class, 'getEvents'])->name('events');
        Route::get('/ajax', [AppointmentsController::class, 'ajax']);
        // Ресурсный маршрут
        Route::get('/', [AppointmentsController::class, 'index'])->name('index');
        Route::get('/create', [AppointmentsController::class, 'create'])->name('create');
        Route::post('/', [AppointmentsController::class, 'store'])->name('store');
        Route::get('/{appointment}', [AppointmentsController::class, 'show'])->name('show');
        Route::get('/{appointment}/edit', [AppointmentsController::class, 'edit'])->name('edit');
        Route::put('/{appointment}', [AppointmentsController::class, 'update'])->name('update');
        Route::delete('/{appointment}', [AppointmentsController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/view', [AppointmentsController::class, 'view'])->name('view');
        Route::post('/{appointment}/update-sales', [AppointmentsController::class, 'updateSales'])->name('update-sales');
        Route::post('/{appointment}/save-products', [AppointmentsController::class, 'saveProducts'])->name('save-products');
        Route::post('/{appointment}/add-product', [AppointmentsController::class, 'addProduct'])->name('add-product');
        Route::delete('/{appointment}/delete-product/{sale}', [AppointmentsController::class, 'deleteProduct'])->name('delete-product');
        Route::post('/{appointment}/add-procedure', [AppointmentsController::class, 'addProcedure']);
        Route::post('/{appointment}/remove-product', [AppointmentsController::class, 'removeProduct'])->name('remove-product');
        Route::post('/{appointment}/move', [App\Http\Controllers\Client\AppointmentsController::class, 'move'])->name('move');
    });
    
    // Расходы
    Route::prefix('expenses')->name('expenses.')->group(function () {
        Route::get('/', [ExpensesController::class, 'index'])->name('index');
        Route::get('/create', [ExpensesController::class, 'create'])->name('create');
        Route::post('/', [ExpensesController::class, 'store'])->name('store');
        Route::get('/{expense}/edit', [ExpensesController::class, 'edit'])->name('edit');
        Route::put('/{expense}', [ExpensesController::class, 'update'])->name('update');
        Route::delete('/{expense}', [ExpensesController::class, 'destroy'])->name('destroy');
    });
    
    // Склады
    Route::prefix('warehouses')->name('warehouses.')->group(function () {
        Route::get('/', [WarehouseController::class, 'index'])->name('index');
        Route::get('/create', [WarehouseController::class, 'create'])->name('create');
        Route::post('/', [WarehouseController::class, 'store'])->name('store');
        Route::get('/{warehouse}/edit', [WarehouseController::class, 'edit'])->name('edit');
        Route::put('/{warehouse}', [WarehouseController::class, 'update'])->name('update');
        Route::delete('/{warehouse}', [WarehouseController::class, 'destroy'])->name('destroy');
        Route::get('/products', [WarehouseController::class, 'getProducts'])->name('products');
    });
    
    // Инвентаризация
    Route::prefix('inventories')->name('inventories.')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('index');
        Route::get('/create', [InventoryController::class, 'create'])->name('create');
        Route::post('/', [InventoryController::class, 'store'])->name('store');
        Route::get('/{inventory}', [InventoryController::class, 'show'])->name('show');
        Route::get('/{inventory}/edit', [InventoryController::class, 'edit'])->name('edit');
        Route::put('/{inventory}', [InventoryController::class, 'update'])->name('update');
        Route::delete('/{inventory}', [InventoryController::class, 'destroy'])->name('destroy');
        Route::get('/{inventory}/items', [InventoryController::class, 'items'])->name('items');
        Route::get('/{inventory}/pdf', [InventoryController::class, 'pdf'])->name('pdf');
        
    });
    
    // Отчеты
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/clients', [ClientReportController::class, 'index'])->name('clients.index');
        Route::get('/client-analytics', [ClientReportController::class, 'getClientAnalyticsData'])->name('clientAnalytics');
        Route::get('/turnover', [TurnoverReportController::class, 'index'])->name('turnover');
        Route::get('/turnover-analytics', [TurnoverReportController::class, 'getDynamicAnalyticsData'])->name('turnover.analytics');
        Route::get('/turnover-tops', [TurnoverReportController::class, 'getTopsAnalyticsData'])->name('turnover.tops');
        Route::get('/suppliers-analytics', [TurnoverReportController::class, 'suppliersAnalyticsData']);
        Route::get('/appointments-by-day', [AppointmentsController::class, 'getAppointmentsByDay']);
        Route::get('/appointment-status-data', [AppointmentsController::class, 'getAppointmentStatusData']);
        Route::get('/service-popularity-data', [AppointmentsController::class, 'getServicePopularityData']);
        Route::get('/top-clients-by-revenue', [AppointmentsController::class, 'getTopClientsByRevenue']);
        Route::get('/avg-check-dynamics', [AppointmentsController::class, 'getAvgCheckDynamics']);
        Route::get('/ltv-by-client-type', [AppointmentsController::class, 'getLtvByClientType']);
        Route::get('/top-services-by-revenue', [AppointmentsController::class, 'getTopServicesByRevenue']);
        // Аналитика по сотрудникам
        Route::get('/employees-procedures-count', [AppointmentsController::class, 'getEmployeesProceduresCount']);
        Route::get('/employees-procedures-structure', [AppointmentsController::class, 'getEmployeesProceduresStructure']);
        Route::get('/employees-procedures-dynamics', [AppointmentsController::class, 'getEmployeesProceduresDynamics']);
        Route::get('/employees-average-time', [AppointmentsController::class, 'getEmployeesAverageTime']);
        Route::get('/employees-revenue', [AppointmentsController::class, 'getEmployeesRevenue']);
        Route::get('/employees-average-check', [AppointmentsController::class, 'getEmployeesAverageCheck']);
    });

    // Зарплата
    Route::prefix('salary')->name('salary.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Client\SalaryController::class, 'index'])->name('index');
        Route::get('/statistics', [\App\Http\Controllers\Client\SalaryController::class, 'getStatistics'])->name('statistics');
        
        // Настройки зарплаты (модальные окна)
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::post('/', [\App\Http\Controllers\Client\SalaryController::class, 'storeSetting'])->name('store');
            Route::get('/{id}/edit', [\App\Http\Controllers\Client\SalaryController::class, 'editSetting'])->name('edit');
            Route::put('/{id}', [\App\Http\Controllers\Client\SalaryController::class, 'updateSetting'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\Client\SalaryController::class, 'destroySetting'])->name('destroy');
        });

        // Расчеты зарплаты
        Route::prefix('calculations')->name('calculations.')->group(function () {
            Route::get('/create', [\App\Http\Controllers\Client\SalaryController::class, 'createCalculation'])->name('create');
            Route::post('/', [\App\Http\Controllers\Client\SalaryController::class, 'storeCalculation'])->name('store');
            Route::get('/{id}', [\App\Http\Controllers\Client\SalaryController::class, 'showCalculation'])->name('show');
            Route::post('/{id}/approve', [\App\Http\Controllers\Client\SalaryController::class, 'approveCalculation'])->name('approve');
            Route::delete('/{id}', [\App\Http\Controllers\Client\SalaryController::class, 'destroyCalculation'])->name('destroy');
            Route::get('/by-user/{userId}', [\App\Http\Controllers\Client\SalaryController::class, 'getCalculationsByUser'])->name('by-user');
            Route::get('/{id}/details', [\App\Http\Controllers\Client\SalaryController::class, 'getCalculationDetails'])->name('details');
        });

        // Выплаты
        Route::prefix('payments')->name('payments.')->group(function () {
            Route::get('/create', [\App\Http\Controllers\Client\SalaryController::class, 'createPayment'])->name('create');
            Route::post('/', [\App\Http\Controllers\Client\SalaryController::class, 'storePayment'])->name('store');
            Route::get('/{id}', [\App\Http\Controllers\Client\SalaryController::class, 'showPayment'])->name('show');
            Route::get('/{id}/details', [\App\Http\Controllers\Client\SalaryController::class, 'getPaymentDetails'])->name('details');
            Route::post('/{id}/approve', [\App\Http\Controllers\Client\SalaryController::class, 'approvePayment'])->name('approve');
            Route::delete('/{id}', [\App\Http\Controllers\Client\SalaryController::class, 'destroyPayment'])->name('destroy');
        });
    });
    
    // График работы (Персонал)
    Route::prefix('work-schedules')->name('work-schedules.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Client\WorkScheduleController::class, 'index'])->name('index');
        Route::get('/employee-schedule', [\App\Http\Controllers\Client\WorkScheduleController::class, 'getEmployeeSchedule'])->name('employee-schedule');
        Route::post('/employee-schedule', [\App\Http\Controllers\Client\WorkScheduleController::class, 'saveEmployeeSchedule'])->name('save-employee-schedule');
        Route::get('/refresh-overview', [\App\Http\Controllers\Client\WorkScheduleController::class, 'refreshOverview'])->name('refresh-overview');
        Route::get('/week', [\App\Http\Controllers\Client\WorkScheduleController::class, 'getWeekSchedule'])->name('week');
        
        // Отпуска
        Route::get('/time-offs', [\App\Http\Controllers\Client\WorkScheduleController::class, 'getTimeOffs'])->name('time-offs.index');
        Route::post('/time-offs', [\App\Http\Controllers\Client\WorkScheduleController::class, 'storeTimeOff'])->name('time-offs.store');
        Route::get('/time-offs/{id}', [\App\Http\Controllers\Client\WorkScheduleController::class, 'getTimeOff'])->name('time-offs.show');
        Route::put('/time-offs/{id}', [\App\Http\Controllers\Client\WorkScheduleController::class, 'updateTimeOff'])->name('time-offs.update');
        Route::delete('/time-offs/{id}', [\App\Http\Controllers\Client\WorkScheduleController::class, 'destroyTimeOff'])->name('time-offs.destroy');
    });

    // Аналитика расходов
    Route::prefix('analytics')->group(function () {
        Route::get('expenses-by-month', [TurnoverReportController::class, 'expensesByMonth']);
        Route::get('expenses-by-category', [TurnoverReportController::class, 'expensesByCategory']);
        Route::get('expenses-category-dynamics', [TurnoverReportController::class, 'expensesCategoryDynamics']);
        Route::get('expenses-average-by-category', [TurnoverReportController::class, 'expensesAverageByCategory']);
        Route::get('expenses-top-months', [TurnoverReportController::class, 'expensesTopMonths']);
        Route::get('expenses-fixed-variable', [TurnoverReportController::class, 'expensesFixedVariable']);
        // Добавлено:
        Route::get('employees-analytics', [TurnoverReportController::class, 'employeesAnalytics']);
    });

    Route::get('/users', [\App\Http\Controllers\Client\ClientUserController::class, 'index'])->name('client.users.index');
    Route::post('/users', [\App\Http\Controllers\Client\ClientUserController::class, 'store'])->name('client.users.store');
    Route::get('/users/{user}/edit', [\App\Http\Controllers\Client\ClientUserController::class, 'edit'])->name('client.users.edit');
Route::get('/users/{user}/check', [\App\Http\Controllers\Client\ClientUserController::class, 'check'])->name('client.users.check');
Route::put('/users/{user}', [\App\Http\Controllers\Client\ClientUserController::class, 'update'])->name('client.users.update');
Route::delete('/users/{user}', [\App\Http\Controllers\Client\ClientUserController::class, 'destroy'])->name('client.users.destroy');
Route::post('/users/{user}/avatar', [\App\Http\Controllers\Client\ClientUserController::class, 'uploadAvatar'])->name('client.users.avatar');

    // Роли и доступы
    Route::get('/roles', [\App\Http\Controllers\Client\RoleController::class, 'index'])->name('roles.index');
Route::post('/roles', [\App\Http\Controllers\Client\RoleController::class, 'store'])->name('roles.store');
Route::get('/roles/{id}/edit', [\App\Http\Controllers\Client\RoleController::class, 'edit'])->name('roles.edit');
Route::put('/roles/{id}', [\App\Http\Controllers\Client\RoleController::class, 'update'])->name('roles.update');
Route::delete('/roles/{id}', [\App\Http\Controllers\Client\RoleController::class, 'destroy'])->name('roles.destroy');
Route::get('/roles/{id}', [\App\Http\Controllers\Client\RoleController::class, 'show'])->name('client.roles.show');


    Route::post('/security/email', [SecurityController::class, 'changeEmail'])->name('client.security.email');
    Route::post('/security/2fa/enable', [SecurityController::class, 'enable2fa'])->name('client.security.2fa.enable');
    Route::post('/security/2fa/disable', [SecurityController::class, 'disable2fa'])->name('client.security.2fa.disable');
    Route::get('/email/change/confirm', [SecurityController::class, 'confirmEmailChange'])->name('client.security.email.confirm');

    // Тикеты поддержки
    Route::get('/support-tickets', [\App\Http\Controllers\Client\SupportTicketController::class, 'index'])->name('client.support-tickets.index');
    Route::post('/support-tickets', [\App\Http\Controllers\Client\SupportTicketController::class, 'store'])
        ->name('support-tickets.store')
        ->middleware('rate.limit:tickets'); // Максимум 10 тикетов в минуту
    // Сообщения тикета поддержки (чат)
    Route::get('/support-tickets/{ticket}/messages', [\App\Http\Controllers\Client\SupportTicketMessageController::class, 'index'])->name('support-tickets.messages.index');
    Route::post('/support-tickets/{ticket}/messages', [\App\Http\Controllers\Client\SupportTicketMessageController::class, 'store'])
        ->name('support-tickets.messages.store')
        ->middleware('rate.limit:messages'); // Максимум 30 сообщений в минуту

    // Уведомления
    Route::get('/notifications', [\App\Http\Controllers\Client\NotificationController::class, 'index'])->name('client.notifications.index');
    Route::post('/notifications/{notification}/read', [\App\Http\Controllers\Client\NotificationController::class, 'markAsRead'])
        ->name('client.notifications.read')
        ->middleware('rate.limit:notifications'); // Максимум 60 отметок "прочитано" в минуту
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\Client\NotificationController::class, 'markAllAsRead'])
        ->name('client.notifications.mark-all-read')
        ->middleware('rate.limit:notifications'); // Максимум 60 отметок "прочитано" в минуту
    Route::delete('/notifications/{notification}', [\App\Http\Controllers\Client\NotificationController::class, 'destroy'])
        ->name('client.notifications.destroy')
        ->middleware('rate.limit:notifications'); // Максимум 60 удалений в минуту

    // Настройки Telegram
    Route::get('/telegram-settings', [\App\Http\Controllers\Client\TelegramSettingsController::class, 'index'])
        ->name('client.telegram-settings.index');
    Route::put('/telegram-settings', [\App\Http\Controllers\Client\TelegramSettingsController::class, 'update'])
        ->name('client.telegram-settings.update');
    Route::post('/telegram-settings/test', [\App\Http\Controllers\Client\TelegramSettingsController::class, 'testConnection'])
        ->name('client.telegram-settings.test');
    Route::get('/telegram-settings/instructions', [\App\Http\Controllers\Client\TelegramSettingsController::class, 'getInstructions'])
        ->name('client.telegram-settings.instructions');

    // Настройки Email
    Route::get('/email-settings', [\App\Http\Controllers\Client\EmailSettingsController::class, 'index'])
        ->name('client.email-settings.index');
    Route::put('/email-settings', [\App\Http\Controllers\Client\EmailSettingsController::class, 'update'])
        ->name('client.email-settings.update');
    Route::post('/email-settings/test', [\App\Http\Controllers\Client\EmailSettingsController::class, 'testConnection'])
        ->name('client.email-settings.test');
    Route::get('/email-settings/instructions', function() {
        return response()->json([
            'instructions' => [
                'step1' => __('messages.email_instructions_step1'),
                'step2' => __('messages.email_instructions_step2'),
                'step3' => __('messages.email_instructions_step3'),
                'step4' => __('messages.email_instructions_step4'),
                'step5' => __('messages.email_instructions_step5'),
                'step6' => __('messages.email_instructions_step6'),
                'step7' => __('messages.email_instructions_step7'),
                'step8' => __('messages.email_instructions_step8'),
            ]
        ]);
    })->name('client.email-settings.instructions');

    // Настройки виджета для сайта
    Route::get('/widget-settings', [\App\Http\Controllers\Client\WidgetSettingsController::class, 'index'])
        ->name('client.widget-settings.index');
    Route::put('/widget-settings', [\App\Http\Controllers\Client\WidgetSettingsController::class, 'update'])
        ->name('client.widget-settings.update');
    Route::get('/widget-settings/generate-code', [\App\Http\Controllers\Client\WidgetSettingsController::class, 'generateCode'])
        ->name('client.widget-settings.generate-code');

    Route::get('/api/dashboard/profit-chart', [\App\Http\Controllers\Client\DashboardController::class, 'profitChartData']);
    Route::get('/api/dashboard/sales-chart', [\App\Http\Controllers\Client\DashboardController::class, 'salesChartData']);
    Route::get('/api/dashboard/services-chart', [\App\Http\Controllers\Client\DashboardController::class, 'servicesChartData']);
    Route::get('/api/dashboard/expenses-chart', [\App\Http\Controllers\Client\DashboardController::class, 'expensesChartData']);
    Route::get('/api/dashboard/activity-chart', [\App\Http\Controllers\Client\DashboardController::class, 'activityChartData']);

    // Подписки
    Route::prefix('subscriptions')->name('client.subscriptions.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Client\SubscriptionsController::class, 'index'])->name('index');
        Route::get('/renew', [\App\Http\Controllers\Client\SubscriptionsController::class, 'renew'])->name('renew');
        Route::post('/change-plan', [\App\Http\Controllers\Client\SubscriptionsController::class, 'changePlan'])->name('change-plan');
        Route::get('/cancel', [\App\Http\Controllers\Client\SubscriptionsController::class, 'cancel'])->name('cancel');
        Route::post('/activate', [\App\Http\Controllers\Client\SubscriptionsController::class, 'activateSubscription'])->name('activate');
        Route::post('/payment-failed', [\App\Http\Controllers\Client\SubscriptionsController::class, 'handleFailedPayment'])->name('payment-failed');
    });

    // Настройки
    Route::prefix('settings')->name('client.settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::post('/update', [SettingsController::class, 'update'])->name('update');
        Route::post('/update-language-currency', [SettingsController::class, 'updateLanguageCurrency'])->name('update-language-currency');
    });
    
    // Онлайн-бронирование
    Route::prefix('booking')->name('client.booking.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Client\BookingManagementController::class, 'index'])->name('index');
        Route::post('/settings', [\App\Http\Controllers\Client\BookingManagementController::class, 'updateSettings'])->name('update-settings');
        Route::get('/schedules', [\App\Http\Controllers\Client\BookingManagementController::class, 'schedules'])->name('schedules');
        Route::get('/schedules/user', [\App\Http\Controllers\Client\BookingManagementController::class, 'getUserSchedule'])->name('get-user-schedule');
        Route::post('/schedules/save', [\App\Http\Controllers\Client\BookingManagementController::class, 'saveUserSchedule'])->name('save-user-schedule');
        
        // Управление услугами мастеров
        Route::post('/user-services', [\App\Http\Controllers\Client\UserServicesController::class, 'store'])->name('user-services.store');
        Route::get('/user-services/{id}', [\App\Http\Controllers\Client\UserServicesController::class, 'show'])->name('user-services.show');
        Route::put('/user-services/{id}', [\App\Http\Controllers\Client\UserServicesController::class, 'update'])->name('user-services.update');
        Route::delete('/user-services/{id}', [\App\Http\Controllers\Client\UserServicesController::class, 'destroy'])->name('user-services.destroy');
        Route::get('/user-services/user/{userId}', [\App\Http\Controllers\Client\UserServicesController::class, 'getUserServices'])->name('user-services.get-user-services');
    });



}); 

