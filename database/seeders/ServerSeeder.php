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
            ['name' => 'Local PACS'],
            [
                'base_url' => 'http://host.docker.internal:8080',
                'archive' => 'dcm4chee-arc',
                'aet' => 'DCM4CHEE',
                'username' => 'admin',
                'password' => Crypt::encryptString('changeit'),
                'timeout' => 30,
                'ssl_verify' => false,
                'enabled' => true,
            ],
        );

        if (! Server::where('name', 'Mini Pacs')->exists()) {
            Server::create([
                'name' => 'Mini Pacs',
                'base_url' => 'https://192.168.2.220:8443',
                'archive' => 'dcm4chee-arc',
                'aet' => 'DCM4CHEE',
                'username' => 'admin',
                'password' => Crypt::encryptString('changeit'),
                'timeout' => 30,
                'ssl_verify' => false,
                'enabled' => true,
            ]);
        }
    }
}
