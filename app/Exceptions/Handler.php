<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Auth\AuthenticationException;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
        
        // Обработка 404 ошибок
        $this->renderable(function (NotFoundHttpException $e, $request) {
            if ($request->is('admin/*')) {
                // Для админской части используем стандартную страницу 404
                return response()->view('errors.404', [], 404);
            } elseif ($request->is('book/*') || $request->is('knowledge/*')) {
                // Для публичных страниц используем стандартную страницу 404
                return response()->view('errors.404', [], 404);
            } else {
                // Для клиентской части используем нашу красивую страницу 404
                return response()->view('client.errors.404', [], 404);
            }
        });
        
        // Обработка ошибок "Call to a member function on null"
        $this->renderable(function (Throwable $e, $request) {
            if (str_contains($e->getMessage(), 'Call to a member function') && str_contains($e->getMessage(), 'on null')) {
                if ($request->is('admin/*')) {
                    return response()->view('errors.404', [], 404);
                } elseif ($request->is('book/*') || $request->is('knowledge/*')) {
                    return response()->view('errors.404', [], 404);
                } else {
                    return response()->view('client.errors.404', [], 404);
                }
            }
        });
    }

    // Удаляю глобальное логирование NotFoundHttpException (404)
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    public function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        return redirect()->guest(route('login'));
    }
}
