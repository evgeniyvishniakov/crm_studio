<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class WidgetController extends Controller
{
    /**
     * Получить конфигурацию виджета для проекта
     */
    public function getConfig($slug)
    {
        // Rate limiting для защиты от DDoS
        $key = 'widget-config:' . $slug . ':' . request()->ip();
        if (RateLimiter::tooManyAttempts($key, 60)) { // 60 запросов в минуту
            return response()->json([
                'success' => false,
                'message' => 'Слишком много запросов. Попробуйте позже.'
            ], 429);
        }
        RateLimiter::hit($key);

        // Валидация slug
        if (!preg_match('/^[a-z0-9-]+$/', $slug)) {
            return response()->json([
                'success' => false,
                'message' => 'Неверный формат идентификатора проекта'
            ], 400);
        }

        // Находим проект по slug
        $project = Project::where('booking_enabled', true)
            ->where('widget_enabled', true)
            ->get()
            ->first(function($project) use ($slug) {
                return $project->slug === $slug;
            });

        if (!$project) {
            return response()->json([
                'success' => false,
                'message' => 'Проект не найден или виджет отключен'
            ], 404);
        }

        // Возвращаем конфигурацию виджета
        return response()->json([
            'success' => true,
            'config' => [
                'button_text' => $project->widget_button_text ?? 'Записаться',
                'button_color' => $project->widget_button_color ?? '#007bff',
                'text_color' => $project->widget_text_color ?? '#ffffff',
                'position' => $project->widget_position ?? 'bottom-right',
                'size' => $project->widget_size ?? 'medium',
                'border_radius' => $project->widget_border_radius ?? 25,
                'animation_enabled' => $project->widget_animation_enabled ?? true,
                'animation_type' => $project->widget_animation_type ?? 'scale',
                'animation_duration' => $project->widget_animation_duration ?? 300,
                'project_name' => $project->project_name,
                'project_slug' => $project->slug
            ]
        ]);
    }
}