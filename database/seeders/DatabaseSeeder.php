<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            ['name' => 'Admin', 'password' => 'admin123'],
        );

        $this->call(RolesAndPermissionsSeeder::class);

        // Seed users for each role (created after roles exist)
        User::firstOrCreate(
            ['email' => 'radiologist@radiology.com'],
            ['name' => 'Dr. Radiologist', 'password' => 'radiologist123'],
        )->assignRole('radiologist');

        User::firstOrCreate(
            ['email' => 'radiographer@radiology.com'],
            ['name' => 'Radiographer', 'password' => 'radiographer123'],
        )->assignRole('radiographer');

        User::firstOrCreate(
            ['email' => 'dokter@hospital.com'],
            ['name' => 'Dr. Dokter', 'password' => 'dokter123'],
        )->assignRole('dokter');

        $this->call(ServerSeeder::class);
        $this->call(DefaultDataSeeder::class);
    }
}
