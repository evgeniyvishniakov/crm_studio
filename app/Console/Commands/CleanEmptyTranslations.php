<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Admin\BlogCategoryTranslation;
use App\Models\Admin\BlogTagTranslation;
use App\Models\Admin\BlogArticleTranslation;

class CleanEmptyTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:clean-empty';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Удалить пустые переводы из всех таблиц блога';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Очистка пустых переводов...');
        
        // Очистка переводов категорий
        $categoryCount = BlogCategoryTranslation::where(function($query) {
            $query->whereNull('name')
                  ->orWhere('name', '')
                  ->orWhereRaw('TRIM(name) = ""');
        })->count();
        
        if ($categoryCount > 0) {
            BlogCategoryTranslation::where(function($query) {
                $query->whereNull('name')
                      ->orWhere('name', '')
                      ->orWhereRaw('TRIM(name) = ""');
            })->delete();
            $this->info("Удалено {$categoryCount} пустых переводов категорий");
        }
        
        // Очистка переводов тегов
        $tagCount = BlogTagTranslation::where(function($query) {
            $query->whereNull('name')
                  ->orWhere('name', '')
                  ->orWhereRaw('TRIM(name) = ""');
        })->count();
        
        if ($tagCount > 0) {
            BlogTagTranslation::where(function($query) {
                $query->whereNull('name')
                      ->orWhere('name', '')
                      ->orWhereRaw('TRIM(name) = ""');
            })->delete();
            $this->info("Удалено {$tagCount} пустых переводов тегов");
        }
        
        // Очистка переводов статей
        $articleCount = BlogArticleTranslation::where(function($query) {
            $query->whereNull('title')
                  ->orWhere('title', '')
                  ->orWhereRaw('TRIM(title) = ""');
        })->count();
        
        if ($articleCount > 0) {
            BlogArticleTranslation::where(function($query) {
                $query->whereNull('title')
                      ->orWhere('title', '')
                      ->orWhereRaw('TRIM(title) = ""');
            })->delete();
            $this->info("Удалено {$articleCount} пустых переводов статей");
        }
        
        $this->info('Очистка завершена!');
    }
}
