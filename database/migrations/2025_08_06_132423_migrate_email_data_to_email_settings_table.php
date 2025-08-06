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
        // Получаем все проекты с email настройками
        $projects = DB::table('projects')->get();
        
        foreach ($projects as $project) {
            // Создаем запись в email_settings для каждого проекта
            DB::table('email_settings')->insert([
                'project_id' => $project->id,
                'email_host' => $project->email_host,
                'email_port' => $project->email_port,
                'email_username' => $project->email_username,
                'email_password' => $project->email_password,
                'email_encryption' => $project->email_encryption ?? 'tls',
                'email_from_name' => $project->email_from_name,
                'email_notifications_enabled' => $project->email_notifications_enabled ?? false,
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
        $emailSettings = DB::table('email_settings')->get();
        
        foreach ($emailSettings as $setting) {
            DB::table('projects')
                ->where('id', $setting->project_id)
                ->update([
                    'email_host' => $setting->email_host,
                    'email_port' => $setting->email_port,
                    'email_username' => $setting->email_username,
                    'email_password' => $setting->email_password,
                    'email_encryption' => $setting->email_encryption,
                    'email_from_name' => $setting->email_from_name,
                    'email_notifications_enabled' => $setting->email_notifications_enabled,
                ]);
        }
    }
};
