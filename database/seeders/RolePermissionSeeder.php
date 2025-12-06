<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Basic permissions
        $permissions = [
            'view_dashboard',
            'manage_users',
            'manage_stores',
            'manage_settings',
        ];

        foreach ($permissions as $p) {
            Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
        }

        // Roles
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $manager = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);

        // Give all permissions to admin
        $admin->syncPermissions(Permission::all());

        // Give only selected permissions to manager
        $manager->syncPermissions([
            'view_dashboard',
            'manage_stores',
        ]);
    }
}
