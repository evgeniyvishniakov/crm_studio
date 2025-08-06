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
        // Получаем все проекты с telegram настройками
        $projects = DB::table('projects')->get();
        
        foreach ($projects as $project) {
            // Создаем запись в telegram_settings для каждого проекта
            DB::table('telegram_settings')->insert([
                'project_id' => $project->id,
                'telegram_bot_token' => $project->telegram_bot_token,
                'telegram_chat_id' => $project->telegram_chat_id,
                'telegram_notifications_enabled' => $project->telegram_notifications_enabled ?? false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Восстанавливаем данные в таблице projects
        $telegramSettings = DB::table('telegram_settings')->get();
        
        foreach ($telegramSettings as $setting) {
            DB::table('projects')
                ->where('id', $setting->project_id)
                ->update([
                    'telegram_bot_token' => $setting->telegram_bot_token,
                    'telegram_chat_id' => $setting->telegram_chat_id,
                    'telegram_notifications_enabled' => $setting->telegram_notifications_enabled,
                ]);
        }
    }
};
