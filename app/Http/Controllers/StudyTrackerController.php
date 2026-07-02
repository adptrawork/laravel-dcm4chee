<?php

namespace App\Http\Controllers;

use App\Models\Server;
use App\Models\WorklistItem;
use App\Services\Dcm4chee\DicomHelper;
use App\Services\Dcm4chee\StudyService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudyTrackerController extends Controller
{
    public function index(Request $request): View
    {
        $servers = Server::where('enabled', true)->get();
        $serverId = $request->session()->get('active_server_id', $servers->first()?->id);
        $query = $request->input('query', '');
        $status = $request->input('status', '');

        $items = WorklistItem::with('server')
            ->when($serverId, fn($q) => $q->where('server_id', $serverId))
            ->when($query, function ($q) use ($query) {
                $q->where(function ($q) use ($query) {
                    $q->where('patient_name', 'like', "%{$query}%")
                      ->orWhere('accession_number', 'like', "%{$query}%")
                      ->orWhere('patient_id', 'like', "%{$query}%");
                });
            })
            ->when($status, fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(25);

        return view('study-tracker.index', compact('servers', 'serverId', 'items', 'query', 'status'));
    }

    public function show(Request $request, string $accession): View
    {
        $servers = Server::where('enabled', true)->get();
        $serverId = $request->session()->get('active_server_id', $servers->first()?->id);

        $item = WorklistItem::where('accession_number', $accession)->firstOrFail();

        $study = [];
        if ($item->study_instance_uid && $serverId) {
            $server = Server::find($serverId);
            if ($server) {
                $service = new StudyService($server);
                $studiesData = $service->search(studyUid: $item->study_instance_uid);
                $study = !empty($studiesData) ? DicomHelper::flattenStudies($studiesData)[0] : [];
            }
        }

        return view('study-tracker.show', compact('servers', 'serverId', 'item', 'study'));
    }
}
