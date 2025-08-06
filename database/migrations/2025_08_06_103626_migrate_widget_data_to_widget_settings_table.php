<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Переносим данные из таблицы projects в widget_settings
        $projects = DB::table('projects')->get();
        
        foreach ($projects as $project) {
            DB::table('widget_settings')->insert([
                'project_id' => $project->id,
                'widget_enabled' => $project->widget_enabled ?? false,
                'widget_button_text' => $project->widget_button_text ?? 'Записаться',
                'widget_button_color' => $project->widget_button_color ?? '#007bff',
                'widget_position' => $project->widget_position ?? 'bottom-right',
                'widget_size' => $project->widget_size ?? 'medium',
                'widget_animation_enabled' => $project->widget_animation_enabled ?? true,
                'widget_animation_type' => $project->widget_animation_type ?? 'scale',
                'widget_animation_duration' => $project->widget_animation_duration ?? 300,
                'widget_border_radius' => $project->widget_border_radius ?? 25,
                'widget_text_color' => $project->widget_text_color ?? '#ffffff',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Удаляем все записи из widget_settings
        DB::table('widget_settings')->truncate();
    }
};
