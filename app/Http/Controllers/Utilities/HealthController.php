<?php

namespace App\Http\Controllers\Utilities;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\Server;
use App\Services\Dcm4chee\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class HealthController extends Controller
{
    public function index(): View
    {
        // ponytail: simple sequential checks, parallelize if > 10 servers
        $checks = [];

        // Laravel app
        $checks['app'] = ['label' => 'Laravel App', 'status' => true, 'detail' => config('app.name') . ' ' . app()->version()];

        // Database
        try {
            DB::select('SELECT 1');
            $checks['db'] = ['label' => 'Database', 'status' => true, 'detail' => config('database.default')];
        } catch (\Exception $e) {
            $checks['db'] = ['label' => 'Database', 'status' => false, 'detail' => $e->getMessage()];
        }

        // Cache
        try {
            Cache::store(config('cache.default'))->get('health-check');
            $checks['cache'] = ['label' => 'Cache', 'status' => true, 'detail' => config('cache.default')];
        } catch (\Exception $e) {
            $checks['cache'] = ['label' => 'Cache', 'status' => false, 'detail' => $e->getMessage()];
        }

        // Queue (check if failed_jobs exists and worker might be running)
        $pendingJobs = DB::table('jobs')->count();
        $failedJobs = DB::table('failed_jobs')->count();
        $checks['queue'] = ['label' => 'Queue', 'status' => true, 'detail' => "Pending: {$pendingJobs}, Failed: {$failedJobs}"];

        // DCM4CHEE servers
        $servers = Server::where('enabled', true)->get();
        $onlineServers = 0;
        foreach ($servers as $server) {
            try {
                $client = new Client($server);
                $response = $client->raw('GET', 'patients', ['query' => ['limit' => 1]]);
                $online = $response->successful();
                if ($online) $onlineServers++;
                $checks["server_{$server->id}"] = [
                    'label' => $server->name,
                    'status' => $online,
                    'detail' => $online ? $response->status() . ' OK' : $response->status() . ' ' . $response->body(),
                ];
            } catch (\Exception $e) {
                $checks["server_{$server->id}"] = ['label' => $server->name, 'status' => false, 'detail' => $e->getMessage()];
            }
        }

        // Modalities
        $totalDevices = Device::count();
        $onlineDevices = Device::whereNotNull('last_echo_at')->where('last_echo_at', '>=', now()->subHours(2))->count();
        $checks['modalities'] = ['label' => 'Modalities', 'status' => $onlineDevices > 0, 'detail' => "{$onlineDevices}/{$totalDevices} Online"];

        return view('utilities.health', compact('checks'));
    }
}
