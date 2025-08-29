<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'products/import*',
        'products/import/preview',
        'products/import',
        'products/analyze-name',
        '/products/import*',
        '/products/import/preview',
        '/products/import',
        '/products/analyze-name',
        'api/languages/set/*',
    ];
}
