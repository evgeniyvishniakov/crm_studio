<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Admin\Project;
use App\Helpers\CurrencyHelper;
use Illuminate\Support\Facades\Auth;

class SetCurrency
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Получаем валюту из проекта пользователя
        if (Auth::check()) {
            $user = Auth::user();
            $project = Project::where('id', $user->project_id ?? null)->first();
            
            if ($project && $project->currency) {
                // Устанавливаем валюту в сессию через хелпер
                CurrencyHelper::setCurrency($project->currency);
            } else {
                // По умолчанию UAH
                CurrencyHelper::setCurrency('UAH');
            }
        } else {
            // Для неавторизованных пользователей
            CurrencyHelper::setCurrency('UAH');
        }

        return $next($request);
    }
}
