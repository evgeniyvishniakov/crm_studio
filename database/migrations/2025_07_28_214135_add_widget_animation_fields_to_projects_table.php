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
            $table->boolean('widget_animation_enabled')->default(true)->after('widget_size');
            $table->enum('widget_animation_type', ['scale', 'bounce', 'pulse', 'shake', 'none'])->default('scale')->after('widget_animation_enabled');
            $table->integer('widget_animation_duration')->default(300)->after('widget_animation_type'); // в миллисекундах
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'widget_animation_enabled',
                'widget_animation_type',
                'widget_animation_duration'
            ]);
        });
    }
};
