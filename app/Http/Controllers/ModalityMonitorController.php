<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Server;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ModalityMonitorController extends Controller
{
    public function index(Request $request): View
    {
        $servers = Server::where('enabled', true)->get();
        $serverId = $request->session()->get('modality_server_id', $servers->first()?->id);

        $devices = Device::with('server')
            ->when($serverId, fn($q) => $q->where('server_id', $serverId))
            ->get();

        return view('modality-monitor.index', compact('servers', 'serverId', 'devices'));
    }

    public function setServer(Request $request): RedirectResponse
    {
        $validated = $request->validate(['server_id' => 'required|exists:servers,id']);
        $request->session()->put('modality_server_id', $validated['server_id']);
        return back();
    }

    public function ping(Device $device): RedirectResponse
    {
        try {
            $output = [];
            $resultCode = 0;
            exec("echoscu -v -aec {$device->ae_title} -aet DCM4CHEE {$device->hostname} {$device->port} 2>&1", $output, $resultCode);

            $device->update([
                'status' => $resultCode === 0 ? 'online' : 'offline',
                'last_echo_at' => now(),
            ]);

            if ($resultCode === 0) {
                return back()->with('success', "C-ECHO to {$device->name} succeeded.");
            }
            return back()->withErrors(['error' => "C-ECHO failed: " . implode("\n", array_slice($output, 0, 3))]);
        } catch (\Exception $e) {
            $device->update(['status' => 'offline', 'last_echo_at' => now()]);
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
