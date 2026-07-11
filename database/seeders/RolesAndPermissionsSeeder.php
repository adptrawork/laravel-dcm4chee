<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'view_dashboard', 'view_studies', 'view_worklist',
            'create_order', 'edit_order', 'write_report', 'verify_report',
            'manage_servers', 'manage_devices', 'manage_procedures', 'manage_users',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions(Permission::pluck('name')->all());

        $radiologist = Role::firstOrCreate(['name' => 'radiologist', 'guard_name' => 'web']);
        $radiologist->syncPermissions(['view_dashboard', 'view_studies', 'view_worklist',
            'create_order', 'write_report', 'verify_report']);

        $radiographer = Role::firstOrCreate(['name' => 'radiographer', 'guard_name' => 'web']);
        $radiographer->syncPermissions(['view_dashboard', 'view_worklist',
            'create_order', 'edit_order']);

        $dokter = Role::firstOrCreate(['name' => 'dokter', 'guard_name' => 'web']);
        $dokter->syncPermissions(['view_studies', 'view_worklist']);

        $user = User::where('email', 'admin@admin.com')->first();
        if ($user) {
            $user->assignRole('admin');
        }
    }
}
