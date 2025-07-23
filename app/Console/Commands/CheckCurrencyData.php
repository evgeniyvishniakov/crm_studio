<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Admin\Project;
use App\Models\Currency;
use App\Helpers\CurrencyHelper;

class CheckCurrencyData extends Command
{
    protected $signature = 'currency:check';
    protected $description = 'Проверить данные валют и проектов';

    public function handle()
    {
        $this->info('=== ПРОВЕРКА ДАННЫХ ВАЛЮТ ===');
        
        // Проверяем валюты
        $this->info('Валюты в базе данных:');
        $currencies = Currency::all(['code', 'name', 'is_default', 'is_active']);
        foreach ($currencies as $currency) {
            $this->line("  {$currency->code}: {$currency->name} (по умолчанию: " . ($currency->is_default ? 'да' : 'нет') . ", активна: " . ($currency->is_active ? 'да' : 'нет') . ")");
        }
        
        $this->info('');
        $this->info('Проекты и их валюты:');
        $projects = Project::all(['id', 'name', 'currency']);
        foreach ($projects as $project) {
            $this->line("  {$project->id}: {$project->name} - валюта: {$project->currency}");
        }
        
        $this->info('');
        $this->info('Текущая валюта через CurrencyHelper:');
        $this->line("  " . CurrencyHelper::getCurrentCurrency());
        
        $this->info('');
        $this->info('Валюты проекта:');
        $this->line("  " . (CurrencyHelper::getProjectCurrency() ?: 'не определена'));
        
        $this->info('');
        $this->info('Валюты по умолчанию:');
        $default = Currency::getDefault();
        if ($default) {
            $this->line("  " . $default->code . " - " . $default->name);
        } else {
            $this->line("  Валюты по умолчанию не найдена");
        }
        
        $this->info('');
        $this->info('Сессия:');
        $this->line("  " . (session('currency') ?: 'не установлена'));
        
        $this->info('');
        $this->info('Детальная информация о USD:');
        $usd = Currency::getByCode('USD');
        if ($usd) {
            $this->line("  Код: {$usd->code}");
            $this->line("  Название: {$usd->name}");
            $this->line("  Символ: '{$usd->symbol}'");
            $this->line("  Позиция: {$usd->symbol_position}");
            $this->line("  Десятичные: {$usd->decimal_places}");
            $this->line("  Разделитель десятичных: '{$usd->decimal_separator}'");
            $this->line("  Разделитель тысяч: '{$usd->thousands_separator}'");
            $this->line("  Активна: " . ($usd->is_active ? 'да' : 'нет'));
            $this->line("  По умолчанию: " . ($usd->is_default ? 'да' : 'нет'));
            $this->line("  Тест форматирования: " . $usd->formatAmount(1000));
        } else {
            $this->line("  USD не найдена в базе данных");
        }
    }
} 