<?php

namespace App\Http\Controllers;

use App\Models\Server;
use App\Services\Dcm4chee\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DebugController extends Controller
{
    const TEST_CASES = [
        // ── Patient ──
        'patient-list' => [
            'group' => 'Patient',
            'label' => 'Search Patients',
            'method' => 'GET',
            'path' => 'patients?limit=5',
            'headers' => "Accept: application/dicom+json",
            'body' => '',
        ],
        'patient-by-id' => [
            'group' => 'Patient',
            'label' => 'Get Patient by ID',
            'method' => 'GET',
            'path' => 'patients/{id}',
            'headers' => "Accept: application/dicom+json",
            'body' => '',
        ],
        'patient-create' => [
            'group' => 'Patient',
            'label' => 'Create New Patient',
            'method' => 'POST',
            'path' => 'patients',
            'headers' => "Content-Type: application/dicom+json",
            'body' => '{
  "00100010": { "vr": "PN", "Value": [{"Alphabetic": "Test^Patient"}] },
  "00100020": { "vr": "LO", "Value": ["TEST001"] },
  "00100030": { "vr": "DA", "Value": ["19900101"] },
  "00100040": { "vr": "CS", "Value": ["O"] }
}',
        ],
        'patient-studies' => [
            'group' => 'Patient',
            'label' => 'Patient Studies',
            'method' => 'GET',
            'path' => 'patients/{id}/studies',
            'headers' => "Accept: application/dicom+json",
            'body' => '',
        ],

        // ── Study ──
        'study-list' => [
            'group' => 'Study',
            'label' => 'Search Studies',
            'method' => 'GET',
            'path' => 'studies?limit=5',
            'headers' => "Accept: application/dicom+json",
            'body' => '',
        ],
        'study-series' => [
            'group' => 'Study',
            'label' => 'Series in Study',
            'method' => 'GET',
            'path' => 'studies/{StudyInstanceUID}/series',
            'headers' => "Accept: application/dicom+json",
            'body' => '',
        ],
        'study-instances' => [
            'group' => 'Study',
            'label' => 'Instances in Series',
            'method' => 'GET',
            'path' => 'studies/{StudyInstanceUID}/series/{SeriesInstanceUID}/instances',
            'headers' => "Accept: application/dicom+json",
            'body' => '',
        ],
        'study-metadata' => [
            'group' => 'Study',
            'label' => 'Instance Metadata',
            'method' => 'GET',
            'path' => 'studies/{StudyInstanceUID}/series/{SeriesInstanceUID}/instances/{SOPInstanceUID}/metadata',
            'headers' => "Accept: application/dicom+json",
            'body' => '',
        ],

        // ── WADO ──
        'wado-rendered' => [
            'group' => 'WADO',
            'label' => 'Rendered Image (JPEG)',
            'method' => 'GET',
            'path' => 'studies/{StudyInstanceUID}/series/{SeriesInstanceUID}/instances/{SOPInstanceUID}/rendered?viewport=512',
            'headers' => "Accept: image/jpeg",
            'body' => '',
        ],
        'wado-thumbnail' => [
            'group' => 'WADO',
            'label' => 'Thumbnail (256px)',
            'method' => 'GET',
            'path' => 'studies/{StudyInstanceUID}/series/{SeriesInstanceUID}/instances/{SOPInstanceUID}/rendered?viewport=256&dw=256&dh=256',
            'headers' => "Accept: image/jpeg",
            'body' => '',
        ],

        // ── Monitoring ──
        'health' => [
            'group' => 'Monitoring',
            'label' => 'Health Check',
            'method' => 'GET',
            'path' => 'monitoring/health',
            'headers' => '',
            'body' => '',
        ],
        'device-info' => [
            'group' => 'Monitoring',
            'label' => 'Device Info',
            'method' => 'GET',
            'path' => 'monitoring/device',
            'headers' => '',
            'body' => '',
        ],
        'aet-list' => [
            'group' => 'Monitoring',
            'label' => 'AE Titles',
            'method' => 'GET',
            'path' => 'monitoring/aets',
            'headers' => '',
            'body' => '',
        ],
        'cecho' => [
            'group' => 'Monitoring',
            'label' => 'C-Echo Test',
            'method' => 'GET',
            'path' => 'monitoring/echoscp',
            'headers' => '',
            'body' => '',
        ],

        // ── Stow ──
        'stow-single' => [
            'group' => 'STOW-RS',
            'label' => 'Upload DICOM File (STOW-RS)',
            'method' => 'POST',
            'path' => 'studies',
            'headers' => "Content-Type: multipart/related; type=\"application/dicom\"; boundary=STOWBOUNDARY
Accept: application/dicom+json",
            'body' => '--STOWBOUNDARY
Content-Type: application/dicom

{binary DICOM data here}
--STOWBOUNDARY--',
        ],
    ];

    public function index(Request $request): View
    {
        $servers = Server::where('enabled', true)->get();
        $serverId = $request->session()->get('debug_server_id', $servers->first()?->id);

        return view('debug.index', [
            'servers' => $servers,
            'serverId' => $serverId,
            'testCases' => self::TEST_CASES,
        ]);
    }

    public function send(Request $request): View|RedirectResponse
    {
        $validated = $request->validate([
            'server_id' => 'required|exists:servers,id',
            'method' => 'required|in:GET,POST,PUT,DELETE',
            'path' => 'required|string|max:500',
            'headers' => 'nullable|string',
            'body' => 'nullable|string',
        ]);

        $server = Server::findOrFail($validated['server_id']);
        $client = new Client($server);

        $method = strtolower($validated['method']);
        $path = $validated['path'];
        $headers = $this->parseHeaders($validated['headers'] ?? '');

        $options = ['headers' => $headers];

        if (in_array($method, ['post', 'put']) && !empty($validated['body'])) {
            $options['body'] = $validated['body'];
            if (!isset($headers['Content-Type'])) {
                $options['headers']['Content-Type'] = 'application/dicom+json';
            }
        }

        $start = microtime(true);

        try {
            $response = $client->raw($validated['method'], $path, $options);
            $duration = (int) ((microtime(true) - $start) * 1000);

            $result = [
                'status' => $response->status(),
                'headers' => $response->headers(),
                'body' => $this->formatBody($response->body(), $response->header('Content-Type')),
                'duration' => $duration,
                'error' => null,
            ];
        } catch (\Exception $e) {
            $result = [
                'status' => null,
                'headers' => [],
                'body' => null,
                'duration' => 0,
                'error' => $e->getMessage(),
            ];
        }

        $request->session()->put('debug_server_id', $validated['server_id']);

        $servers = Server::where('enabled', true)->get();
        $serverId = $validated['server_id'];

        return view('debug.index', [
            'servers' => $servers,
            'serverId' => $serverId,
            'testCases' => self::TEST_CASES,
            'result' => $result,
            'oldMethod' => $validated['method'],
            'oldPath' => $validated['path'],
            'oldHeaders' => $validated['headers'] ?? '',
            'oldBody' => $validated['body'] ?? '',
        ]);
    }

    public function setServer(Request $request): RedirectResponse
    {
        $validated = $request->validate(['server_id' => 'required|exists:servers,id']);
        $request->session()->put('debug_server_id', $validated['server_id']);

        return back();
    }

    protected function parseHeaders(string $raw): array
    {
        $headers = [];
        foreach (explode("\n", $raw) as $line) {
            $line = trim($line);
            if (str_contains($line, ':')) {
                [$key, $value] = explode(':', $line, 2);
                $headers[trim($key)] = trim($value);
            }
        }
        return $headers;
    }

    protected function formatBody(string $body, ?string $contentType): string
    {
        if ($body === '') {
            return '(empty)';
        }

        if ($contentType && str_contains($contentType, 'json')) {
            $decoded = json_decode($body, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            }
        }

        return $body;
    }
}
