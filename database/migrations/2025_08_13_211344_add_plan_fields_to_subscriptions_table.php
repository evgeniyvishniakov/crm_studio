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
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->foreignId('plan_id')->nullable()->constrained('plans')->onDelete('set null');
            $table->string('period_type')->default('monthly'); // monthly, quarterly, semiannual, yearly
            $table->decimal('amount_paid', 10, 2)->nullable(); // Фактически оплаченная сумма
            $table->decimal('discount_percent', 5, 2)->default(0); // Процент скидки
            $table->string('payment_status')->default('pending'); // pending, paid, failed
            $table->timestamp('current_period_start')->nullable(); // Начало текущего периода
            $table->timestamp('current_period_end')->nullable(); // Конец текущего периода
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropForeign(['plan_id']);
            $table->dropColumn([
                'plan_id', 'period_type', 'amount_paid', 'discount_percent',
                'payment_status', 'current_period_start', 'current_period_end'
            ]);
        });
    }
};
