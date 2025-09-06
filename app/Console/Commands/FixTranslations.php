<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Admin\BlogCategoryTranslation;
use App\Models\Admin\BlogTagTranslation;
use App\Models\Admin\BlogArticleTranslation;

class FixTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:fix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Исправить переводы - удалить неправильные языки и пустые переводы';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Исправление переводов...');
        
        // Удаляем переводы с неправильными языками (оставляем только ru, en, ua)
        $validLocales = ['ru', 'en', 'ua'];
        
        // Очистка переводов категорий
        $categoryCount = BlogCategoryTranslation::whereNotIn('locale', $validLocales)->count();
        if ($categoryCount > 0) {
            BlogCategoryTranslation::whereNotIn('locale', $validLocales)->delete();
            $this->info("Удалено {$categoryCount} переводов категорий с неправильными языками");
        }
        
        // Очистка переводов тегов
        $tagCount = BlogTagTranslation::whereNotIn('locale', $validLocales)->count();
        if ($tagCount > 0) {
            BlogTagTranslation::whereNotIn('locale', $validLocales)->delete();
            $this->info("Удалено {$tagCount} переводов тегов с неправильными языками");
        }
        
        // Очистка переводов статей
        $articleCount = BlogArticleTranslation::whereNotIn('locale', $validLocales)->count();
        if ($articleCount > 0) {
            BlogArticleTranslation::whereNotIn('locale', $validLocales)->delete();
            $this->info("Удалено {$articleCount} переводов статей с неправильными языками");
        }
        
        // Удаляем пустые переводы
        $emptyCategoryCount = BlogCategoryTranslation::where(function($query) {
            $query->whereNull('name')
                  ->orWhere('name', '')
                  ->orWhereRaw('TRIM(name) = ""');
        })->count();
        
        if ($emptyCategoryCount > 0) {
            BlogCategoryTranslation::where(function($query) {
                $query->whereNull('name')
                      ->orWhere('name', '')
                      ->orWhereRaw('TRIM(name) = ""');
            })->delete();
            $this->info("Удалено {$emptyCategoryCount} пустых переводов категорий");
        }
        
        $emptyTagCount = BlogTagTranslation::where(function($query) {
            $query->whereNull('name')
                  ->orWhere('name', '')
                  ->orWhereRaw('TRIM(name) = ""');
        })->count();
        
        if ($emptyTagCount > 0) {
            BlogTagTranslation::where(function($query) {
                $query->whereNull('name')
                      ->orWhere('name', '')
                      ->orWhereRaw('TRIM(name) = ""');
            })->delete();
            $this->info("Удалено {$emptyTagCount} пустых переводов тегов");
        }
        
        $emptyArticleCount = BlogArticleTranslation::where(function($query) {
            $query->whereNull('title')
                  ->orWhere('title', '')
                  ->orWhereRaw('TRIM(title) = ""');
        })->count();
        
        if ($emptyArticleCount > 0) {
            BlogArticleTranslation::where(function($query) {
                $query->whereNull('title')
                      ->orWhere('title', '')
                      ->orWhereRaw('TRIM(title) = ""');
            })->delete();
            $this->info("Удалено {$emptyArticleCount} пустых переводов статей");
        }
        
        $this->info('Исправление завершено!');
    }
}
