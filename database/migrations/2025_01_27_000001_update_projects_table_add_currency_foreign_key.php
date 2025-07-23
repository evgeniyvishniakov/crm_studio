<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Добавляем новое поле currency_id
            $table->unsignedBigInteger('currency_id')->nullable()->after('language');
            
            // Добавляем внешний ключ
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('set null');
        });

        // Мигрируем данные из старого поля currency в новое currency_id
        $this->migrateCurrencyData();

        // Удаляем старое поле currency
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('currency');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Восстанавливаем старое поле currency
            $table->string('currency')->nullable()->after('language');
        });

        // Восстанавливаем данные
        $this->rollbackCurrencyData();

        Schema::table('projects', function (Blueprint $table) {
            // Удаляем новое поле и внешний ключ
            $table->dropForeign(['currency_id']);
            $table->dropColumn('currency_id');
        });
    }

    /**
     * Мигрируем данные из currency в currency_id
     */
    private function migrateCurrencyData(): void
    {
        $currencies = \App\Models\Currency::all()->keyBy('code');
        
        \App\Models\Admin\Project::chunk(100, function ($projects) use ($currencies) {
            foreach ($projects as $project) {
                if ($project->currency) {
                    $currency = $currencies->get($project->currency);
                    if ($currency) {
                        $project->currency_id = $currency->id;
                        $project->save();
                    }
                }
            }
        });
    }

    /**
     * Восстанавливаем данные из currency_id в currency
     */
    private function rollbackCurrencyData(): void
    {
        \App\Models\Admin\Project::chunk(100, function ($projects) {
            foreach ($projects as $project) {
                if ($project->currency_id) {
                    $currency = \App\Models\Currency::find($project->currency_id);
                    if ($currency) {
                        $project->currency = $currency->code;
                        $project->save();
                    }
                }
            }
        });
    }
}; 