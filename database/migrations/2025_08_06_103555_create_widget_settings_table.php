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
        Schema::create('widget_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->boolean('widget_enabled')->default(false);
            $table->string('widget_button_text', 50)->default('Записаться');
            $table->string('widget_button_color', 7)->default('#007bff');
            $table->enum('widget_position', ['bottom-right', 'bottom-left', 'top-right', 'top-left', 'center', 'inline-left', 'inline-center', 'inline-right'])->default('bottom-right');
            $table->enum('widget_size', ['small', 'medium', 'large'])->default('medium');
            $table->boolean('widget_animation_enabled')->default(true);
            $table->enum('widget_animation_type', ['scale', 'bounce', 'pulse', 'shake', 'none'])->default('scale');
            $table->integer('widget_animation_duration')->default(300);
            $table->integer('widget_border_radius')->default(25);
            $table->string('widget_text_color', 7)->default('#ffffff');
            $table->timestamps();

            // Внешний ключ к таблице projects
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            
            // Уникальный индекс для project_id (один проект - одни настройки виджета)
            $table->unique('project_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('widget_settings');
    }
};
