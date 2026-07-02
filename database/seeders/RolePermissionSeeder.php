<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'user.list', 'user.create', 'user.edit', 'user.delete',
            'role.list', 'role.create', 'role.edit', 'role.delete',
            'audit.view', 'audit.export',
            'job.view', 'job.retry',
            'server.list', 'server.create', 'server.edit', 'server.delete',
            'device.list', 'device.create', 'device.edit', 'device.delete',
            'procedure.list', 'procedure.create', 'procedure.edit', 'procedure.delete',
            'registration.create', 'registration.view',
            'worklist.view', 'worklist.refresh', 'worklist.cancel',
            'study.view',
            'mwl-queue.view',
            'study-tracker.view',
            'modality-monitor.view', 'modality-monitor.echo',
            'pacs-monitor.view',
            'settings.view', 'settings.edit',
        ];

        foreach ($permissions as $p) {
            Permission::create(['name' => $p]);
        }

        $roles = [
            'Super Admin' => $permissions,
            'Admin' => ['user.list', 'role.list',
                'server.list', 'server.create', 'server.edit',
                'device.list', 'device.create', 'device.edit',
                'procedure.list', 'procedure.create', 'procedure.edit',
                'audit.view', 'job.view', 'job.retry',
                'settings.view', 'settings.edit',
                'worklist.view', 'study.view',
            ],
            'Operator' => [
                'registration.create', 'registration.view',
                'worklist.view', 'worklist.refresh', 'worklist.cancel',
                'study.view',
                'mwl-queue.view',
                'study-tracker.view',
                'pacs-monitor.view',
            ],
            'Radiografer' => [
                'worklist.view',
                'study.view',
                'modality-monitor.view',
                'study-tracker.view',
            ],
            'Dokter' => [
                'study.view',
                'study-tracker.view',
            ],
            'Receptionist' => [
                'registration.create', 'registration.view',
                'worklist.view',
                'mwl-queue.view',
            ],
        ];

        foreach ($roles as $name => $perms) {
            $role = Role::create(['name' => $name]);
            $role->syncPermissions($perms);
        }

        $admin = User::first();
        if ($admin) {
            $admin->assignRole('Super Admin');
        }
    }
}
