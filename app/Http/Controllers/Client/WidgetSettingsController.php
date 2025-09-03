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
        
        // Получаем или создаем настройки виджета
        $widgetSettings = $project->getOrCreateWidgetSettings();
        
        // Преобразуем в формат для view
        $widgetSettingsArray = [
            'enabled' => $widgetSettings->widget_enabled,
            'button_text' => $widgetSettings->widget_button_text,
            'button_color' => $widgetSettings->widget_button_color,
            'position' => $widgetSettings->widget_position,
            'size' => $widgetSettings->widget_size,
            'animation_enabled' => $widgetSettings->widget_animation_enabled,
            'animation_type' => $widgetSettings->widget_animation_type,
            'animation_duration' => $widgetSettings->widget_animation_duration,
            'border_radius' => $widgetSettings->widget_border_radius,
            'text_color' => $widgetSettings->widget_text_color,
        ];
        
        return view('client.widget-settings.index', compact('project', 'widgetSettings', 'widgetSettingsArray'));
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
        
        // Получаем или создаем настройки виджета
        $widgetSettings = $project->getOrCreateWidgetSettings();
        
        // Обновляем настройки виджета
        $widgetSettings->update($validated);
        
        return response()->json([
            'success' => true,
            'message' => __('messages.widget_settings_saved')
        ]);
    }
    
    public function generateCode()
    {
        $user = Auth::guard('client')->user();
        $project = $user->project;
        $widgetSettings = $project->getOrCreateWidgetSettings();
        
        if (!$widgetSettings->widget_enabled) {
            return response()->json([
                'success' => false,
                'message' => __('messages.widget_not_enabled')
            ]);
        }
        
        // Генерируем код для вставки на сайт
        $widgetCode = $this->generateWidgetCode($project, $widgetSettings);
        
        // Определяем, является ли позиция inline для отладки
        $isInline = in_array($widgetSettings->widget_position, ['inline-left', 'inline-center', 'inline-right']);
        
        return response()->json([
            'success' => true,
            'code' => $widgetCode,
            'debug' => [
                'position' => $widgetSettings->widget_position,
                'is_inline' => $isInline
            ]
        ]);
    }
    
    private function generateWidgetCode($project, $widgetSettings)
    {
        // Определяем, является ли позиция inline
        $isInline = in_array($widgetSettings->widget_position, ['inline-left', 'inline-center', 'inline-right']);
        
        // Отладочная информация
        \Log::info('Widget position: ' . $widgetSettings->widget_position);
        \Log::info('Is inline: ' . ($isInline ? 'true' : 'false'));
        
        if ($isInline) {
            // Для inline позиций создаем простой код для вставки
            return "
" . __('messages.widget_booking_inline_comment') . "
" . __('messages.widget_booking_inline_instruction') . "
<script src=\"http://127.0.0.1:8000/widget-loader.js\" data-project=\"{$project->slug}\"></script>
<span id=\"booking-widget-inline\"></span>

" . __('messages.widget_booking_button_comment') . "
<button onclick=\"openBookingModal()\" style='
    background-color: {$widgetSettings->widget_button_color};
    color: {$widgetSettings->widget_text_color};
    border: none;
    border-radius: {$widgetSettings->widget_border_radius}px;
    font-weight: 600;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    {$this->getPositionStyles($widgetSettings->widget_position)}
    {$this->getSizeStyles($widgetSettings->widget_size)}
    {$this->getAnimationStyles($widgetSettings)}
    {$this->getAnimationJavaScript($widgetSettings)}
'>
    {$widgetSettings->widget_button_text}
</button>";
        } else {
            // Для фиксированных позиций создаем простой код
            return "
" . __('messages.widget_booking_code_comment') . "
" . __('messages.widget_booking_code_instruction') . "
<script src=\"http://127.0.0.1:8000/widget-loader.js\" data-project=\"{$project->slug}\"></script>

" . __('messages.widget_booking_manual_comment') . "
<script>
    " . __('messages.widget_booking_manual_instruction') . "
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
    
    private function getAnimationStyles($widgetSettings)
    {
        if (!$widgetSettings->widget_animation_enabled || $widgetSettings->widget_animation_type === 'none') {
            return '';
        }
        
        $duration = $widgetSettings->widget_animation_duration . 'ms';
        
        switch ($widgetSettings->widget_animation_type) {
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
    
    private function getAnimationJavaScript($widgetSettings)
    {
        if (!$widgetSettings->widget_animation_enabled || $widgetSettings->widget_animation_type === 'none') {
            return '';
        }
        
        $duration = $widgetSettings->widget_animation_duration;
        
        switch ($widgetSettings->widget_animation_type) {
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