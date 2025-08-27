<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SystemSetting;

class SystemSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SystemSetting::firstOrCreate(
            ['id' => 1],
            [
                'site_name' => 'CRM Studio',
                'site_description' => 'Система управления клиентами и записями',
                'admin_email' => 'admin@example.com',
                'timezone' => 'Europe/Moscow',
                'landing_logo' => null,
                'favicon' => null,
            ]
        );
    }
}



