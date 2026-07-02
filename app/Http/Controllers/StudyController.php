<?php

namespace App\Http\Controllers;

use App\Models\Server;
use App\Services\Dcm4chee\DicomHelper;
use App\Services\Dcm4chee\StudyService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudyController extends Controller
{
    protected function getServer(Request $request): Server
    {
        $serverId = $request->session()->get('active_server_id');

        if (!$serverId) {
            abort(400, 'No active server selected.');
        }

        return Server::findOrFail($serverId);
    }

    public function index(Request $request): View
    {
        $servers = Server::where('enabled', true)->get();
        $serverId = $request->session()->get('active_server_id', $servers->first()?->id);
        $studies = [];
        $query = [
            'patientName' => $request->input('patientName'),
            'patientId' => $request->input('patientId'),
            'studyDate' => $request->input('studyDate'),
            'accessionNumber' => $request->input('accessionNumber'),
        ];

        if ($serverId) {
            $server = Server::find($serverId);
            if ($server) {
                $service = new StudyService($server);
                $studiesData = $service->search(
                    patientName: $query['patientName'] ?? null,
                    patientId: $query['patientId'] ?? null,
                    studyDate: $query['studyDate'] ?? null,
                    accessionNumber: $query['accessionNumber'] ?? null,
                    limit: 50,
                );
                $studies = DicomHelper::flattenStudies($studiesData);
            }
        }

        return view('studies.index', compact('servers', 'serverId', 'studies', 'query'));
    }

    public function show(Request $request, string $studyUid): View
    {
        $servers = Server::where('enabled', true)->get();
        $serverId = $request->session()->get('active_server_id', $servers->first()?->id);

        if (!$serverId) {
            abort(400, 'No active server configured.');
        }

        $server = Server::findOrFail($serverId);
        $service = new StudyService($server);

        $studiesData = $service->search(studyUid: $studyUid);
        $study = !empty($studiesData) ? DicomHelper::flattenStudies($studiesData)[0] : [];

        $seriesData = $service->series($studyUid);
        $series = DicomHelper::flattenSeries($seriesData);

        return view('studies.show', compact('server', 'study', 'studyUid', 'series'));
    }

    public function series(Request $request, string $studyUid, string $seriesUid): View
    {
        $serverId = $request->session()->get('active_server_id');
        $server = Server::findOrFail($serverId);
        $service = new StudyService($server);

        $instances = $service->instances($studyUid, $seriesUid);

        $seriesData = $service->series($studyUid);
        $seriesInfo = [];
        foreach ($seriesData as $s) {
            if (DicomHelper::extractValue($s, '0020000E') === $seriesUid) {
                $seriesInfo = DicomHelper::flattenSeries([$s])[0];
                break;
            }
        }

        return view('studies.series', compact('server', 'studyUid', 'seriesUid', 'seriesInfo', 'instances'));
    }

    public function metadata(Request $request, string $studyUid, string $seriesUid, string $instanceUid): View
    {
        $server = $this->getServer($request);
        $service = new StudyService($server);
        $data = $service->metadata($studyUid, $seriesUid, $instanceUid);

        return view('studies.metadata', compact('server', 'studyUid', 'seriesUid', 'instanceUid', 'data'));
    }

    public function rendered(Request $request, string $studyUid, string $seriesUid, string $instanceUid): \Illuminate\Http\Response
    {
        $server = $this->getServer($request);
        $service = new StudyService($server);
        $image = $service->rendered($studyUid, $seriesUid, $instanceUid);

        return response($image, 200, ['Content-Type' => 'image/jpeg']);
    }
}
