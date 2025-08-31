<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\KnowledgeArticle;
use App\Models\KnowledgeArticleTranslation;

class KnowledgeTranslationsSeeder extends Seeder
{
    public function run(): void
    {
        // Получаем все существующие статьи
        $articles = KnowledgeArticle::all();
        
        foreach ($articles as $article) {
            // Английские переводы
            KnowledgeArticleTranslation::create([
                'knowledge_article_id' => $article->id,
                'locale' => 'en',
                'title' => $this->getEnglishTitle($article->title),
                'description' => $this->getEnglishDescription($article->description),
            ]);
            
            // Украинские переводы
            KnowledgeArticleTranslation::create([
                'knowledge_article_id' => $article->id,
                'locale' => 'ua',
                'title' => $this->getUkrainianTitle($article->title),
                'description' => $this->getUkrainianDescription($article->description),
            ]);
        }
        
        $this->command->info('Переводы статей базы знаний успешно созданы!');
    }
    
    private function getEnglishTitle($russianTitle)
    {
        $translations = [
            'Настройка ролей и доступов' => 'Setting up roles and permissions',
            'Первые шаги в системе' => 'First steps in the system',
            'Работа с клиентами' => 'Working with clients',
            'Управление проектами' => 'Project management',
            'Настройка интеграций' => 'Setting up integrations',
        ];
        
        return $translations[$russianTitle] ?? $russianTitle;
    }
    
    private function getEnglishDescription($russianDescription)
    {
        $translations = [
            'Пошаговая инструкция по настройке ролей и прав доступа для пользователей системы' => 'Step-by-step guide to setting up roles and permissions for system users',
            'Базовое руководство по началу работы с системой управления проектами' => 'Basic guide to getting started with the project management system',
            'Подробное руководство по управлению клиентской базой и взаимодействию с клиентами' => 'Detailed guide to managing the client database and interacting with clients',
            'Инструкция по созданию и управлению проектами в системе' => 'Instructions for creating and managing projects in the system',
            'Руководство по настройке внешних интеграций и API' => 'Guide to setting up external integrations and API',
        ];
        
        return $translations[$russianDescription] ?? $russianDescription;
    }
    
    private function getUkrainianTitle($russianTitle)
    {
        $translations = [
            'Настройка ролей и доступов' => 'Налаштування ролей та доступів',
            'Первые шаги в системе' => 'Перші кроки в системі',
            'Работа с клиентами' => 'Робота з клієнтами',
            'Управление проектами' => 'Управління проектами',
            'Настройка интеграций' => 'Налаштування інтеграцій',
        ];
        
        return $translations[$russianTitle] ?? $russianTitle;
    }
    
    private function getUkrainianDescription($russianDescription)
    {
        $translations = [
            'Пошаговая инструкция по настройке ролей и прав доступа для пользователей системы' => 'Покрокова інструкція з налаштування ролей та прав доступу для користувачів системи',
            'Базовое руководство по началу работы с системой управления проектами' => 'Базовий посібник з початку роботи з системою управління проектами',
            'Подробное руководство по управлению клиентской базой и взаимодействию с клиентами' => 'Детальний посібник з управління клієнтською базою та взаємодії з клієнтами',
            'Инструкция по созданию и управлению проектами в системе' => 'Інструкція з створення та управління проектами в системі',
            'Руководство по настройке внешних интеграций и API' => 'Посібник з налаштування зовнішніх інтеграцій та API',
        ];
        
        return $translations[$russianDescription] ?? $russianDescription;
    }
}
