<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'manage-users',
            'manage-departments',
            'manage-all-projects',
            'manage-own-projects',
            'manage-all-tasks',
            'manage-own-tasks',
            'assign-tasks',
            'view-all-work-logs',
            'view-team-work-logs',
            'manage-own-work-logs',
            'view-all-reports',
            'view-team-reports',
            'export-reports',
            'view-audit-log',
            'manage-comments',
            'manage-attachments',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions($permissions);

        $manager = Role::firstOrCreate(['name' => 'manager']);
        $manager->syncPermissions([
            'manage-own-projects',
            'manage-all-tasks',
            'assign-tasks',
            'view-team-work-logs',
            'manage-own-work-logs',
            'view-team-reports',
            'export-reports',
            'manage-comments',
            'manage-attachments',
        ]);

        $employee = Role::firstOrCreate(['name' => 'employee']);
        $employee->syncPermissions([
            'manage-own-tasks',
            'manage-own-work-logs',
            'manage-comments',
            'manage-attachments',
        ]);
    }
}
