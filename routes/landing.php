<?php

use Illuminate\Support\Facades\Route;

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
})->name('landing.index');

Route::get('/about', function () {
    return view('landing.pages.about');
})->name('landing.about');

Route::get('/contact', function () {
    return view('landing.pages.contact');
})->name('landing.contact');

Route::get('/services', function () {
    return view('landing.pages.services');
})->name('landing.services'); 