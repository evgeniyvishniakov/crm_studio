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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');                    // "До 2 сотрудников", "До 5 сотрудников", "Без лимита"
            $table->string('slug')->unique();           // "small", "medium", "unlimited"
            $table->integer('max_employees');           // 2, 5, null (без лимита)
            $table->decimal('price_monthly', 10, 2);   // 490.00, 990.00, 1990.00
            $table->text('description')->nullable();    // Описание тарифа
            $table->json('features')->nullable();       // Дополнительные возможности
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);  // Порядок сортировки
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
