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
        Schema::create('booking_link_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_link_id'); // ID ссылки бронирования
            $table->unsignedBigInteger('user_id'); // ID мастера
            $table->timestamps();

            // Внешние ключи
            $table->foreign('booking_link_id')->references('id')->on('booking_links')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('admin_users')->onDelete('cascade');
            
            // Уникальный индекс для предотвращения дублирования
            $table->unique(['booking_link_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_link_users');
    }
};
