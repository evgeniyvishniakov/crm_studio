<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Auth::routes(['reset' => true, 'register' => false, 'verify' => false]);

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Здесь можно добавить публичные маршруты, если потребуется
|
*/

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
