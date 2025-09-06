<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Helpers\LanguageHelper;
use Illuminate\Http\Request;

class LanguageUrlController extends Controller
{
    /**
     * Генерирует правильный URL для указанного языка
     */
    public function generateUrl(Request $request)
    {
        $request->validate([
            'route' => 'required|string',
            'parameters' => 'array',
            'language' => 'required|string|in:ru,en,ua'
        ]);

        $route = $request->input('route');
        $parameters = $request->input('parameters', []);
        $language = $request->input('language');

        try {
            $url = LanguageHelper::createSeoUrl($route, $parameters, $language);
            
            return response()->json([
                'success' => true,
                'url' => $url
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
