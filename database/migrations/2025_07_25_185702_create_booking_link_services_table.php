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
        Schema::create('booking_link_services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_link_id'); // ID ссылки бронирования
            $table->unsignedBigInteger('service_id'); // ID услуги
            $table->timestamps();

            // Внешние ключи
            $table->foreign('booking_link_id')->references('id')->on('booking_links')->onDelete('cascade');
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
            
            // Уникальный индекс для предотвращения дублирования
            $table->unique(['booking_link_id', 'service_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_link_services');
    }
};
