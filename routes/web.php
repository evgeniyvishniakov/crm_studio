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
Route::get('/login', [ClientAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [ClientAuthController::class, 'login']);
Route::post('/logout', [ClientAuthController::class, 'logout'])->name('logout');
