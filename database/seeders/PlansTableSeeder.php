<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlansTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'До 2 сотрудников',
                'slug' => 'small',
                'max_employees' => 2,
                'price_monthly' => 490.00,
                'description' => 'Базовый тариф для небольших салонов',
                'features' => [
                    'Все основные функции CRM',
                    'До 2 сотрудников',
                    'Базовая поддержка'
                ],
                'is_active' => true,
                'sort_order' => 1
            ],
            [
                'name' => 'До 5 сотрудников',
                'slug' => 'medium',
                'max_employees' => 5,
                'price_monthly' => 990.00,
                'description' => 'Стандартный тариф для средних салонов',
                'features' => [
                    'Все функции CRM',
                    'До 5 сотрудников',
                    'Приоритетная поддержка',
                    'Расширенная аналитика'
                ],
                'is_active' => true,
                'sort_order' => 2
            ],
            [
                'name' => 'Без лимита',
                'slug' => 'unlimited',
                'max_employees' => null,
                'price_monthly' => 1990.00,
                'description' => 'Премиум тариф для крупных сетей',
                'features' => [
                    'Все функции CRM',
                    'Неограниченное количество сотрудников',
                    'VIP поддержка 24/7',
                    'Персональный менеджер',
                    'Индивидуальные настройки'
                ],
                'is_active' => true,
                'sort_order' => 3
            ]
        ];

        foreach ($plans as $plan) {
            \App\Models\Plan::updateOrCreate(
                ['slug' => $plan['slug']],
                $plan
            );
        }
    }
}
