<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('ris:ping-modalities')]
#[Description('C-ECHO all devices and update status')]
class PingModalities extends Command
{
    public function handle()
    {
        $devices = \App\Models\Device::all();

        if ($devices->isEmpty()) {
            $this->warn('No devices configured.');
            return;
        }

        foreach ($devices as $device) {
            $this->info("Pinging {$device->name} ({$device->ae_title})...");

            $output = [];
            $resultCode = 0;
            exec("echoscu -v -aec {$device->ae_title} -aet DCM4CHEE {$device->hostname} {$device->port} 2>&1", $output, $resultCode);

            $device->update([
                'status' => $resultCode === 0 ? 'online' : 'offline',
                'last_echo_at' => now(),
            ]);

            if ($resultCode === 0) {
                $this->info("  ✓ Online");
            } else {
                $this->warn("  ✗ Offline");
            }
        }
    }
}
