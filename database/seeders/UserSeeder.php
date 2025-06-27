<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::create([
            'id' => Str::uuid(),
            'name' => "admin",
            'email' => "admin@gmail.com",
            'password' => Hash::make('password'),
            'phone' => "0812345678910",
            'email_verified_at' => now(),
        ]);

        $barberman = User::create([
            'id' => Str::uuid(),
            'name' => "Rudy Alamsyah",
            'email' => "rudybarberman@gmail.com",
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'hairstyle' => [
                'hairstyle-view',
                'hairstyle-store',
                'hairstyle-update',
                'hairstyle-destroy',
            ],
            'barberman' => [
                'barberman-view',
                'barberman-store',
                'barberman-update',
                'barberman-destroy',
            ],
            'barbermanSchedule' => [
                'barbermanSchedule-view',
                'barbermanSchedule-store',
                'barbermanSchedule-update',
                'barbermanSchedule-destroy',
            ],
            'booking' => [
                'booking-view',
                'booking-store',
                'booking-update',
                'booking-destroy',
            ],
            'user' => [
                'user-view',
                'user-store',
                'user-update',
                'user-destroy',
            ]
        ];

        foreach ($permissions as $k => $v) {
            foreach ($v as $key => $value) {
                $arr = [];
                $arr['name'] = $value;
                $arr['guard_name'] = 'web';
                Permission::create($arr);
            }
        }

        Role::create(['name' => 'admin'])->givePermissionTo([
            $permissions
        ]);
        // $admin = $admin->fresh();
        $admin->assignRole(['admin']);

        Role::create(['name' => 'user'])->givePermissionTo(['booking-view']);

        Role::create(['name' => 'barberman'])->givePermissionTo(['booking-view']);

        $barberman->assignRole(['barberman']);
    }
}
