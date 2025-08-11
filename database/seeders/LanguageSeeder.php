<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Language;

class LanguageSeeder extends Seeder
{
    public function run(): void
    {
        $languages = [
            [
                'code' => 'ru',
                'name' => 'Русский',
                'native_name' => 'Русский',
                'flag' => 'ru.png',
                'is_active' => true,
                'is_default' => true
            ],
            [
                'code' => 'en',
                'name' => 'English',
                'native_name' => 'English',
                'flag' => 'en.png',
                'is_active' => true,
                'is_default' => false
            ],
            [
                'code' => 'ua',
                'name' => 'Українська',
                'native_name' => 'Українська',
                'flag' => 'ua.png',
                'is_active' => true,
                'is_default' => false
            ]
        ];

        foreach ($languages as $language) {
            Language::updateOrCreate(
                ['code' => $language['code']],
                $language
            );
        }

        $this->command->info('Языки успешно добавлены!');
    }
}
