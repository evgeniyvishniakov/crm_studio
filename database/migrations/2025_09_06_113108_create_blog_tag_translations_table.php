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
        Schema::create('blog_tag_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_tag_id')->constrained()->onDelete('cascade');
            $table->string('locale', 5);
            $table->string('name');
            $table->timestamps();
            
            $table->unique(['blog_tag_id', 'locale']);
            $table->index('locale');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_tag_translations');
    }
};
