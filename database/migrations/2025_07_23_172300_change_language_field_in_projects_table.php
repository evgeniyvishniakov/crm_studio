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
        // Сначала создаем временное поле
        Schema::table('projects', function (Blueprint $table) {
            $table->unsignedBigInteger('language_id')->nullable()->after('language');
        });

        // Обновляем данные: конвертируем коды языков в ID
        $languages = DB::table('languages')->get();
        $languageMap = $languages->pluck('id', 'code')->toArray();

        $projects = DB::table('projects')->whereNotNull('language')->get();
        foreach ($projects as $project) {
            if (isset($languageMap[$project->language])) {
                DB::table('projects')
                    ->where('id', $project->id)
                    ->update(['language_id' => $languageMap[$project->language]]);
            }
        }

        // Удаляем старое поле и переименовываем новое
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('language');
        });

        // Переименовываем поле (MySQL требует отдельную операцию)
        DB::statement('ALTER TABLE projects CHANGE language_id language BIGINT UNSIGNED NULL');

        // Добавляем внешний ключ
        Schema::table('projects', function (Blueprint $table) {
            $table->foreign('language')->references('id')->on('languages')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Удаляем внешний ключ
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['language']);
        });

        // Создаем временное поле для кода
        Schema::table('projects', function (Blueprint $table) {
            $table->string('language_code', 5)->nullable()->after('language');
        });

        // Конвертируем ID обратно в коды
        $languages = DB::table('languages')->get();
        $languageMap = $languages->pluck('code', 'id')->toArray();

        $projects = DB::table('projects')->whereNotNull('language')->get();
        foreach ($projects as $project) {
            if (isset($languageMap[$project->language])) {
                DB::table('projects')
                    ->where('id', $project->id)
                    ->update(['language_code' => $languageMap[$project->language]]);
            }
        }

        // Удаляем поле с ID и переименовываем поле с кодом
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('language');
        });

        // Переименовываем поле (MySQL требует отдельную операцию)
        DB::statement('ALTER TABLE projects CHANGE language_code language VARCHAR(5) NULL');
    }
};
