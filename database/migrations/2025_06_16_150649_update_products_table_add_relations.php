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
        Schema::table('products', function (Blueprint $table) {
            // Добавляем новые внешние ключи
            $table->foreignId('category_id')->nullable()->constrained('product_categories');
            $table->foreignId('brand_id')->nullable()->constrained('product_brands');

            // Удаляем старые колонки
            $table->dropColumn(['category', 'brand']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
