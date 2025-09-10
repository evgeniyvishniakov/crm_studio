<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API для генерации языковых URL (защищен CSRF)
Route::post('/language-url', [\App\Http\Controllers\Api\LanguageUrlController::class, 'generateUrl'])
    ->middleware('throttle:10,1'); // 10 запросов в минуту

// API для виджета записи (публичный, но с ограничением)
Route::prefix('widget')->name('widget.')->group(function () {
    Route::get('/config/{slug}', [\App\Http\Controllers\Api\WidgetController::class, 'getConfig'])
        ->name('config')
        ->middleware('throttle:30,1'); // 30 запросов в минуту
});

// API для смены языка на лендинге (публичный, но с ограничением)
Route::prefix('languages')->name('languages.')->group(function () {
    Route::post('/set/{code}', [\App\Http\Controllers\Api\LanguageController::class, 'setLanguage'])
        ->name('set')
        ->middleware('throttle:5,1'); // 5 запросов в минуту
    Route::get('/', [\App\Http\Controllers\Api\LanguageController::class, 'index'])
        ->name('index')
        ->middleware('throttle:20,1'); // 20 запросов в минуту
    Route::get('/current', [\App\Http\Controllers\Api\LanguageController::class, 'current'])
        ->name('current')
        ->middleware('throttle:20,1'); // 20 запросов в минуту
});


