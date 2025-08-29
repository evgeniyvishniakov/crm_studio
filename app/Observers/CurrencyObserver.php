<?php

namespace App\Observers;

use App\Models\Currency;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;

class CurrencyObserver
{
    /**
     * Handle the Currency "created" event.
     */
    public function created(Currency $currency): void
    {
        $this->addCurrencyFieldsToPlans($currency->code);
    }

    /**
     * Handle the Currency "updated" event.
     */
    public function updated(Currency $currency): void
    {
        // Если код валюты изменился, переименовываем поля
        if ($currency->wasChanged('code')) {
            $oldCode = $currency->getOriginal('code');
            $newCode = $currency->code;
            $this->renameCurrencyFieldsInPlans($oldCode, $newCode);
        }
    }

    /**
     * Handle the Currency "deleted" event.
     */
    public function deleted(Currency $currency): void
    {
        $this->removeCurrencyFieldsFromPlans($currency->code);
    }

    /**
     * Handle the Currency "restored" event.
     */
    public function restored(Currency $currency): void
    {
        $this->addCurrencyFieldsToPlans($currency->code);
    }

    /**
     * Handle the Currency "force deleted" event.
     */
    public function forceDeleted(Currency $currency): void
    {
        $this->removeCurrencyFieldsFromPlans($currency->code);
    }

    /**
     * Добавляет поля валюты в таблицу планов
     */
    private function addCurrencyFieldsToPlans(string $currencyCode): void
    {
        $currencyCode = strtolower($currencyCode);
        
        if (!Schema::hasTable('plans')) {
            return;
        }

        $periods = ['monthly', 'quarterly', 'six_months', 'yearly'];
        
        foreach ($periods as $period) {
            $fieldName = "price_{$period}_{$currencyCode}";
            
            if (!Schema::hasColumn('plans', $fieldName)) {
                Schema::table('plans', function (Blueprint $table) use ($fieldName) {
                    $table->decimal($fieldName, 10, 2)->nullable();
                });
            }
        }
    }

    /**
     * Переименовывает поля валюты в таблице планов
     */
    private function renameCurrencyFieldsInPlans(string $oldCode, string $newCode): void
    {
        $oldCode = strtolower($oldCode);
        $newCode = strtolower($newCode);
        
        if (!Schema::hasTable('plans')) {
            return;
        }

        $periods = ['monthly', 'quarterly', 'six_months', 'yearly'];
        
        foreach ($periods as $period) {
            $oldFieldName = "price_{$period}_{$oldCode}";
            $newFieldName = "price_{$period}_{$newCode}";
            
            if (Schema::hasColumn('plans', $oldFieldName)) {
                Schema::table('plans', function (Blueprint $table) use ($oldFieldName, $newFieldName) {
                    $table->renameColumn($oldFieldName, $newFieldName);
                });
            }
        }
    }

    /**
     * Удаляет поля валюты из таблицы планов
     */
    private function removeCurrencyFieldsFromPlans(string $currencyCode): void
    {
        $currencyCode = strtolower($currencyCode);
        
        if (!Schema::hasTable('plans')) {
            return;
        }

        $periods = ['monthly', 'quarterly', 'six_months', 'yearly'];
        $columnsToDrop = [];
        
        foreach ($periods as $period) {
            $fieldName = "price_{$period}_{$currencyCode}";
            
            if (Schema::hasColumn('plans', $fieldName)) {
                $columnsToDrop[] = $fieldName;
            }
        }
        
        if (!empty($columnsToDrop)) {
            Schema::table('plans', function (Blueprint $table) use ($columnsToDrop) {
                $table->dropColumn($columnsToDrop);
            });
        }
    }
}
