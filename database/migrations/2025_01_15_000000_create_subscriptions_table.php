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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id'); // Ссылка на проект (салон)
            $table->unsignedBigInteger('admin_user_id'); // Ссылка на админа проекта
            $table->string('plan_type'); // Тип тарифа (trial, basic, premium, enterprise)
            $table->decimal('amount', 10, 2); // Сумма оплаты
            $table->string('currency', 3)->default('USD'); // Валюта
            $table->timestamp('paid_at'); // Дата оплаты
            $table->timestamp('starts_at'); // Дата начала подписки
            $table->timestamp('trial_ends_at')->nullable(); // Дата окончания пробного периода
            $table->timestamp('expires_at'); // Дата окончания подписки
            $table->enum('status', ['trial', 'active', 'expired', 'cancelled'])->default('trial'); // Статус
            $table->text('notes')->nullable(); // Заметки
            $table->timestamps();

            // Внешние ключи
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('admin_user_id')->references('id')->on('admin_users')->onDelete('cascade');
            
            // Индексы
            $table->index(['project_id', 'status']);
            $table->index(['status', 'expires_at']);
            $table->index(['admin_user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
