<?php

namespace Database\Seeders;

use App\Models\Server;
use Illuminate\Database\Seeder;

class ServerSeeder extends Seeder
{
    public function run(): void
    {
        Server::create([
            'name' => 'Local DCM4CHEE',
            'base_url' => 'http://host.docker.internal:8080',
            'username' => 'admin',
            'password' => encrypt('changeit'),
        ]);
    }
}
