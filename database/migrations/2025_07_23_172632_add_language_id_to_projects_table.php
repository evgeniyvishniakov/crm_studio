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
        Schema::table('projects', function (Blueprint $table) {
            $table->unsignedBigInteger('language_id')->nullable()->after('currency_id');
        });

        // Устанавливаем русский язык по умолчанию для всех проектов
        $russianLanguage = DB::table('languages')->where('code', 'ru')->first();
        if ($russianLanguage) {
            DB::table('projects')->update(['language_id' => $russianLanguage->id]);
        }

        // Добавляем внешний ключ
        Schema::table('projects', function (Blueprint $table) {
            $table->foreign('language_id')->references('id')->on('languages')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['language_id']);
            $table->dropColumn('language_id');
        });
    }
};
