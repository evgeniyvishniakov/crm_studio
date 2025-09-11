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
Route::get('/', function () {
    return redirect()->to('https://trimora.app');
})->name('home');


// Маршруты логина для клиентской части
Route::get('/login', [ClientAuthController::class, 'showLoginForm'])->middleware('guest')->name('login');
Route::post('/login', [ClientAuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('/logout', [ClientAuthController::class, 'logout'])->name('logout');

// Публичные маршруты бронирования (с защитой от спама)
Route::prefix('book')->name('public.booking.')->group(function () {
    Route::get('/{slug}', [\App\Http\Controllers\PublicBookingController::class, 'show'])
        ->name('show')
        ->middleware('throttle:60,1'); // 60 запросов в минуту
    
    Route::post('/{slug}/slots', [\App\Http\Controllers\PublicBookingController::class, 'getAvailableSlots'])
        ->name('slots')
        ->middleware('throttle:30,1'); // 30 запросов в минуту
    
    Route::post('/{slug}/schedule', [\App\Http\Controllers\PublicBookingController::class, 'getMasterSchedule'])
        ->name('schedule')
        ->middleware('throttle:30,1'); // 30 запросов в минуту
    
    Route::post('/{slug}/unavailable-dates', [\App\Http\Controllers\PublicBookingController::class, 'getUnavailableDates'])
        ->name('unavailable-dates')
        ->middleware('throttle:30,1'); // 30 запросов в минуту
    
    Route::post('/{slug}/store', [\App\Http\Controllers\PublicBookingController::class, 'store'])
        ->name('store')
        ->middleware('throttle:5,1'); // 5 записей в минуту (защита от спама)
});

