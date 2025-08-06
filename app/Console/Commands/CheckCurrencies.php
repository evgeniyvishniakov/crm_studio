<?php

namespace App\Console\Commands;

use App\Models\Currency;
use Illuminate\Console\Command;

class CheckCurrencies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'currencies:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check currencies in database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $currencies = Currency::all();
        
        $this->info('Текущие валюты в базе данных:');
        
        foreach ($currencies as $currency) {
            $this->line(sprintf(
                '%s: %d знаков, разделитель: "%s", тысячи: "%s", по умолчанию: %s',
                $currency->code,
                $currency->decimal_places,
                $currency->decimal_separator,
                $currency->thousands_separator ?: '(пусто)',
                $currency->is_default ? 'ДА' : 'НЕТ'
            ));
        }
        
        return 0;
    }
}
