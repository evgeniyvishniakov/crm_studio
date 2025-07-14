<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Clients\ClientType;

class ClientTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'name' => 'Новый клиент',
                'description' => 'Клиент, который только начал пользоваться услугами',
                'discount' => 0,
                'status' => true
            ],
            [
                'name' => 'Постоянный клиент',
                'description' => 'Клиент, который регулярно пользуется услугами',
                'discount' => 5,
                'status' => true
            ],
        ];

        foreach ($types as $type) {
            ClientType::firstOrCreate(['name' => $type['name']], $type);
        }
    }
} 