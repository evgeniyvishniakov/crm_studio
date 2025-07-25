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
        Schema::create('booking_links', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Название ссылки
            $table->string('slug')->unique(); // Уникальный slug для URL
            $table->text('description')->nullable(); // Описание
            $table->unsignedBigInteger('project_id'); // ID проекта
            $table->boolean('is_active')->default(true); // Активна ли ссылка
            $table->timestamps();

            // Внешний ключ на проекты
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            
            // Индексы
            $table->index(['project_id', 'is_active']);
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_links');
    }
};
