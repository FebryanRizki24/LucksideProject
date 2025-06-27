<?php

namespace Database\Seeders;

use App\Models\Barberman;
use App\Models\BarbermanSchedule;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BarbermanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $barbermen = [
            ['name' => 'Rudy Alamsyah', 'phone' => null, 'photo' => null, 'status' => true],
            ['name' => 'Jon', 'phone' => null, 'photo' => null, 'status' => true],
            ['name' => 'Tegar', 'phone' => null, 'photo' => null, 'status' => true],
        ];

        foreach ($barbermen as $barberman) {
            $barbermanModel = Barberman::create($barberman);

            switch ($barbermanModel->name) {
                case 'Rudy Alamsyah':
                    $schedules = [
                        ['start_time' => '15:00', 'end_time' => '17:00'],
                        ['start_time' => '19:00', 'end_time' => '22:00']
                    ];
                    
                    foreach ($schedules as $schedule) {
                        BarbermanSchedule::create(array_merge($schedule, [
                            'barberman_id' => $barbermanModel->id,
                        ]));
                    }                    
                    break;

                case 'Jon':
                    $schedules = [
                        ['start_time' => '08:00', 'end_time' => '15:00'],
                        ['start_time' => '19:00', 'end_time' => '22:00']
                    ];
                    
                    foreach ($schedules as $schedule) {
                        BarbermanSchedule::create(array_merge($schedule, [
                            'barberman_id' => $barbermanModel->id,
                        ]));
                    }                    
                    break;

                case 'Tegar':
                    $schedules = [
                        ['start_time' => '08:00', 'end_time' => '12:00']
                    ];
                    
                    foreach ($schedules as $schedule) {
                        BarbermanSchedule::create(array_merge($schedule, [
                            'barberman_id' => $barbermanModel->id,
                        ]));
                    }                    
                    break;
            }
        }
    }
}
