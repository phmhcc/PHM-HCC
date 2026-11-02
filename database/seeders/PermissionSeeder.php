<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $rolePermissions = [
            'admin' => ['*'],
        
            'deacon' => [
                'dashboard.view',
                'events.view',
                'duties.view',
                'meetings.view',
                'announcements.view',
                'announcements.view_all',
                'departments.view',
                'reports.view',
                'reports.create',
                'reports.update',
                'reports.delete',
            ],
            'elder' => [
                'dashboard.view',
                'events.view',
                'duties.view',
                'meetings.view',
                'announcements.view',
                'announcements.view_all',
                'departments.view',
                'reports.view',
                'reports.create',
                'reports.update',
                'reports.delete',
            ],
            'department_leader' => [
                'dashboard.view',
                'events.view',
                'duties.*',
                'announcements.view',
                'announcements.view_all',
                'meetings.view',
                'departments.view',
                'reports.view',
                'reports.create',
                'reports.update',
                'reports.delete',
            ],
            'treasurer' => [
                'dashboard.view',
                'events.view',
                'announcements.view',
                'announcements.view_all',
                'duties.view',
                'meetings.view',
                'departments.view',
                'reports.view',
                'reports.create',
                'reports.update',
                'reports.delete',
                'finance.*',
            ],
        ];

        foreach ($rolePermissions as $roleName => $permissionNames) {
            $permissionIds = collect($permissionNames)
                ->map(fn (string $name) => Permission::firstOrCreate(['name' => $name])->id)
                ->all();

            Role::where('name', $roleName)
                ->firstOrFail()
                ->permissions()
                ->sync($permissionIds);
        }
    }
}
