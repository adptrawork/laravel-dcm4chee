<?php

namespace Database\Seeders;

use App\Models\User;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Facades\Filament;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        Artisan::call('shield:generate', [
            '--all' => true,
            '--option' => 'policies_and_permissions',
            '--panel' => 'admin',
        ]);

        $role = Utils::createRole();
        $role->syncPermissions(Utils::getPermissionModel()::pluck('id'));

        $user = User::where('email', 'admin@admin.com')->first();
        if ($user) {
            $user->assignRole($role);
        }
    }
}
