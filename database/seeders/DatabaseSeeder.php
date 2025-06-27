<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\OperationalHour;
use App\Models\Service;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            UserSeeder::class,
            BarbermanSeeder::class,
            FaceShapeSeeder::class,
            HairstyleSeeder::class,
            HairstyleFaceShapeSeeder::class,
            PermissionSeeder::class,
        ]);

        OperationalHour::create([
            'open_time' => '08:00:00',
            'close_time' => '22:00:00',
        ]);

        Service::create([
            'name' => 'Haircut',
            'price' => '20000'
        ]);

    }
}
