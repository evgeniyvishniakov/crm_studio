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
        Schema::create('booking_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id'); // ID проекта
            $table->integer('booking_interval')->default(30); // Интервал записи в минутах
            $table->time('working_hours_start')->default('09:00:00'); // Начало рабочего дня
            $table->time('working_hours_end')->default('18:00:00'); // Конец рабочего дня
            $table->integer('advance_booking_days')->default(30); // За сколько дней можно записаться
            $table->boolean('allow_same_day_booking')->default(true); // Разрешить запись в тот же день
            $table->boolean('require_confirmation')->default(false); // Требовать подтверждение
            $table->text('booking_instructions')->nullable(); // Инструкции для клиентов
            $table->timestamps();

            // Внешний ключ на проекты
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            
            // Уникальный индекс для проекта (один проект - одни настройки)
            $table->unique('project_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_settings');
    }
};
