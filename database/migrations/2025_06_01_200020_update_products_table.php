<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Добавляем новые колонки
            $table->foreignId('category_id')->after('name')->constrained('product_categories');
            $table->foreignId('brand_id')->after('category_id')->constrained('product_brands');
            
            // Удаляем старые колонки
            $table->dropColumn(['category', 'brand']);
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Восстанавливаем старые колонки
            $table->string('category')->after('name');
            $table->string('brand')->after('category');
            
            // Удаляем новые колонки
            $table->dropForeign(['category_id']);
            $table->dropForeign(['brand_id']);
            $table->dropColumn(['category_id', 'brand_id']);
        });
    }
}; 