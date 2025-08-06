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
            ->with('widgetSettings')
            ->get()
            ->first(function($project) use ($slug) {
                return $project->slug === $slug;
            });

        if (!$project) {
            return response()->json([
                'success' => false,
                'message' => 'Проект не найден'
            ], 404);
        }

        // Получаем настройки виджета
        $widgetSettings = $project->getOrCreateWidgetSettings();

        if (!$widgetSettings->widget_enabled) {
            return response()->json([
                'success' => false,
                'message' => 'Виджет отключен'
            ], 404);
        }

        // Возвращаем конфигурацию виджета
        return response()->json([
            'success' => true,
            'config' => [
                'button_text' => $widgetSettings->widget_button_text,
                'button_color' => $widgetSettings->widget_button_color,
                'text_color' => $widgetSettings->widget_text_color,
                'position' => $widgetSettings->widget_position,
                'size' => $widgetSettings->widget_size,
                'border_radius' => $widgetSettings->widget_border_radius,
                'animation_enabled' => $widgetSettings->widget_animation_enabled,
                'animation_type' => $widgetSettings->widget_animation_type,
                'animation_duration' => $widgetSettings->widget_animation_duration,
                'project_name' => $project->project_name,
                'project_slug' => $project->slug
            ]
        ]);
    }
}