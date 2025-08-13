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

Route::get('/contact', function () {
    return view('landing.pages.contact');
})->name('beautyflow.contact');

Route::get('/pricing', function () {
    return view('landing.pages.pricing');
})->name('beautyflow.pricing');

Route::get('/features', function () {
    return view('landing.pages.features');
})->name('beautyflow.features');


Route::get('/privacy', function () {
    return view('landing.pages.privacy');
})->name('beautyflow.privacy');

Route::get('/terms', function () {
    return view('landing.pages.terms');
})->name('beautyflow.terms');



Route::get('/knowledge', function () {
    $articles = \App\Models\KnowledgeArticle::published()
        ->with(['steps', 'tips'])
        ->orderBy('sort_order')
        ->get();
    
    $categories = [
        'getting-started' => 'Начало работы',
        'features' => 'Функции',
        'tips' => 'Советы',
        'troubleshooting' => 'Решение проблем'
    ];
    
    return view('landing.pages.knowledge', compact('articles', 'categories'));
})->name('beautyflow.knowledge');

Route::get('/knowledge/{slug}', function ($slug) {
    $article = \App\Models\KnowledgeArticle::published()
        ->where('slug', $slug)
        ->with(['steps', 'tips'])
        ->firstOrFail();
    
    return view('landing.pages.knowledge-show', compact('article'));
})->name('beautyflow.knowledge.show');

Route::post('/register', [RegisterController::class, 'store'])->name('beautyflow.register');

// Личный кабинет
Route::get('/account/login', [\App\Http\Controllers\Landing\AccountController::class, 'showLogin'])->name('landing.account.login');
Route::post('/account/login', [\App\Http\Controllers\Landing\AccountController::class, 'login'])->name('landing.account.login.post');

Route::middleware('landing.auth')->group(function () {
    Route::get('/account/dashboard', [\App\Http\Controllers\Landing\AccountController::class, 'dashboard'])->name('landing.account.dashboard');
    Route::get('/account/profile', [\App\Http\Controllers\Landing\AccountController::class, 'profile'])->name('landing.account.profile');
    Route::post('/account/profile', [\App\Http\Controllers\Landing\AccountController::class, 'updateProfile'])->name('landing.account.profile.update');
    Route::get('/account/crm', [\App\Http\Controllers\Landing\AccountController::class, 'goToCrm'])->name('landing.account.crm');
    Route::post('/account/logout', [\App\Http\Controllers\Landing\AccountController::class, 'logout'])->name('landing.account.logout');
}); 