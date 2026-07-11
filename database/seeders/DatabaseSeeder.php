<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);

        $admin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            ['name' => 'Admin', 'password' => 'admin123'],
        );
        $admin->assignRole('admin');

        $this->call(ServerSeeder::class);
        $this->call(DefaultDataSeeder::class);
    }
}
