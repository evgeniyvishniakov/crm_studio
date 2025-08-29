<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Plan;
use App\Models\Currency;
use Illuminate\Support\Facades\DB;

class TestCurrencySystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:currency-system';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Тестирует систему мультивалютности для планов';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Тестирование системы мультивалютности...');
        
        // Проверяем существующие планы
        $plans = Plan::all();
        $this->info("📋 Найдено планов: {$plans->count()}");
        
        if ($plans->count() > 0) {
            $plan = $plans->first();
            $this->info("📊 Тестируем план: {$plan->name}");
            
            // Проверяем цены для разных языков
            $languages = ['ua', 'ru', 'en', 'pl'];
            
            foreach ($languages as $lang) {
                $currency = $plan->getCurrencyByLanguage($lang);
                $monthlyPrice = $plan->getPriceForLanguage($lang, 'monthly');
                $this->info("  🌍 {$lang} → {$currency}: {$monthlyPrice}");
            }
        }
        
        // Проверяем существующие валюты
        $currencies = Currency::all();
        $this->info("💰 Найдено валют: {$currencies->count()}");
        
        foreach ($currencies as $currency) {
            $this->info("  💱 {$currency->code} ({$currency->name}) - {$currency->symbol}");
        }
        
        // Проверяем поля валют в планах
        if ($plans->count() > 0) {
            $plan = $plans->first();
            $this->info("\n🔍 Проверяем поля валют в плане:");
            
            $currencyFields = [
                'price_monthly_uah', 'price_monthly_usd', 'price_monthly_pln',
                'price_quarterly_uah', 'price_quarterly_usd', 'price_quarterly_pln'
            ];
            
            foreach ($currencyFields as $field) {
                $value = $plan->$field ?? 'null';
                $this->info("  📝 {$field}: {$value}");
            }
        }
        
        // Проверяем структуру таблицы планов
        $this->info("\n🏗️ Структура таблицы plans:");
        try {
            $columns = DB::select('SHOW COLUMNS FROM plans');
            foreach ($columns as $col) {
                if (strpos($col->Field, 'price_') !== false) {
                    $this->info("  📊 {$col->Field} - {$col->Type}");
                }
            }
        } catch (\Exception $e) {
            $this->error("❌ Ошибка при получении структуры таблицы: " . $e->getMessage());
        }
        
        $this->info("\n✅ Тестирование завершено!");
    }
}
