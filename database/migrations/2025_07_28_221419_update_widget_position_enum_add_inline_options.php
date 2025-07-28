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
        // Обновляем ENUM для widget_position, добавляя новые inline опции
        DB::statement("ALTER TABLE projects MODIFY COLUMN widget_position ENUM('bottom-right', 'bottom-left', 'top-right', 'top-left', 'center', 'inline-left', 'inline-center', 'inline-right') DEFAULT 'bottom-right'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Возвращаем ENUM к исходному состоянию
        DB::statement("ALTER TABLE projects MODIFY COLUMN widget_position ENUM('bottom-right', 'bottom-left', 'top-right', 'top-left') DEFAULT 'bottom-right'");
    }
};
