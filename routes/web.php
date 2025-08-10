<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Client\ClientAuthController;

Auth::routes(['reset' => false, 'register' => false, 'verify' => false]);

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Здесь можно добавить публичные маршруты, если потребуется
|
*/

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Маршруты логина для клиентской части
Route::get('/login', [ClientAuthController::class, 'showLoginForm'])->middleware('guest')->name('login');
Route::post('/login', [ClientAuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('/logout', [ClientAuthController::class, 'logout'])->name('logout');

// Публичные маршруты бронирования
Route::prefix('book')->name('public.booking.')->group(function () {
    Route::get('/{slug}', [\App\Http\Controllers\PublicBookingController::class, 'show'])->name('show');
    Route::post('/{slug}/slots', [\App\Http\Controllers\PublicBookingController::class, 'getAvailableSlots'])->name('slots');
    Route::post('/{slug}/schedule', [\App\Http\Controllers\PublicBookingController::class, 'getMasterSchedule'])->name('schedule');
    Route::post('/{slug}/unavailable-dates', [\App\Http\Controllers\PublicBookingController::class, 'getUnavailableDates'])->name('unavailable-dates');
    Route::post('/{slug}/store', [\App\Http\Controllers\PublicBookingController::class, 'store'])->name('store');
});

// Публичные маршруты базы знаний
Route::prefix('knowledge')->name('knowledge.')->group(function () {
    Route::get('/', [\App\Http\Controllers\KnowledgeController::class, 'index'])->name('index');
    Route::get('/{slug}', [\App\Http\Controllers\KnowledgeController::class, 'show'])->name('show');
});
