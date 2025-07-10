<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Landing\RegisterController;

/*
|--------------------------------------------------------------------------
| Landing Routes
|--------------------------------------------------------------------------
|
| Здесь размещаются маршруты для публичной части сайта (лендинг)
|
*/

Route::get('/', function () {
    return view('landing.pages.index');
})->name('beautyflow.index');

Route::get('/about', function () {
    return view('landing.pages.about');
})->name('beautyflow.about');

Route::get('/contact', function () {
    return view('landing.pages.contact');
})->name('beautyflow.contact');

Route::get('/services', function () {
    return view('landing.pages.services');
})->name('beautyflow.services');

Route::post('/register', [RegisterController::class, 'store'])->name('beautyflow.register');

Route::get('/studio', function () {
    return view('landing.pages.index');
})->name('landing.manual'); 