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

        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'role' => [
                'role-index',
                'role-store',
                'role-update',
                'role-destroy',
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

        $admin_role = Role::create(['name' => 'admin'])->givePermissionTo([
            $permissions
        ]);
        $admin = $admin->fresh();
        $admin->syncRoles(['admin']);

        Role::create(['name' => 'User']);
    }
}
