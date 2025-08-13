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

Route::get('/about', function () {
    return view('landing.pages.about');
})->name('beautyflow.about');

Route::get('/privacy', function () {
    return view('landing.pages.privacy');
})->name('beautyflow.privacy');

Route::get('/terms', function () {
    return view('landing.pages.terms');
})->name('beautyflow.terms');

Route::get('/faq', function () {
    return view('landing.pages.faq');
})->name('beautyflow.faq');

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