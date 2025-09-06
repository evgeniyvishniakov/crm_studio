<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('blog_articles', function (Blueprint $table) {
            $table->foreignId('blog_category_id')->nullable()->constrained('blog_categories')->onDelete('set null')->after('id');
        });
        
        // Копируем данные из старой колонки в новую
        DB::statement('UPDATE blog_articles SET blog_category_id = category_id WHERE category_id IS NOT NULL');
        
        Schema::table('blog_articles', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blog_articles', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->constrained('blog_categories')->onDelete('set null')->after('id');
        });
        
        // Копируем данные из новой колонки в старую
        DB::statement('UPDATE blog_articles SET category_id = blog_category_id WHERE blog_category_id IS NOT NULL');
        
        Schema::table('blog_articles', function (Blueprint $table) {
            $table->dropForeign(['blog_category_id']);
            $table->dropColumn('blog_category_id');
        });
    }
};
