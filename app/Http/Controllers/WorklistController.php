<?php

namespace App\Http\Controllers;

use App\Models\Server;
use App\Models\WorklistItem;
use App\Services\Dcm4chee\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

class WorklistController extends Controller
{
    public function index(Request $request): View
    {
        $servers = Server::where('enabled', true)->get();
        $serverId = $request->session()->get('worklist_server_id', $servers->first()?->id);
        $status = $request->input('status', '');

        $items = WorklistItem::when($serverId, fn($q) => $q->where('server_id', $serverId))
            ->when($status, fn($q) => $q->where('status', $status))
            ->latest()
            ->take(100)
            ->get();

        return view('worklist.index', compact('servers', 'serverId', 'items', 'status'));
    }

    public function refresh(Request $request): RedirectResponse
    {
        $serverId = $request->session()->get('worklist_server_id');
        if (!$serverId) {
            return back()->withErrors(['error' => 'No server selected.']);
        }

        $server = Server::findOrFail($serverId);
        $client = new Client($server);

        try {
            $response = $client->raw('GET', 'workitems', [
                'headers' => ['Accept' => 'application/dicom+json'],
            ], 'mwl');

            if ($response->successful()) {
                $remoteItems = $response->json() ?? [];

                foreach ($remoteItems as $item) {
                    $accession = $item['00080050']['Value'][0] ?? null;
                    if (!$accession) continue;

                    $existing = WorklistItem::where('accession_number', $accession)->first();

                    $status = WorklistItem::STATUS_MW_PUBLISHED;
                    $sps = $item['00400100']['Value'][0] ?? [];
                    $spsStatus = $sps['00400020']['Value'][0] ?? '';
                    if (in_array($spsStatus, ['IN PROGRESS'])) {
                        $status = WorklistItem::STATUS_ACQUIRING;
                    } elseif (in_array($spsStatus, ['COMPLETED', 'DISCONTINUED'])) {
                        $status = WorklistItem::STATUS_ACQUIRED;
                    } elseif (in_array($spsStatus, ['CANCELED'])) {
                        $status = WorklistItem::STATUS_CANCELLED;
                    }

                    $patientName = $item['00100010']['Value'][0]['Alphabetic'] ?? ($item['00100010']['Value'][0] ?? '');
                    $patientId = $item['00100020']['Value'][0] ?? '';

                    if ($existing) {
                        if (in_array($existing->status, [WorklistItem::STATUS_MW_PUBLISHED, WorklistItem::STATUS_REGISTERED])) {
                            $existing->update(['status' => $status]);
                        }
                    } else {
                        WorklistItem::create([
                            'server_id' => $serverId,
                            'accession_number' => $accession,
                            'patient_name' => is_array($patientName) ? ($patientName['Alphabetic'] ?? '') : $patientName,
                            'patient_id' => $patientId,
                            'modality' => $item['00080060']['Value'][0] ?? '',
                            'procedure_description' => $item['00321060']['Value'][0] ?? ($sps['00400007']['Value'][0] ?? ''),
                            'requesting_physician' => $item['00080090']['Value'][0]['Alphabetic'] ?? '',
                            'scheduled_date' => $sps['00400002']['Value'][0] ?? '',
                            'scheduled_time' => $sps['00400003']['Value'][0] ?? '',
                            'status' => $status,
                        ]);
                    }
                }

                return back()->with('success', 'Worklist refreshed from DCM4CHEE.');
            } else {
                Log::error('Worklist refresh failed', ['response' => $response->body()]);
                return back()->withErrors(['error' => 'Failed to fetch worklist: ' . ($response->body() ?: '(empty)')]);
            }
        } catch (\Exception $e) {
            Log::error('Worklist refresh exception', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Connection error: ' . $e->getMessage()]);
        }
    }

    public function updateStatus(Request $request, WorklistItem $item): RedirectResponse
    {
        $validated = $request->validate(['status' => 'required|string']);

        if ($validated['status'] === WorklistItem::STATUS_CANCELLED && in_array($item->status, [WorklistItem::STATUS_REGISTERED, WorklistItem::STATUS_MW_PUBLISHED])) {
            $server = Server::find($item->server_id);
            if ($server) {
                try {
                    $client = new Client($server);
                    $client->raw('DELETE', "workitems/{$item->accession_number}", [], 'mwl');
                } catch (\Exception $e) {
                    return back()->withErrors(['error' => 'Gagal cancel MWL di DCM4CHEE: ' . $e->getMessage()]);
                }
            }
        }

        $item->update(['status' => $validated['status']]);
        return back()->with('success', 'Status updated.');
    }

    public function setServer(Request $request): RedirectResponse
    {
        $validated = $request->validate(['server_id' => 'required|exists:servers,id']);
        $request->session()->put('worklist_server_id', $validated['server_id']);
        return back();
    }
}
