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
            // Поля для настроек виджета
            $table->boolean('widget_enabled')->default(false)->after('booking_enabled');
            $table->string('widget_button_text', 50)->default('Записаться')->after('widget_enabled');
            $table->string('widget_button_color', 7)->default('#007bff')->after('widget_button_text');
            $table->enum('widget_position', ['bottom-right', 'bottom-left', 'top-right', 'top-left'])->default('bottom-right')->after('widget_button_color');
            $table->enum('widget_size', ['small', 'medium', 'large'])->default('medium')->after('widget_position');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'widget_enabled',
                'widget_button_text', 
                'widget_button_color',
                'widget_position',
                'widget_size'
            ]);
        });
    }
};
