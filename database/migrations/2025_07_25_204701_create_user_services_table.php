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
        Schema::create('user_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('admin_users')->onDelete('cascade');
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->boolean('is_active_for_booking')->default(true);
            $table->decimal('price', 10, 2)->nullable(); // Цена мастера за эту услугу (может отличаться от базовой)
            $table->integer('duration')->nullable(); // Длительность услуги у этого мастера в минутах
            $table->text('description')->nullable(); // Описание услуги у этого мастера
            $table->timestamps();
            
            // Уникальная связь пользователь-услуга
            $table->unique(['user_id', 'service_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_services');
    }
};
