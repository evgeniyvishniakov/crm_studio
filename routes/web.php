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
