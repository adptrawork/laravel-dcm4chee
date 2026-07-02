<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('ris:poll-studies')]
#[Description('Poll DCM4CHEE for new studies matching pending MWL items')]
class PollStudies extends Command
{
    public function handle()
    {
        $servers = \App\Models\Server::where('enabled', true)->get();

        if ($servers->isEmpty()) {
            $this->warn('No enabled servers configured.');
            return;
        }

        foreach ($servers as $server) {
            $this->info("Polling server: {$server->name}...");
            $service = new \App\Services\Ris\StudyPollerService;
            $result = $service->poll($server);
            $this->info($result['message']);
        }
    }
}
