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
        Schema::create('payment_settings', function (Blueprint $table) {
            $table->id();
            $table->string('payment_method'); // liqpay, stripe, paypal
            $table->string('name'); // Название платежной системы
            $table->boolean('is_active')->default(false);
            $table->json('settings'); // Настройки в JSON формате
            $table->timestamps();
            
            $table->unique('payment_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_settings');
    }
};
