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
        // Отключаем CASCADE DELETE для warehouse
        Schema::table('warehouse', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
        });

        // Отключаем CASCADE DELETE для sale_items
        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
        });

        // Отключаем CASCADE DELETE для purchase_items (если есть)
        if (Schema::hasTable('purchase_items')) {
            Schema::table('purchase_items', function (Blueprint $table) {
                $table->dropForeign(['product_id']);
                $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Восстанавливаем CASCADE DELETE для warehouse
        Schema::table('warehouse', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
        });

        // Восстанавливаем CASCADE DELETE для sale_items
        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });

        // Восстанавливаем CASCADE DELETE для purchase_items (если есть)
        if (Schema::hasTable('purchase_items')) {
            Schema::table('purchase_items', function (Blueprint $table) {
                $table->dropForeign(['product_id']);
                $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
            });
        }
    }
};
