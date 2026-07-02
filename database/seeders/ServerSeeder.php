<?php

namespace Database\Seeders;

use App\Models\Server;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;

class ServerSeeder extends Seeder
{
    public function run(): void
    {
        Server::firstOrCreate(
            ['name' => 'Local DCM4CHEE'],
            [
                'name' => 'Local DCM4CHEE',
                'base_url' => 'https://host.docker.internal:8443',
                'archive' => 'dcm4chee-arc',
                'aet' => 'DCM4CHEE',
                'username' => 'admin',
                'password' => Crypt::encryptString('changeit'),
                'timeout' => 30,
                'ssl_verify' => false,
                'enabled' => true,
            ]
        );

        $this->command->info('Default DCM4CHEE server seeded.');
    }
}
