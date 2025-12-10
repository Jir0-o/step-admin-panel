<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'view_dashboard',
            'view_store',
            'view_discount',
            'create_stores',
            'view_route_management',
            'manage_users',
            'manage_settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        // Roles
        $adminRole = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        $userRole = Role::firstOrCreate([
            'name' => 'user',
            'guard_name' => 'web',
        ]);

        // Admin permissions
        $adminRole->syncPermissions([
            'view_dashboard',
            'create_stores',
            'view_route_management',
            'manage_users',
            'manage_settings',
        ]);

        // User permissions
        $userRole->syncPermissions([
            'view_dashboard',
            'view_store',
            'view_discount',
        ]);
    }
}
