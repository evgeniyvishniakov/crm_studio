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
        Schema::table('knowledge_articles', function (Blueprint $table) {
            // Удаляем поле content, так как контент теперь хранится в переводах
            $table->dropColumn('content');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('knowledge_articles', function (Blueprint $table) {
            // Восстанавливаем поле content
            $table->longText('content')->nullable();
        });
    }
};
