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
        Permission::create(['name' => 'view_dashboard']);
        Permission::create(['name' => 'view_studies']);
        Permission::create(['name' => 'view_worklist']);
        Permission::create(['name' => 'create_order']);
        Permission::create(['name' => 'edit_order']);
        Permission::create(['name' => 'write_report']);
        Permission::create(['name' => 'verify_report']);
        Permission::create(['name' => 'manage_servers']);
        Permission::create(['name' => 'manage_devices']);
        Permission::create(['name' => 'manage_procedures']);
        Permission::create(['name' => 'manage_users']);

        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());

        $radiologist = Role::create(['name' => 'radiologist']);
        $radiologist->givePermissionTo(['view_dashboard', 'view_studies', 'view_worklist',
            'create_order', 'write_report', 'verify_report']);

        $radiographer = Role::create(['name' => 'radiographer']);
        $radiographer->givePermissionTo(['view_dashboard', 'view_worklist',
            'create_order', 'edit_order']);

        $dokter = Role::create(['name' => 'dokter']);
        $dokter->givePermissionTo(['view_studies', 'view_worklist']);

        $user = User::where('email', 'admin@admin.com')->first();
        if ($user) {
            $user->assignRole('admin');
        }
    }
}
