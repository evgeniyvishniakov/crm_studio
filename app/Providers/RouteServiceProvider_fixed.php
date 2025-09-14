<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public const HOME = '/';

    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            // Маршруты лендинга (только для landing домена)
            Route::middleware(['web'])
                ->domain(config('domains.landing'))
                ->group(base_path('routes/landing.php'));

            // Клиентская часть (только для CRM домена)
            Route::middleware('web')
                ->domain(config('domains.crm'))
                ->group(base_path('routes/client.php'));

            // Админка (только для panel домена) - БЕЗ префикса /panel
            Route::middleware('web')
                ->domain(config('domains.panel'))
                ->group(base_path('routes/admin.php'));

            // Web маршруты (только для CRM домена)
            Route::middleware('web')
                ->domain(config('domains.crm'))
                ->group(base_path('routes/web.php'));
        });
    }
}



