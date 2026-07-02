<?php

namespace App\Http\Controllers;

use App\Models\Server;
use App\Models\WorklistItem;
use App\Services\Dcm4chee\Client;
use App\Services\Dcm4chee\PatientService;
use App\Services\Dcm4chee\DicomHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    public function index(Request $request): View
    {
        $servers = Server::where('enabled', true)->get();
        $serverId = $request->session()->get('registration_server_id', $servers->first()?->id);

        return view('registration.index', compact('servers', 'serverId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'server_id' => 'required|exists:servers,id',
            'patient_name' => 'required|string|max:255',
            'patient_id' => 'required|string|max:255',
            'nik' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date_format:Y-m-d',
            'gender' => 'nullable|in:M,F,O',
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

        $patientDicom = DicomHelper::buildPatientJson(
            $validated['patient_name'],
            $validated['patient_id'],
            $validated['birth_date'] ?? null,
            $validated['gender'] ?? null,
        );

        $accessionNumber = strtoupper($validated['modality']) . now()->format('YmdHis') . random_int(100, 999);

        $mwlDicom = $this->buildMwlJson($validated, $accessionNumber);

        try {
            $patientService = new PatientService($server);
            $patientService->create($patientDicom);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal membuat Patient: ' . $e->getMessage()])->withInput();
        }

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
            'patient_name' => $validated['patient_name'],
            'patient_id' => $validated['patient_id'],
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

    protected function buildMwlJson(array $data, string $accessionNumber): array
    {
        return [
            '00080005' => ['vr' => 'CS', 'Value' => ['ISO_IR 100']],
            '00080050' => ['vr' => 'SH', 'Value' => [$accessionNumber]],
            '00080060' => ['vr' => 'CS', 'Value' => [$data['modality']]],
            '00100010' => ['vr' => 'PN', 'Value' => [['Alphabetic' => $data['patient_name']]]],
            '00100020' => ['vr' => 'LO', 'Value' => [$data['patient_id']]],
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
