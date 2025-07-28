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
            $table->unsignedBigInteger('booking_language_id')->nullable()->after('language_id');
        });

        // Устанавливаем язык веб-записи равным основному языку проекта по умолчанию
        $projects = DB::table('projects')->whereNotNull('language_id')->get();
        foreach ($projects as $project) {
            DB::table('projects')
                ->where('id', $project->id)
                ->update(['booking_language_id' => $project->language_id]);
        }

        // Добавляем внешний ключ
        Schema::table('projects', function (Blueprint $table) {
            $table->foreign('booking_language_id')->references('id')->on('languages')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['booking_language_id']);
            $table->dropColumn('booking_language_id');
        });
    }
};
