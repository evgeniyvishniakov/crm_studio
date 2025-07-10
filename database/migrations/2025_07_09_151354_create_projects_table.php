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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('logo')->nullable(); // Логотип
            $table->string('name'); // Название
            $table->string('email'); // Почта
            $table->timestamp('registered_at'); // Дата регистрации
            $table->string('language')->default('ua'); // Язык
            $table->string('status')->default('active'); // Статус (Активный)
            $table->string('phone')->nullable(); // Телефон
            $table->string('website')->nullable(); // Сайт
            $table->string('address')->nullable(); // Адрес
            $table->json('social_links')->nullable(); // Соцсети
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
