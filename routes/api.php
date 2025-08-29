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

// API для виджета записи
Route::prefix('widget')->name('widget.')->group(function () {
    Route::get('/config/{slug}', [\App\Http\Controllers\Api\WidgetController::class, 'getConfig'])->name('config');
});

// API для смены языка на лендинге
Route::prefix('languages')->name('languages.')->group(function () {
    Route::post('/set/{code}', [\App\Http\Controllers\Api\LanguageController::class, 'setLanguage'])->name('set');
    Route::get('/', [\App\Http\Controllers\Api\LanguageController::class, 'index'])->name('index');
    Route::get('/current', [\App\Http\Controllers\Api\LanguageController::class, 'current'])->name('current');
});


