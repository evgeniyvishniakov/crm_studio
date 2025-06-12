<?php

use App\Http\Controllers\AppointmentsController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\ExpensesController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('main.index');
});
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProductController;

Route::prefix('clients')->group(function () {
    Route::get('/', [ClientController::class, 'index'])->name('clients.list');
    Route::post('/', [ClientController::class, 'store'])->name('clients.store');
    Route::get('/create', [ClientController::class, 'create'])->name('clients.create'); // если понадобится форма
    Route::get('/{id}', [ClientController::class, 'show'])->name('clients.show');
    Route::get('/check', [ClientController::class, 'checkExisting']);
    Route::delete('/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');
    Route::get('/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit');
    Route::put('/{client}', [ClientController::class, 'update'])->name('clients.update');
});
Route::resource('products', ProductController::class);
Route::post('/products/{product}/remove-photo', [ProductController::class, 'removePhoto'])->name('products.remove-photo');


Route::resource('warehouse', WarehouseController::class);
Route::prefix('warehouse')->group(function () {
    Route::get('/', [WarehouseController::class, 'index'])->name('warehouse.index');
    Route::post('/', [WarehouseController::class, 'store'])->name('warehouse.store');
    Route::put('/{warehouse}', [WarehouseController::class, 'update'])->name('warehouse.update');
    Route::delete('/{warehouse}', [WarehouseController::class, 'destroy'])->name('warehouse.destroy');
    Route::get('/products', [WarehouseController::class, 'getProducts'])->name('warehouse.products');
    Route::get('/warehouse/{id}/edit', [WarehouseController::class, 'edit'])->name('warehouse.edit');
});

Route::resource('purchases', PurchaseController::class);

Route::prefix('sales')->group(function () {
    Route::get('/', [SaleController::class, 'index'])->name('sales.index');
    Route::post('/', [SaleController::class, 'store'])->name('sales.store');
    Route::get('/{sale}/edit', [SaleController::class, 'edit'])->name('sales.edit');
    Route::put('/{sale}', [SaleController::class, 'update'])->name('sales.update');
    Route::delete('/{sale}', [SaleController::class, 'destroy'])->name('sales.destroy');
    Route::delete('/{sale}/items/{item}', [SaleController::class, 'deleteItem'])->name('sales.items.delete');});

Route::resource('services', ServiceController::class);

// Маршруты календаря
Route::get('/appointments/calendar-events', [AppointmentsController::class, 'calendarEvents'])->name('appointments.calendar-events');
Route::get('/appointments/events', [AppointmentsController::class, 'getEvents'])->name('appointments.events');

// Основные маршруты для записей
Route::resource('appointments', AppointmentsController::class);
Route::get('/appointments/{id}/view', [AppointmentsController::class, 'view']);
Route::post('/appointments/{appointment}/update-sales', [AppointmentsController::class, 'updateSales'])
    ->name('appointments.update-sales');
Route::post('/appointments/{appointment}/save-products', [AppointmentsController::class, 'saveProducts'])
    ->name('appointments.save-products');
Route::post('/appointments/{appointment}/add-product', [AppointmentsController::class, 'addProduct'])
    ->name('appointments.add-product');
Route::delete('/appointments/{appointment}/delete-product/{sale}', [AppointmentsController::class, 'deleteProduct'])->name('appointments.delete-product');
Route::post('/appointments/{appointment}/add-procedure', [AppointmentsController::class, 'addProcedure']);
Route::post('/appointments/{appointment}/remove-product', [AppointmentsController::class, 'removeProduct'])
    ->name('appointments.remove-product');

// Маршруты для работы с расходами
Route::prefix('expenses')->group(function () {
    Route::get('/', [ExpensesController::class, 'index'])->name('expenses.index');
    Route::post('/', [ExpensesController::class, 'store'])->name('expenses.store');
    Route::get('/{expense}/edit', [ExpensesController::class, 'edit'])->name('expenses.edit');
    Route::put('/{expense}', [ExpensesController::class, 'update'])->name('expenses.update');
    Route::delete('/{expense}', [ExpensesController::class, 'destroy'])->name('expenses.destroy');
});

Route::get('/check-session', function () {
    return response()->json([
        'authenticated' => auth()->check(),
        'csrf_token' => csrf_token()
    ]);
});

Route::resource('product-categories', \App\Http\Controllers\ProductCategoryController::class)
    ->except(['create', 'show']);
Route::resource('product-brands', \App\Http\Controllers\ProductBrandController::class)
    ->except(['create', 'show']);
Route::resource('suppliers', \App\Http\Controllers\SupplierController::class)
    ->except(['create', 'show']);
Route::resource('client-types', \App\Http\Controllers\ClientTypeController::class)
    ->except(['create', 'show']);
