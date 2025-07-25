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
        Schema::create('user_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // ID мастера
            $table->integer('day_of_week'); // День недели (0-6, где 0 = воскресенье)
            $table->time('start_time'); // Время начала работы
            $table->time('end_time'); // Время окончания работы
            $table->boolean('is_working')->default(true); // Работает ли в этот день
            $table->text('notes')->nullable(); // Заметки
            $table->timestamps();

            // Внешний ключ на пользователей
            $table->foreign('user_id')->references('id')->on('admin_users')->onDelete('cascade');
            
            // Уникальный индекс для комбинации пользователь-день
            $table->unique(['user_id', 'day_of_week']);
            
            // Индексы
            $table->index(['user_id', 'is_working']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_schedules');
    }
};
