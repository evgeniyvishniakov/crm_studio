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
        // Делаем product_id nullable в warehouse
        Schema::table('warehouse', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->change();
        });

        // Делаем product_id nullable в sale_items
        Schema::table('sale_items', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->change();
        });

        // Делаем product_id nullable в purchase_items (если есть)
        if (Schema::hasTable('purchase_items')) {
            Schema::table('purchase_items', function (Blueprint $table) {
                $table->foreignId('product_id')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Возвращаем product_id как NOT NULL в warehouse
        Schema::table('warehouse', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable(false)->change();
        });

        // Возвращаем product_id как NOT NULL в sale_items
        Schema::table('sale_items', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable(false)->change();
        });

        // Возвращаем product_id как NOT NULL в purchase_items (если есть)
        if (Schema::hasTable('purchase_items')) {
            Schema::table('purchase_items', function (Blueprint $table) {
                $table->foreignId('product_id')->nullable(false)->change();
            });
        }
    }
};
