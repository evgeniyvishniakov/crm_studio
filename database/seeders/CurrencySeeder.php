<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            [
                'code' => 'UAH',
                'name' => 'Украинская гривна',
                'symbol' => '₴',
                'symbol_position' => 'after',
                'decimal_places' => 2,
                'decimal_separator' => '.',
                'thousands_separator' => ',',
                'is_active' => true,
                'is_default' => true,
            ],
            [
                'code' => 'USD',
                'name' => 'US Dollar',
                'symbol' => '$',
                'symbol_position' => 'before',
                'decimal_places' => 2,
                'decimal_separator' => '.',
                'thousands_separator' => ',',
                'is_active' => true,
                'is_default' => false,
            ],
            [
                'code' => 'EUR',
                'name' => 'Euro',
                'symbol' => '€',
                'symbol_position' => 'before',
                'decimal_places' => 2,
                'decimal_separator' => '.',
                'thousands_separator' => ',',
                'is_active' => true,
                'is_default' => false,
            ],
        ];

        foreach ($currencies as $currency) {
            Currency::updateOrCreate(
                ['code' => $currency['code']],
                $currency
            );
        }
    }
} 