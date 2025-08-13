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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained('subscriptions')->onDelete('cascade');
            $table->decimal('amount', 10, 2);           // Сумма платежа
            $table->string('currency', 3)->default('UAH'); // Валюта
            $table->string('status');                   // pending, completed, failed, refunded
            $table->string('payment_method')->nullable(); // stripe, liqpay, paypal
            $table->string('external_id')->nullable();   // ID от платежной системы
            $table->json('metadata')->nullable();        // Дополнительные данные
            $table->timestamp('paid_at')->nullable();    // Дата успешной оплаты
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
