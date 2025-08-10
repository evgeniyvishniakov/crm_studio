<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\KnowledgeArticle;
use App\Models\KnowledgeArticleStep;
use App\Models\KnowledgeArticleTip;

class KnowledgeStepsAndTipsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Находим статью "Настройка ролей и доступов"
        $article = KnowledgeArticle::where('title', 'Настройка ролей и доступов')->first();
        
        if ($article) {
            // Добавляем шаги
            $steps = [
                [
                    'title' => 'Переход в раздел "Роли и доступы"',
                    'content' => 'Откройте меню <strong>Настройки</strong> и выберите раздел <strong>Роли и доступы</strong>.',
                    'sort_order' => 1
                ],
                [
                    'title' => 'Создание ролей',
                    'content' => 'Нажмите кнопку <strong>Добавить роль</strong> и введите название роли (например: Администратор, Менеджер, Мастер и т.д.).',
                    'sort_order' => 2
                ],
                [
                    'title' => 'Настройка доступов',
                    'content' => 'Для каждой роли выберите пункты меню, к которым будет открыт доступ. Если доступ к определённому пункту меню закрыт, этот пункт всё равно будет отображаться в меню, но:<br><br><ul><li>рядом с ним будет значок замка;</li><li>он будет подсвечен тусклым цветом;</li><li>переход по нему будет невозможен (не кликабелен).</li></ul>',
                    'sort_order' => 3
                ]
            ];

            foreach ($steps as $step) {
                KnowledgeArticleStep::create([
                    'knowledge_article_id' => $article->id,
                    'title' => $step['title'],
                    'content' => $step['content'],
                    'sort_order' => $step['sort_order']
                ]);
            }

            // Добавляем полезные советы
            $tips = [
                'При создании ролей используйте понятные названия, которые отражают уровень доступа.',
                'Регулярно проверяйте права доступа пользователей и обновляйте их при необходимости.',
                'Создайте тестовую роль для проверки настроек перед применением к реальным пользователям.'
            ];

            foreach ($tips as $index => $tip) {
                KnowledgeArticleTip::create([
                    'knowledge_article_id' => $article->id,
                    'content' => $tip,
                    'sort_order' => $index + 1
                ]);
            }
        }

        $this->command->info('Шаги и полезные советы для статьи "Настройка ролей и доступов" успешно добавлены!');
    }
}
