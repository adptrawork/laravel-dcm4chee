<?php

namespace App\Http\Controllers;

use App\Models\Server;
use App\Services\Dcm4chee\DicomHelper;
use App\Services\Dcm4chee\PatientService;
use App\Services\Dcm4chee\StudyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PatientController extends Controller
{
    public function index(Request $request): View
    {
        $servers = Server::where('enabled', true)->get();
        $serverId = $request->session()->get('active_server_id', $servers->first()?->id);
        $patients = [];
        $query = [
            'patientName' => $request->input('patientName'),
            'patientId' => $request->input('patientId'),
        ];

        if ($serverId) {
            $server = Server::find($serverId);
            if ($server) {
                $service = new PatientService($server);
                $patients = $service->search(
                    name: $query['patientName'] ?? null,
                    patientId: $query['patientId'] ?? null,
                    limit: 50,
                );
            }
        }

        return view('patients.index', compact('servers', 'serverId', 'patients', 'query'));
    }

    public function create(Request $request): View
    {
        $servers = Server::where('enabled', true)->get();
        $serverId = $request->session()->get('active_server_id', $servers->first()?->id);

        return view('patients.form', compact('servers', 'serverId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'server_id' => 'required|exists:servers,id',
            'patientName' => 'required|string|max:255',
            'patientId' => 'required|string|max:255',
            'patientBirthDate' => 'nullable|string|max:10',
            'patientSex' => 'nullable|in:M,F,O',
            'preview' => 'nullable|string',
        ]);

        if ($request->boolean('preview')) {
            return back()->withInput()->with('preview', $this->buildPreview($validated));
        }

        $server = Server::findOrFail($validated['server_id']);
        $dicomJson = DicomHelper::buildPatientJson(
            $validated['patientName'],
            $validated['patientId'],
            $validated['patientBirthDate'] ?? null,
            $validated['patientSex'] ?? null,
        );

        $service = new PatientService($server);
        $service->create($dicomJson);

        $request->session()->put('active_server_id', $validated['server_id']);

        return to_route('patients.index', ['patientId' => $validated['patientId']])
            ->with('success', 'Patient created successfully.');
    }

    public function show(Request $request, string $patientId): View
    {
        $servers = Server::where('enabled', true)->get();
        $serverId = $request->session()->get('active_server_id', $servers->first()?->id);

        if (!$serverId) {
            abort(400, 'No active server configured.');
        }

        $server = Server::findOrFail($serverId);
        $patientService = new PatientService($server);
        $studyService = new StudyService($server);

        $patientData = $patientService->findById($patientId);
        $patient = DicomHelper::flattenPatient($patientData);

        $studiesData = $patientService->studies($patientId);
        $studies = DicomHelper::flattenStudies($studiesData);

        return view('patients.show', compact('server', 'patient', 'patientId', 'studies'));
    }

    public function destroy(Request $request, string $patientId): RedirectResponse
    {
        $serverId = $request->session()->get('active_server_id');

        if (!$serverId) {
            return back()->with('error', 'No active server.');
        }

        $server = Server::findOrFail($serverId);
        $service = new PatientService($server);
        $service->delete($patientId);

        return to_route('patients.index')
            ->with('success', 'Patient deleted.');
    }

    public function setServer(Request $request): RedirectResponse
    {
        $validated = $request->validate(['server_id' => 'required|exists:servers,id']);
        $request->session()->put('active_server_id', $validated['server_id']);

        return back();
    }

    protected function buildPreview(array $data): array
    {
        return DicomHelper::buildPatientJson(
            $data['patientName'],
            $data['patientId'],
            $data['patientBirthDate'] ?? null,
            $data['patientSex'] ?? null,
        );
    }
}
