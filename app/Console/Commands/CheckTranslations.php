<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Admin\BlogCategoryTranslation;
use App\Models\Admin\BlogTagTranslation;
use App\Models\Admin\BlogArticleTranslation;

class CheckTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Проверить содержимое таблиц переводов';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Проверка переводов категорий:');
        $categoryTranslations = BlogCategoryTranslation::all(['id', 'blog_category_id', 'locale', 'name', 'description']);
        foreach ($categoryTranslations as $translation) {
            $this->line("ID: {$translation->id}, Category: {$translation->blog_category_id}, Locale: {$translation->locale}, Name: '{$translation->name}', Description: '{$translation->description}'");
        }
        
        $this->info("\nПроверка переводов тегов:");
        $tagTranslations = BlogTagTranslation::all(['id', 'blog_tag_id', 'locale', 'name']);
        foreach ($tagTranslations as $translation) {
            $this->line("ID: {$translation->id}, Tag: {$translation->blog_tag_id}, Locale: {$translation->locale}, Name: '{$translation->name}'");
        }
        
        $this->info("\nПроверка переводов статей:");
        $articleTranslations = BlogArticleTranslation::all(['id', 'blog_article_id', 'locale', 'title']);
        foreach ($articleTranslations as $translation) {
            $this->line("ID: {$translation->id}, Article: {$translation->blog_article_id}, Locale: {$translation->locale}, Title: '{$translation->title}'");
        }
    }
}
