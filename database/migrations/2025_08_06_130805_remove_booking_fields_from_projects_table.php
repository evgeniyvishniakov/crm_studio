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
            // Удаляем поля бронирования из таблицы projects
            $table->dropColumn([
                'booking_enabled',
                'booking_url'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Восстанавливаем поля бронирования в таблице projects
            $table->boolean('booking_enabled')->default(false)->after('status');
            $table->string('booking_url')->nullable()->after('booking_enabled');
        });
    }
};
