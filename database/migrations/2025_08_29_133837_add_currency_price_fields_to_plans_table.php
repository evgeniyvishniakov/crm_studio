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
        Schema::table('plans', function (Blueprint $table) {
            // Добавляем поля для цен в разных валютах
            $table->decimal('price_monthly_uah', 10, 2)->nullable()->after('price_monthly');
            $table->decimal('price_quarterly_uah', 10, 2)->nullable()->after('price_quarterly');
            $table->decimal('price_six_months_uah', 10, 2)->nullable()->after('price_six_months');
            $table->decimal('price_yearly_uah', 10, 2)->nullable()->after('price_yearly');
            
            $table->decimal('price_monthly_usd', 10, 2)->nullable()->after('price_monthly_uah');
            $table->decimal('price_quarterly_usd', 10, 2)->nullable()->after('price_quarterly_uah');
            $table->decimal('price_six_months_usd', 10, 2)->nullable()->after('price_six_months_uah');
            $table->decimal('price_yearly_usd', 10, 2)->nullable()->after('price_yearly_uah');
            
            // Добавляем поля для других валют (будут добавляться динамически)
            // Например, для польского злотого:
            $table->decimal('price_monthly_pln', 10, 2)->nullable()->after('price_yearly_usd');
            $table->decimal('price_quarterly_pln', 10, 2)->nullable()->after('price_monthly_pln');
            $table->decimal('price_six_months_pln', 10, 2)->nullable()->after('price_quarterly_pln');
            $table->decimal('price_yearly_pln', 10, 2)->nullable()->after('price_six_months_pln');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            // Удаляем все добавленные поля валют
            $table->dropColumn([
                'price_monthly_uah', 'price_quarterly_uah', 'price_six_months_uah', 'price_yearly_uah',
                'price_monthly_usd', 'price_quarterly_usd', 'price_six_months_usd', 'price_yearly_usd',
                'price_monthly_pln', 'price_quarterly_pln', 'price_six_months_pln', 'price_yearly_pln'
            ]);
        });
    }
};
