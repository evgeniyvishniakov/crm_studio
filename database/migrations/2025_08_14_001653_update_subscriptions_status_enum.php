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
        // Сначала удаляем существующий enum
        DB::statement("ALTER TABLE subscriptions MODIFY COLUMN status VARCHAR(20)");
        
        // Теперь добавляем новые значения в enum
        DB::statement("ALTER TABLE subscriptions MODIFY COLUMN status ENUM('trial', 'active', 'expired', 'cancelled', 'pending') DEFAULT 'trial'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Возвращаем старый enum
        DB::statement("ALTER TABLE subscriptions MODIFY COLUMN status ENUM('trial', 'active', 'expired', 'cancelled') DEFAULT 'trial'");
    }
};
