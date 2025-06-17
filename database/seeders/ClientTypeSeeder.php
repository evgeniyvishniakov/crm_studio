<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClientType;

class ClientTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'name' => 'Обычный клиент',
                'description' => 'Стандартный тип клиента без специальных условий',
                'discount' => 0,
                'status' => true
            ],
            [
                'name' => 'Постоянный клиент',
                'description' => 'Клиент, который регулярно пользуется услугами',
                'discount' => 5,
                'status' => true
            ],
            [
                'name' => 'VIP клиент',
                'description' => 'Особо важный клиент с расширенными привилегиями',
                'discount' => 10,
                'status' => true
            ]
        ];

        foreach ($types as $type) {
            ClientType::create($type);
        }
    }
} 