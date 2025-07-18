<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RateLimitMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $type = 'default'): Response
    {
        $user = Auth::user();
        $key = $this->resolveRequestSignature($request, $user, $type);
        
        // Настройки rate limiting в зависимости от типа
        $limits = $this->getLimits($type);
        
        if (RateLimiter::tooManyAttempts($key, $limits['max_attempts'])) {
            $seconds = RateLimiter::availableIn($key);
            
            return response()->json([
                'error' => 'Слишком много запросов. Попробуйте через ' . $seconds . ' секунд.',
                'retry_after' => $seconds
            ], 429);
        }
        
        RateLimiter::hit($key, $limits['decay_minutes'] * 60);
        
        $response = $next($request);
        
        return $response->header('X-RateLimit-Limit', $limits['max_attempts'])
                       ->header('X-RateLimit-Remaining', RateLimiter::remaining($key, $limits['max_attempts']));
    }
    
    /**
     * Получить настройки лимитов для разных типов запросов
     */
    private function getLimits(string $type): array
    {
        $config = config('security.rate_limits');
        
        return match($type) {
            'tickets' => [
                'max_attempts' => $config['tickets']['max_attempts'],
                'decay_minutes' => $config['tickets']['decay_minutes']
            ],
            'messages' => [
                'max_attempts' => $config['messages']['max_attempts'],
                'decay_minutes' => $config['messages']['decay_minutes']
            ],
            'notifications' => [
                'max_attempts' => $config['notifications']['max_attempts'],
                'decay_minutes' => $config['notifications']['decay_minutes']
            ],
            default => [
                'max_attempts' => 60,
                'decay_minutes' => 1
            ]
        };
    }
    
    /**
     * Создать уникальный ключ для rate limiting
     */
    private function resolveRequestSignature(Request $request, $user, string $type): string
    {
        $userId = $user ? $user->id : $request->ip();
        $route = $request->route() ? $request->route()->getName() : 'unknown';
        
        return sha1($userId . '|' . $route . '|' . $type);
    }
} 