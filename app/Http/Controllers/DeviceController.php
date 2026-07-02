<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Server;
use App\Services\Dcm4chee\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DeviceController extends Controller
{
    public function index(): View
    {
        $devices = Device::with('server')->get();
        $servers = Server::where('enabled', true)->get();
        return view('devices.index', compact('devices', 'servers'));
    }

    public function create(): View
    {
        $servers = Server::where('enabled', true)->get();
        return view('devices.form', compact('servers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'server_id' => 'nullable|exists:servers,id',
            'name' => 'required|string|max:255',
            'ae_title' => 'required|string|max:16',
            'hostname' => 'required|string|max:255',
            'port' => 'required|integer|min:1|max:65535',
            'modality' => 'nullable|string|max:16',
        ]);

        Device::create($validated);

        return to_route('devices.index')->with('success', 'Device created.');
    }

    public function edit(Device $device): View
    {
        $servers = Server::where('enabled', true)->get();
        return view('devices.form', compact('device', 'servers'));
    }

    public function update(Request $request, Device $device): RedirectResponse
    {
        $validated = $request->validate([
            'server_id' => 'nullable|exists:servers,id',
            'name' => 'required|string|max:255',
            'ae_title' => 'required|string|max:16',
            'hostname' => 'required|string|max:255',
            'port' => 'required|integer|min:1|max:65535',
            'modality' => 'nullable|string|max:16',
        ]);

        $device->update($validated);

        return to_route('devices.index')->with('success', 'Device updated.');
    }

    public function destroy(Device $device): RedirectResponse
    {
        $device->delete();
        return to_route('devices.index')->with('success', 'Device deleted.');
    }

    public function echo(Device $device): RedirectResponse
    {
        try {
            $output = '';
            $resultCode = 0;
            exec("echoscu -v -aec {$device->ae_title} -aet DCM4CHEE {$device->hostname} {$device->port} 2>&1", $output, $resultCode);

            $device->update(['status' => $resultCode === 0 ? 'online' : 'offline']);

            if ($resultCode === 0) {
                return back()->with('success', "C-ECHO to {$device->name} succeeded.");
            } else {
                return back()->withErrors(['error' => "C-ECHO failed: " . implode("\n", $output)]);
            }
        } catch (\Exception $e) {
            $device->update(['status' => 'offline']);
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
