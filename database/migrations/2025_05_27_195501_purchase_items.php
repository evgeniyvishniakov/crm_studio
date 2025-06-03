<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained();
            $table->decimal('purchase_price', 10, 2);
            $table->decimal('retail_price', 10, 2);
            $table->integer('quantity');
            $table->decimal('total', 12, 2);
            $table->timestamps();

            // Индексы для часто используемых полей
            $table->index('purchase_id');
            $table->index('product_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('purchase_items');
    }
};
