<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Currency;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class AddCurrencyFieldsToExistingPlans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add:currency-fields-to-plans';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Добавляет поля валют в существующие планы для всех активных валют';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Добавление полей валют в существующие планы...');
        
        if (!Schema::hasTable('plans')) {
            $this->error('❌ Таблица plans не найдена!');
            return 1;
        }
        
        // Получаем все активные валюты
        $currencies = Currency::where('is_active', true)->get();
        $this->info("💰 Найдено активных валют: {$currencies->count()}");
        
        $periods = ['monthly', 'quarterly', 'six_months', 'yearly'];
        $addedFields = 0;
        
        foreach ($currencies as $currency) {
            $currencyCode = strtolower($currency->code);
            $this->info("  💱 Обрабатываем валюту: {$currency->code} ({$currency->name})");
            
            foreach ($periods as $period) {
                $fieldName = "price_{$period}_{$currencyCode}";
                
                if (!Schema::hasColumn('plans', $fieldName)) {
                    try {
                        Schema::table('plans', function (Blueprint $table) use ($fieldName) {
                            $table->decimal($fieldName, 10, 2)->nullable();
                        });
                        
                        $this->info("    ✅ Добавлено поле: {$fieldName}");
                        $addedFields++;
                    } catch (\Exception $e) {
                        $this->error("    ❌ Ошибка при добавлении поля {$fieldName}: " . $e->getMessage());
                    }
                } else {
                    $this->info("    ℹ️ Поле уже существует: {$fieldName}");
                }
            }
        }
        
        $this->info("\n📊 Итого добавлено полей: {$addedFields}");
        $this->info("✅ Добавление полей завершено!");
        
        return 0;
    }
}
