<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        // Create permissions
        Permission::create(['name' => 'manage forms']);
        Permission::create(['name' => 'manage responses']);
        Permission::create(['name' => 'view responses']);

        // Create roles and assign existing permissions
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(['manage forms', 'manage responses', 'view responses']);

        $supervisorRole = Role::create(['name' => 'supervisor']);
        $supervisorRole->givePermissionTo(['manage responses', 'view responses']);

        $respondentRole = Role::create(['name' => 'respondent']);
        // Respondents don't have direct permissions on forms/responses via this package
    }
}
