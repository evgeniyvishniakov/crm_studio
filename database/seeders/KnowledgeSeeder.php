<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\KnowledgeArticle;
use Illuminate\Support\Str;

class KnowledgeSeeder extends Seeder
{
    public function run(): void
    {
        $articles = [
            [
                'category' => 'getting-started',
                'title' => 'Настройка ролей и доступов',
                'description' => 'Пошаговая инструкция по настройке ролей и прав доступа для пользователей системы',
                'author' => 'Команда Trimora',
                'is_published' => true,
                'published_at' => now(),
                'sort_order' => 1
            ],
            [
                'category' => 'getting-started',
                'title' => 'Первые шаги в системе',
                'description' => 'Базовое руководство по началу работы с системой управления проектами',
                'author' => 'Команда Trimora',
                'is_published' => true,
                'published_at' => now(),
                'sort_order' => 2
            ],
            [
                'category' => 'features',
                'title' => 'Работа с клиентами',
                'description' => 'Подробное руководство по управлению клиентской базой и взаимодействию с клиентами',
                'author' => 'Команда Trimora',
                'is_published' => true,
                'published_at' => now(),
                'sort_order' => 3
            ],
            [
                'category' => 'features',
                'title' => 'Управление проектами',
                'description' => 'Инструкция по созданию и управлению проектами в системе',
                'author' => 'Команда Trimora',
                'is_published' => true,
                'published_at' => now(),
                'sort_order' => 4
            ],
            [
                'category' => 'advanced',
                'title' => 'Настройка интеграций',
                'description' => 'Руководство по настройке внешних интеграций и API',
                'author' => 'Команда Trimora',
                'is_published' => false,
                'published_at' => null,
                'sort_order' => 5
            ]
        ];

        foreach ($articles as $article) {
            $article['slug'] = Str::slug($article['title']);
            KnowledgeArticle::create($article);
        }

        $this->command->info('База знаний успешно заполнена!');
    }
}
