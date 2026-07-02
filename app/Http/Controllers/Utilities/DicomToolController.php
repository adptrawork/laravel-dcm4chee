<?php

namespace App\Http\Controllers\Utilities;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\Server;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DicomToolController extends Controller
{
    public function index(): View
    {
        $servers = Server::where('enabled', true)->get();
        $devices = Device::orderBy('name')->get();
        return view('utilities.dicom-tools', compact('servers', 'devices'));
    }

    public function echo(Request $request): View
    {
        $validated = $request->validate([
            'ae_title' => 'required|string|max:64',
            'hostname' => 'required|string|max:255',
            'port' => 'required|integer|min:1|max:65535',
        ]);

        $cmd = sprintf(
            'echoscu -v -aec %s -aet DCM4CHEE -t 5 %s %d 2>&1',
            escapeshellarg($validated['ae_title']),
            escapeshellarg($validated['hostname']),
            $validated['port']
        );

        exec($cmd, $output, $code);

        $result = implode("\n", $output);
        $success = $code === 0;

        $servers = Server::where('enabled', true)->get();
        $devices = Device::orderBy('name')->get();
        return view('utilities.dicom-tools', compact('servers', 'devices', 'result', 'success'));
    }

    // ponytail: ping via shell exec, add timeout config if hosts are slow
    public function ping(Request $request): View
    {
        $validated = $request->validate([
            'hostname' => 'required|string|max=255',
        ]);

        $cmd = sprintf('ping -c 4 -W 3 %s 2>&1', escapeshellarg($validated['hostname']));
        exec($cmd, $output, $code);

        $result = implode("\n", $output);
        $success = $code === 0;

        $servers = Server::where('enabled', true)->get();
        $devices = Device::orderBy('name')->get();
        return view('utilities.dicom-tools', compact('servers', 'devices', 'result', 'success'));
    }

    public function find(Request $request): View
    {
        $validated = $request->validate([
            'server_id' => 'required|exists:servers,id',
            'patient_name' => 'nullable|string|max=255',
            'patient_id' => 'nullable|string|max=64',
            'accession' => 'nullable|string|max=64',
        ]);

        $server = Server::findOrFail($validated['server_id']);
        $client = new \App\Services\Dcm4chee\Client($server);

        $params = [];
        if (!empty($validated['patient_name'])) $params['PatientName'] = $validated['patient_name'];
        if (!empty($validated['patient_id'])) $params['PatientID'] = $validated['patient_id'];
        if (!empty($validated['accession'])) $params['AccessionNumber'] = $validated['accession'];

        $response = $client->raw('GET', 'patients', ['query' => $params]);

        $result = $response->successful() ? json_encode($response->json(), JSON_PRETTY_PRINT) : 'Error: ' . $response->body();
        $success = $response->successful();

        $servers = Server::where('enabled', true)->get();
        $devices = Device::orderBy('name')->get();
        return view('utilities.dicom-tools', compact('servers', 'devices', 'result', 'success'));
    }

    public function move(Request $request): View
    {
        $validated = $request->validate([
            'server_id' => 'required|exists:servers,id',
            'study_uid' => 'required|string|max=128',
            'dest_ae' => 'required|string|max=64',
        ]);

        $server = Server::findOrFail($validated['server_id']);
        $client = new \App\Services\Dcm4chee\Client($server);

        // ponytail: C-MOVE via REST, add DCMTK movescu fallback if REST fails
        $response = $client->raw('POST', "studies/{$validated['study_uid']}/move/{$validated['dest_ae']}");

        $result = $response->successful() ? "C-MOVE initiated. Response: " . $response->body() : 'Error: ' . $response->body();
        $success = $response->successful();

        $servers = Server::where('enabled', true)->get();
        $devices = Device::orderBy('name')->get();
        return view('utilities.dicom-tools', compact('servers', 'devices', 'result', 'success'));
    }
}
