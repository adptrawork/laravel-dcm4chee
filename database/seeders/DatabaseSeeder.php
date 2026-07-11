<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@testing.com',
        ]);

        $this->call(ServerSeeder::class);
        $this->call(DefaultDataSeeder::class);
    }
}
