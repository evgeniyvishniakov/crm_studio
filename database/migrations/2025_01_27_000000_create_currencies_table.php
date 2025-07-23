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
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique(); // UAH, USD, EUR, etc.
            $table->string('name'); // Украинская гривна, US Dollar, Euro
            $table->string('symbol', 10); // ₴, $, €
            $table->string('symbol_position', 10)->default('after'); // before, after
            $table->integer('decimal_places')->default(2);
            $table->string('decimal_separator', 1)->default('.');
            $table->string('thousands_separator', 1)->default(',');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
}; 