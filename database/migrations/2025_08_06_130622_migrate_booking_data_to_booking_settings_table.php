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
        // Обновляем существующие записи в booking_settings
        $bookingSettings = DB::table('booking_settings')->get();
        
        foreach ($bookingSettings as $setting) {
            $project = DB::table('projects')->where('id', $setting->project_id)->first();
            
            if ($project) {
                DB::table('booking_settings')
                    ->where('id', $setting->id)
                    ->update([
                        'booking_enabled' => $project->booking_enabled ?? false,
                        'booking_url' => $project->booking_url ?? null,
                    ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Восстанавливаем данные в таблице projects
        $bookingSettings = DB::table('booking_settings')->get();
        
        foreach ($bookingSettings as $setting) {
            DB::table('projects')
                ->where('id', $setting->project_id)
                ->update([
                    'booking_enabled' => $setting->booking_enabled,
                    'booking_url' => $setting->booking_url,
                ]);
        }
    }
};
