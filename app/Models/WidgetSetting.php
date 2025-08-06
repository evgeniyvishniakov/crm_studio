<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WidgetSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'widget_enabled',
        'widget_button_text',
        'widget_button_color',
        'widget_position',
        'widget_size',
        'widget_animation_enabled',
        'widget_animation_type',
        'widget_animation_duration',
        'widget_border_radius',
        'widget_text_color',
    ];

    protected $casts = [
        'widget_enabled' => 'boolean',
        'widget_animation_enabled' => 'boolean',
        'widget_animation_duration' => 'integer',
        'widget_border_radius' => 'integer',
    ];

    /**
     * Связь с проектом
     */
    public function project()
    {
        return $this->belongsTo(\App\Models\Admin\Project::class);
    }
}
