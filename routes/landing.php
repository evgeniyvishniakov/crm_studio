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

Route::get('/pricing', [\App\Http\Controllers\Landing\PricingController::class, 'index'])->name('beautyflow.pricing');




Route::get('/privacy', function () {
    return view('landing.pages.privacy');
})->name('beautyflow.privacy');

Route::get('/terms', function () {
    return view('landing.pages.terms');
})->name('beautyflow.terms');



Route::get('/knowledge', [\App\Http\Controllers\KnowledgeController::class, 'index'])->name('beautyflow.knowledge');

Route::get('/knowledge/{slug}', [\App\Http\Controllers\KnowledgeController::class, 'show'])->name('beautyflow.knowledge.show');

Route::post('/register', [RegisterController::class, 'store'])->name('beautyflow.register');

// Личный кабинет
Route::get('/account/login', [\App\Http\Controllers\Landing\AccountController::class, 'showLogin'])->name('landing.account.login');
Route::post('/account/login', [\App\Http\Controllers\Landing\AccountController::class, 'login'])->name('landing.account.login.post');

// Маршрут для сброса пароля (доступен всем)
Route::post('/account/password/email', [\App\Http\Controllers\Landing\AccountController::class, 'sendPasswordResetLink'])->name('landing.account.password.email');

Route::middleware('landing.auth')->group(function () {
    Route::get('/account/dashboard', [\App\Http\Controllers\Landing\AccountController::class, 'dashboard'])->name('landing.account.dashboard');
    Route::get('/account/profile', [\App\Http\Controllers\Landing\AccountController::class, 'profile'])->name('landing.account.profile');
    Route::post('/account/profile', [\App\Http\Controllers\Landing\AccountController::class, 'updateProfile'])->name('landing.account.profile.update');
    Route::post('/account/password', [\App\Http\Controllers\Landing\AccountController::class, 'updatePassword'])->name('landing.account.password.update');
    Route::get('/account/crm', [\App\Http\Controllers\Landing\AccountController::class, 'goToCrm'])->name('landing.account.crm');
    Route::get('/account/logout', [\App\Http\Controllers\Landing\AccountController::class, 'logout'])->name('landing.account.logout');
});

// Маршруты для платежей
Route::middleware('landing.auth')->group(function () {
    Route::get('/payment/form', [\App\Http\Controllers\Landing\PaymentController::class, 'showPaymentForm'])->name('landing.payment.form');
    Route::post('/payment/create', [\App\Http\Controllers\Landing\PaymentController::class, 'createPayment'])->name('landing.payment.create');
    Route::get('/payment/success', [\App\Http\Controllers\Landing\PaymentController::class, 'paymentSuccess'])->name('landing.payment.success');
    Route::get('/payment/failure', [\App\Http\Controllers\Landing\PaymentController::class, 'paymentFailure'])->name('landing.payment.failure');
    Route::post('/payment/webhook', [\App\Http\Controllers\Landing\PaymentController::class, 'webhook'])->name('landing.payment.webhook');
}); 