<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Удаляем поля виджета из таблицы projects
            $table->dropColumn([
                'widget_enabled',
                'widget_button_text',
                'widget_button_color',
                'widget_position',
                'widget_size',
                'widget_animation_enabled',
                'widget_animation_type',
                'widget_animation_duration',
                'widget_border_radius',
                'widget_text_color'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Восстанавливаем поля виджета в таблице projects
            $table->boolean('widget_enabled')->default(false)->after('booking_enabled');
            $table->string('widget_button_text', 50)->default('Записаться')->after('widget_enabled');
            $table->string('widget_button_color', 7)->default('#007bff')->after('widget_button_text');
            $table->enum('widget_position', ['bottom-right', 'bottom-left', 'top-right', 'top-left', 'center', 'inline-left', 'inline-center', 'inline-right'])->default('bottom-right')->after('widget_button_color');
            $table->enum('widget_size', ['small', 'medium', 'large'])->default('medium')->after('widget_position');
            $table->boolean('widget_animation_enabled')->default(true)->after('widget_size');
            $table->enum('widget_animation_type', ['scale', 'bounce', 'pulse', 'shake', 'none'])->default('scale')->after('widget_animation_enabled');
            $table->integer('widget_animation_duration')->default(300)->after('widget_animation_type');
            $table->integer('widget_border_radius')->default(25)->after('widget_animation_duration');
            $table->string('widget_text_color', 7)->default('#ffffff')->after('widget_border_radius');
        });
    }
};
