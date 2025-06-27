<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'gallery' => [
                'gallery-view',
                'gallery-store',
                'gallery-update',
                'gallery-destroy',
            ],
            'service' => [
                'service-view',
                // 'service-store',
                'service-update',
                // 'service-destroy',
            ],
            'operationalHour' => [
                'operationalHour-view',
                // 'operationalHour-store',
                'operationalHour-update',
                // 'operationalHour-destroy',
            ],
            'holiday' => [
                'holiday-view',
                'holiday-store',
                'holiday-update',
                'holiday-destroy',
            ],
        ];

        $adminRole = Role::where('name', 'admin')->first(); // pastikan role admin ada

        foreach ($permissions as $group => $perms) {
            foreach ($perms as $permissionName) {
                $permission = Permission::firstOrCreate([
                    'name' => $permissionName,
                    'guard_name' => 'web'
                ]);

                if ($adminRole && !$adminRole->hasPermissionTo($permission)) {
                    $adminRole->givePermissionTo($permission);
                }
            }
        }
    }
}