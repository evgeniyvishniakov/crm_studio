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

Route::get('/pricing', function () {
    return view('landing.pages.pricing');
})->name('beautyflow.pricing');

Route::get('/features', function () {
    return redirect(route('beautyflow.index') . '#features-grid');
})->name('beautyflow.features');

Route::get('/integrations', function () {
    return view('landing.pages.integrations');
})->name('beautyflow.integrations');

Route::get('/demo', function () {
    return view('landing.pages.demo');
})->name('beautyflow.demo');

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

Route::get('/knowledge/roles', function () {
    return view('landing.pages.article-roles');
})->name('beautyflow.knowledge.roles');

Route::get('/knowledge/{slug}', function ($slug) {
    $article = \App\Models\KnowledgeArticle::published()
        ->with(['steps', 'tips'])
        ->where('slug', $slug)
        ->firstOrFail();
    
    $categories = [
        'getting-started' => 'Начало работы',
        'features' => 'Функции',
        'tips' => 'Советы',
        'troubleshooting' => 'Решение проблем'
    ];
    
    return view('landing.pages.knowledge-article', compact('article', 'categories'));
})->name('knowledge.show');

Route::post('/register', [RegisterController::class, 'store'])->name('beautyflow.register');

Route::get('/studio', function () {
    return view('landing.pages.index');
})->name('landing.manual'); 