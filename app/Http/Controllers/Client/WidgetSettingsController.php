<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WidgetSettingsController extends Controller
{
    public function index()
    {
        $user = Auth::guard('client')->user();
        $project = $user->project;
        
        // Получаем настройки виджета
        $widgetSettings = [
            'enabled' => $project->widget_enabled ?? false,
            'button_text' => $project->widget_button_text ?? 'Записаться',
            'button_color' => $project->widget_button_color ?? '#007bff',
            'position' => $project->widget_position ?? 'bottom-right',
            'size' => $project->widget_size ?? 'medium',
            'animation_enabled' => $project->widget_animation_enabled ?? true,
            'animation_type' => $project->widget_animation_type ?? 'scale',
            'animation_duration' => $project->widget_animation_duration ?? 300,
            'border_radius' => $project->widget_border_radius ?? 25,
            'text_color' => $project->widget_text_color ?? '#ffffff',
        ];
        
        return view('client.widget-settings.index', compact('project', 'widgetSettings'));
    }
    
    public function update(Request $request)
    {
        $user = Auth::guard('client')->user();
        $project = $user->project;
        
        $validated = $request->validate([
            'widget_enabled' => 'boolean',
            'widget_button_text' => 'required|string|max:50',
            'widget_button_color' => 'required|string|max:7',
            'widget_position' => 'required|in:bottom-right,bottom-left,top-right,top-left,center,inline-left,inline-center,inline-right',
            'widget_size' => 'required|in:small,medium,large',
            'widget_animation_enabled' => 'boolean',
            'widget_animation_type' => 'required|in:scale,bounce,pulse,shake,none',
            'widget_animation_duration' => 'required|integer|min:100|max:2000',
            'widget_border_radius' => 'required|integer|min:0|max:50',
            'widget_text_color' => 'required|string|max:7',
        ]);
        
        // Обновляем настройки проекта
        $project->update($validated);
        
        return response()->json([
            'success' => true,
            'message' => __('messages.widget_settings_saved')
        ]);
    }
    
    public function generateCode()
    {
        $user = Auth::guard('client')->user();
        $project = $user->project;
        
        if (!$project->widget_enabled) {
            return response()->json([
                'success' => false,
                'message' => __('messages.widget_not_enabled')
            ]);
        }
        
        // Генерируем код для вставки на сайт
        $widgetCode = $this->generateWidgetCode($project);
        
        // Определяем, является ли позиция inline для отладки
        $isInline = in_array($project->widget_position, ['inline-left', 'inline-center', 'inline-right']);
        
        return response()->json([
            'success' => true,
            'code' => $widgetCode,
            'debug' => [
                'position' => $project->widget_position,
                'is_inline' => $isInline
            ]
        ]);
    }
    
    private function generateWidgetCode($project)
    {
        // Определяем, является ли позиция inline
        $isInline = in_array($project->widget_position, ['inline-left', 'inline-center', 'inline-right']);
        
        // Отладочная информация
        \Log::info('Widget position: ' . $project->widget_position);
        \Log::info('Is inline: ' . ($isInline ? 'true' : 'false'));
        
        if ($isInline) {
            // Для inline позиций создаем простой код для вставки
            return "
<!-- Виджет записи для сайта (встраиваемый) -->
<!-- Просто вставьте этот код в нужное место на вашей странице -->
<script src=\"http://127.0.0.1:8000/widget-loader.js\" data-project=\"{$project->slug}\"></script>
<span id=\"booking-widget-inline\"></span>

<!-- Или используйте этот HTML код для кнопки: -->
<button onclick=\"openBookingModal()\" style='
    background-color: {$project->widget_button_color};
    color: {$project->widget_text_color};
    border: none;
    border-radius: {$project->widget_border_radius}px;
    font-weight: 600;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    {$this->getPositionStyles($project->widget_position)}
    {$this->getSizeStyles($project->widget_size)}
    {$this->getAnimationStyles($project)}
    {$this->getAnimationJavaScript($project)}
'>
    {$project->widget_button_text}
</button>";
        } else {
            // Для фиксированных позиций создаем простой код
            return "
<!-- Виджет записи для сайта -->
<!-- Просто вставьте этот код в &lt;head&gt; или перед закрывающим &lt;/body&gt; на вашей странице -->
<script src=\"http://127.0.0.1:8000/widget-loader.js\" data-project=\"{$project->slug}\"></script>

<!-- Или инициализируйте виджет вручную: -->
<script>
    // Инициализация виджета
    window.initBookingWidget('{$project->slug}');
</script>";
        }
    }
    
    private function getPositionStyles($position)
    {
        switch ($position) {
            case 'bottom-right':
                return 'bottom: 20px; right: 20px;';
            case 'bottom-left':
                return 'bottom: 20px; left: 20px;';
            case 'top-right':
                return 'top: 20px; right: 20px;';
            case 'top-left':
                return 'top: 20px; left: 20px;';
            case 'center':
                return 'top: 50%; left: 50%; transform: translate(-50%, -50%);';
            case 'inline-left':
                return 'position: relative; display: inline-block; margin: 10px 10px 10px 0;';
            case 'inline-center':
                return 'position: relative; display: block; margin: 10px auto; text-align: center;';
            case 'inline-right':
                return 'position: relative; display: inline-block; margin: 10px 0 10px 10px; float: right;';
            default:
                return 'bottom: 20px; right: 20px;';
        }
    }
    
    private function getSizeStyles($size)
    {
        switch ($size) {
            case 'small':
                return 'padding: 8px 16px; font-size: 14px;';
            case 'large':
                return 'padding: 16px 32px; font-size: 18px;';
            default:
                return 'padding: 12px 24px; font-size: 16px;';
        }
    }
    
    private function getAnimationStyles($project)
    {
        if (!$project->widget_animation_enabled || $project->widget_animation_type === 'none') {
            return '';
        }
        
        $duration = $project->widget_animation_duration . 'ms';
        
        switch ($project->widget_animation_type) {
            case 'scale':
                return "
                    transition: transform {$duration} ease;
                ";
            case 'bounce':
                return "
                    transition: transform {$duration} cubic-bezier(0.68, -0.55, 0.265, 1.55);
                ";
            case 'pulse':
                return "
                    transition: all {$duration} ease;
                ";
            case 'shake':
                return "
                    transition: transform {$duration} ease;
                ";
            default:
                return '';
        }
    }
    
    private function getAnimationJavaScript($project)
    {
        if (!$project->widget_animation_enabled || $project->widget_animation_type === 'none') {
            return '';
        }
        
        $duration = $project->widget_animation_duration;
        
        switch ($project->widget_animation_type) {
            case 'scale':
                return "
                    onmouseover='this.style.transform=\"scale(1.1)\"'
                    onmouseout='this.style.transform=\"scale(1)\"'
                ";
            case 'bounce':
                return "
                    onmouseover='this.style.transform=\"scale(1.1) translateY(-5px)\"'
                    onmouseout='this.style.transform=\"scale(1) translateY(0)\"'
                ";
            case 'pulse':
                return "
                    onmouseover='this.style.transform=\"scale(1.05)\"; this.style.boxShadow=\"0 6px 20px rgba(0,0,0,0.25)\"'
                    onmouseout='this.style.transform=\"scale(1)\"; this.style.boxShadow=\"0 4px 12px rgba(0,0,0,0.15)\"'
                ";
            case 'shake':
                return "
                    onmouseover='this.style.transform=\"translateX(5px)\"'
                    onmouseout='this.style.transform=\"translateX(0)\"'
                ";
            default:
                return '';
        }
    }
} 