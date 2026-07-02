<?php

namespace App\Http\Controllers;

use App\Models\ExaminationTemplate;
use App\Models\MwlConfig;
use App\Models\Server;
use App\Models\WorklistItem;
use App\Services\Dcm4chee\Client;
use App\Services\Dcm4chee\DicomHelper;
use App\Services\Dcm4chee\PatientService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    public function index(Request $request): View
    {
        $servers = Server::where('enabled', true)->get();
        $serverId = $request->session()->get('registration_server_id', $servers->first()?->id);
        $patients = [];
        $selectedPatient = null;
        $query = [
            'search_name' => $request->input('search_name'),
            'search_id' => $request->input('search_id'),
        ];

        if ($serverId && ($query['search_name'] || $query['search_id'])) {
            $server = Server::find($serverId);
            if ($server) {
                $service = new PatientService($server);
                $patients = $service->search(
                    name: $query['search_name'] ?? null,
                    patientId: $query['search_id'] ?? null,
                    limit: 20,
                );
            }
        }

        if ($request->has('select_patient') && $serverId) {
            $server = Server::find($serverId);
            if ($server) {
                $service = new PatientService($server);
                $data = $service->findById($request->input('select_patient'));
                $selectedPatient = DicomHelper::flattenPatient($data);
            }
        }

        $templates = ExaminationTemplate::orderBy('sort_order')->get();
        $mwlConfig = MwlConfig::where('server_id', $serverId)->first();

        return view('registration.index', compact(
            'servers', 'serverId', 'patients', 'query', 'selectedPatient', 'templates', 'mwlConfig'
        ));
    }

    public function searchPatients(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'server_id' => 'required|exists:servers,id',
            'search_name' => 'nullable|string|max:255',
            'search_id' => 'nullable|string|max:255',
        ]);

        $request->session()->put('registration_server_id', $validated['server_id']);

        return to_route('registration.index', [
            'search_name' => $validated['search_name'] ?? null,
            'search_id' => $validated['search_id'] ?? null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'server_id' => 'required|exists:servers,id',
            'action' => 'required|in:new,existing',
            'patient_id' => 'required_if:action,existing|string|max:255',
            'patient_name' => 'required_if:action,new|string|max:255',
            'new_patient_id' => 'required_if:action,new|string|max:255',
            'birth_date' => 'nullable|date_format:Y-m-d',
            'gender' => 'nullable|in:M,F,O',
            'nik' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'requesting_physician' => 'nullable|string|max:255',
            'modality' => 'required|string|max:16',
            'procedure_description' => 'required|string|max:255',
            'room' => 'nullable|string|max:50',
            'priority' => 'required|in:routine,urgent,stat',
            'scheduled_date' => 'required|date_format:Y-m-d',
            'scheduled_time' => 'required|date_format:H:i',
        ]);

        $server = Server::findOrFail($validated['server_id']);
        $request->session()->put('registration_server_id', $validated['server_id']);

        $accessionNumber = strtoupper($validated['modality']) . now()->format('YmdHis') . random_int(100, 999);

        // Create patient in PACS if new
        if ($validated['action'] === 'new') {
            $patientDicom = DicomHelper::buildPatientJson(
                $validated['patient_name'],
                $validated['new_patient_id'],
                $validated['birth_date'] ?? null,
                $validated['gender'] ?? null,
            );

            try {
                $patientService = new PatientService($server);
                $patientService->create($patientDicom);
            } catch (\Exception $e) {
                return back()->withErrors(['error' => 'Gagal membuat pasien: ' . $e->getMessage()])->withInput();
            }

            $patientId = $validated['new_patient_id'];
            $patientName = $validated['patient_name'];
        } else {
            $patientId = $validated['patient_id'];
            $patientName = $validated['patient_name'] ?? $patientId;
        }

        // Create MWL
        $mwlDicom = $this->buildMwlJson($validated, $accessionNumber, $patientName, $patientId);

        try {
            $client = new Client($server);
            $response = $client->raw('POST', 'workitems', [
                'headers' => ['Content-Type' => 'application/dicom+json'],
                'body' => json_encode($mwlDicom, JSON_UNESCAPED_SLASHES),
            ], 'mwl');

            if (!$response->successful()) {
                return back()->withErrors(['error' => 'Gagal membuat MWL: ' . ($response->body() ?: '(empty)')])->withInput();
            }
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal membuat MWL: ' . $e->getMessage()])->withInput();
        }

        WorklistItem::create([
            'server_id' => $validated['server_id'],
            'accession_number' => $accessionNumber,
            'patient_name' => $patientName,
            'patient_id' => $patientId,
            'modality' => $validated['modality'],
            'procedure_description' => $validated['procedure_description'],
            'requesting_physician' => $validated['requesting_physician'],
            'scheduled_date' => str_replace('-', '', $validated['scheduled_date']),
            'scheduled_time' => str_replace(':', '', $validated['scheduled_time']),
            'status' => 'waiting',
        ]);

        return to_route('worklist.index')
            ->with('success', 'Pasien berhasil didaftarkan dan MWL created.');
    }

    protected function buildMwlJson(array $data, string $accessionNumber, string $patientName, string $patientId): array
    {
        return [
            '00080005' => ['vr' => 'CS', 'Value' => ['ISO_IR 100']],
            '00080050' => ['vr' => 'SH', 'Value' => [$accessionNumber]],
            '00080060' => ['vr' => 'CS', 'Value' => [$data['modality']]],
            '00100010' => ['vr' => 'PN', 'Value' => [['Alphabetic' => $patientName]]],
            '00100020' => ['vr' => 'LO', 'Value' => [$patientId]],
            '00321060' => ['vr' => 'LO', 'Value' => [$data['procedure_description']]],
            '00400100' => [
                'vr' => 'SQ',
                'Value' => [[
                    '00400001' => ['vr' => 'AE', 'Value' => ['PZDR']],
                    '00400002' => ['vr' => 'DA', 'Value' => [str_replace('-', '', $data['scheduled_date'])]],
                    '00400003' => ['vr' => 'TM', 'Value' => [str_replace(':', '', $data['scheduled_time']) . '00']],
                    '00400009' => ['vr' => 'SH', 'Value' => ['SPS' . now()->format('YmdHis')]],
                    '00400007' => ['vr' => 'LO', 'Value' => [$data['procedure_description']]],
                ]]
            ]
        ];
    }

    public function setServer(Request $request): RedirectResponse
    {
        $validated = $request->validate(['server_id' => 'required|exists:servers,id']);
        $request->session()->put('registration_server_id', $validated['server_id']);
        return back();
    }
}
