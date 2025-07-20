<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Clients\Appointment;
use App\Models\Clients\Client;
use App\Models\Clients\Service;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class AppointmentsSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('ru_RU');
        $projectId = 45;
        $clients = Client::where('project_id', $projectId)->pluck('id')->toArray();
        $services = Service::where('project_id', $projectId)->get(['id', 'price'])->toArray();

        $start = strtotime('2024-07-20');
        $end = strtotime('2025-07-20');
        $totalAppointments = 953;

        $clientAppointmentsCount = array_fill_keys($clients, 0);
        $clientDateTimes = [];
        $appointments = [];

        for ($i = 0; $i < $totalAppointments; $i++) {
            asort($clientAppointmentsCount);
            $clientId = array_key_first($clientAppointmentsCount);

            $date = date('Y-m-d', rand($start, $end));
            $hour = $faker->numberBetween(9, 20);
            $minute = $faker->randomElement([0, 15, 30, 45]);
            $time = sprintf('%02d:%02d:00', $hour, $minute);

            $dateTimeKey = $clientId . '_' . $date . '_' . $time;
            $attempts = 0;
            while (isset($clientDateTimes[$dateTimeKey]) && $attempts < 10) {
                $hour = $faker->numberBetween(9, 20);
                $minute = $faker->randomElement([0, 15, 30, 45]);
                $time = sprintf('%02d:%02d:00', $hour, $minute);
                $dateTimeKey = $clientId . '_' . $date . '_' . $time;
                $attempts++;
            }
            $clientDateTimes[$dateTimeKey] = true;

            // Случайная услуга и её цена
            $service = $faker->randomElement($services);
            $serviceId = $service['id'];
            $price = $service['price'];

            $status = $faker->boolean(7) ? 'cancelled' : 'completed';

            $appointments[] = [
                'service_id'   => $serviceId,
                'client_id'    => $clientId,
                'date'         => $date,
                'time'         => $time,
                'price'        => $price,
                'notes'        => $faker->optional(0.2)->sentence,
                'status'       => $status,
                'created_at'   => now(),
                'updated_at'   => now(),
                'project_id'   => $projectId,
            ];

            $clientAppointmentsCount[$clientId]++;
        }

        foreach (array_chunk($appointments, 500) as $chunk) {
            Appointment::insert($chunk);
        }
    }
}
