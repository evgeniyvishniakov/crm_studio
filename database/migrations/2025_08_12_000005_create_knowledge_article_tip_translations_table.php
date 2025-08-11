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
        Schema::create('knowledge_article_tip_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('knowledge_article_tip_id')->constrained()->onDelete('cascade');
            $table->string('locale'); // Код языка (ru, en, ua)
            $table->text('content');
            $table->timestamps();
            
            // Уникальный индекс для предотвращения дублирования переводов
            $table->unique(['knowledge_article_tip_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('knowledge_article_tip_translations');
    }
};
