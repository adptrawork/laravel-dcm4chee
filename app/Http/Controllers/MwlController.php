<?php

namespace App\Http\Controllers;

use App\Models\Server;
use App\Services\Dcm4chee\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MwlController extends Controller
{
    public function index(): View
    {
        $servers = Server::where('enabled', true)->get();
        $serverId = session('mwl_server_id', $servers->first()?->id);

        return view('mwl.index', [
            'servers' => $servers,
            'serverId' => $serverId,
        ]);
    }

    public function create(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'server_id' => 'required|exists:servers,id',
            'patient_name' => 'required|string|max:255',
            'patient_id' => 'required|string|max:255',
            'modality' => 'required|string|max:16',
            'procedure_description' => 'required|string|max:255',
            'requesting_physician' => 'required|string|max:255',
            'scheduled_date' => 'required|date_format:Y-m-d',
            'scheduled_time' => 'required|date_format:H:i',
        ]);

        $server = Server::findOrFail($validated['server_id']);
        $client = new Client($server);

        // Build DICOM JSON payload
        $dicomJson = $this->buildMwlDicomJson($validated);

        try {
            $response = $client->raw('POST', 'mwl/workitems', [
                'headers' => ['Content-Type' => 'application/dicom+json'],
                'body' => json_encode($dicomJson, JSON_UNESCAPED_SLASHES),
            ]);

            if ($response->successful()) {
                return back()->with('success', 'MWL entry berhasil dibuat.');
            } else {
                $errorBody = json_decode($response->body(), true);
                $errorMessage = $errorBody['00081198']['Value'][0]['00081197']['Value'][0] ?? $response->body();
                return back()->withErrors(['mwl_error' => 'Gagal membuat MWL: ' . $errorMessage])->withInput();
            }
        } catch (\Exception $e) {
            return back()->withErrors(['mwl_error' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
        }
    }

    protected function buildMwlDicomJson(array $data): array
    {
        // Generate UIDs and Accession Number
        $studyInstanceUID = $this->generateUid();
        $spsId = 'SPS' . now()->format('YmdHis');
        $accessionNumber = strtoupper($data['modality']) . now()->format('YmdHis') . rand(100, 999);

        return [
            '00080005' => ['vr' => 'CS', 'Value' => ['ISO_IR 100']], // Specific Character Set
            '00080050' => ['vr' => 'SH', 'Value' => [$accessionNumber]], // Accession Number
            '00080060' => ['vr' => 'CS', 'Value' => [$data['modality']]], // Modality
            '00080090' => ['vr' => 'PN', 'Value' => [['Alphabetic' => $data['requesting_physician']]]], // Referring Physician Name
            '00100010' => ['vr' => 'PN', 'Value' => [['Alphabetic' => $data['patient_name']]]], // Patient Name
            '00100020' => ['vr' => 'LO', 'Value' => [$data['patient_id']]], // Patient ID
            '00100030' => ['vr' => 'DA', 'Value' => [str_replace('-', '', now()->subYears(30)->format('Ymd'))]], // Patient Birth Date (placeholder: 30 years ago)
            '00100040' => ['vr' => 'CS', 'Value' => ['O']], // Patient Sex (Other - placeholder)
            '0020000D' => ['vr' => 'UI', 'Value' => [$studyInstanceUID]], // Study Instance UID
            '00321060' => ['vr' => 'LO', 'Value' => [$data['procedure_description']]], // Requested Procedure Description
            '00321062' => ['vr' => 'LO', 'Value' => [$data['procedure_description']]], // Reason for Requested Procedure

            // Scheduled Procedure Step Sequence (0040,0100)
            '00400100' => [
                'vr' => 'SQ',
                'Value' => [
                    [
                        '00400001' => ['vr' => 'AE', 'Value' => ['PZDR']], // Scheduled Station AE Title
                        '00400002' => ['vr' => 'DA', 'Value' => [str_replace('-', '', $data['scheduled_date'])]], // Scheduled Procedure Step Start Date
                        '00400003' => ['vr' => 'TM', 'Value' => [str_replace(':', '', $data['scheduled_time']) . '00']], // Scheduled Procedure Step Start Time
                        '00400006' => ['vr' => 'PN', 'Value' => [['Alphabetic' => $data['requesting_physician']]]], // Scheduled Performing Physician Name
                        '00400007' => ['vr' => 'LO', 'Value' => [$data['procedure_description']]], // Scheduled Procedure Step Description
                        '00400009' => ['vr' => 'SH', 'Value' => [$spsId]], // Scheduled Procedure Step ID
                        '00400010' => [
                            'vr' => 'SQ',
                            'Value' => [
                                [
                                    '00080060' => ['vr' => 'CS', 'Value' => [$data['modality']]], // Modality
                                ]
                            ]
                        ], // Scheduled Action Item Sequence
                        '00400020' => ['vr' => 'CS', 'Value' => ['PLANNED']], // Scheduled Procedure Step Status (PLANNED)

                        '00400012' => [
                            'vr' => 'SQ',
                            'Value' => [
                                [
                                    '00080100' => ['vr' => 'SH', 'Value' => [strtoupper($data['modality'])]], // Code Value
                                    '00080102' => ['vr' => 'SH', 'Value' => ['DCM']], // Coding Scheme Designator
                                    '00080104' => ['vr' => 'LO', 'Value' => [$data['procedure_description']]], // Code Meaning
                                ]
                            ]
                        ], // Scheduled Protocol Code Sequence
                    ],
                ],
            ],
            // Requested Procedure Code Sequence (0032,1064)
            '00321064' => [
                'vr' => 'SQ',
                'Value' => [
                    [
                        '00080100' => ['vr' => 'SH', 'Value' => [strtoupper($data['modality'])]], // Code Value
                        '00080102' => ['vr' => 'SH', 'Value' => ['DCM']], // Coding Scheme Designator
                        '00080104' => ['vr' => 'LO', 'Value' => [$data['procedure_description']]], // Code Meaning
                    ]
                ]
            ],
        ];
    }

    protected function generateUid(): string
    {
        // Implement DICOM UID generation logic here
        // Example: 1.2.826.0.1.3680043.8.498.1.{timestamp}.{random}
        return '1.2.826.0.1.3680043.8.498.1.' . now()->format('YmdHisu') . '.' . random_int(1000, 9999);
    }
}
