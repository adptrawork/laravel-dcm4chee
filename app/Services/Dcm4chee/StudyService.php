<?php

declare(strict_types=1);

namespace App\Services\Dcm4chee;

use App\Models\Server;

final class StudyService
{
    protected Client $client;

    public function __construct(
        protected Server $server,
    ) {
        $this->client = new Client($server);
    }

    private function dicomValue(array $json, string $tag): mixed
    {
        return $json[$tag]['Value'][0] ?? null;
    }

    private function flattenStudy(array $raw): array
    {
        $e = fn (string $t) => $this->dicomValue($raw, $t);
        $pn = fn (string $t) => is_array($e($t)) ? ($e($t)['Alphabetic'] ?? null) : (is_string($e($t)) ? $e($t) : null);
        $mods = $raw['00080061']['Value'] ?? [];
        if (is_string($mods)) {
            $mods = [$mods];
        }

        return [
            'patientName' => $pn('00100010'),
            'patientId' => $e('00100020'),
            'studyDate' => $e('00080020'),
            'studyDescription' => $e('00081030'),
            'accessionNumber' => $e('00080050'),
            'referringPhysician' => $pn('00080090'),
            'modalities' => $mods,
            'series' => (int) ($e('00201206') ?? 0),
            'instances' => (int) ($e('00201208') ?? 0),
            'studyUid' => $e('0020000D'),
        ];
    }

    public function search(
        ?string $patientName = null,
        ?string $patientId = null,
        ?string $studyDate = null,
        ?string $accessionNumber = null,
        ?string $studyUid = null,
        int $limit = 50,
        int $offset = 0,
    ): array {
        $query = [
            'limit' => $limit,
            'offset' => $offset,
        ];

        if ($patientName) {
            $query['PatientName'] = $patientName;
        }
        if ($patientId) {
            $query['PatientID'] = $patientId;
        }
        if ($studyDate) {
            $query['StudyDate'] = $studyDate;
        }
        if ($accessionNumber) {
            $query['AccessionNumber'] = $accessionNumber;
        }
        if ($studyUid) {
            $query['StudyInstanceUID'] = $studyUid;
        }

        $data = $this->client->get('studies', $query, [
            'Accept' => 'application/dicom+json',
        ]);

        return array_map(fn (array $s) => $this->flattenStudy($s), $data);
    }

    public function count(
        ?string $patientName = null,
        ?string $patientId = null,
        ?string $studyDate = null,
        ?string $accessionNumber = null,
    ): int {
        $query = ['limit' => 0, 'offset' => 0];

        if ($patientName) {
            $query['PatientName'] = $patientName;
        }
        if ($patientId) {
            $query['PatientID'] = $patientId;
        }
        if ($studyDate) {
            $query['StudyDate'] = $studyDate;
        }
        if ($accessionNumber) {
            $query['AccessionNumber'] = $accessionNumber;
        }

        try {
            $response = $this->client->raw('GET', 'studies', [
                'query' => $query,
                'headers' => ['Accept' => 'application/dicom+json'],
            ]);

            // DCM4CHEE returns total count in X-Total-Count header
            return (int) ($response->header('X-Total-Count') ?? 0);
        } catch (\Throwable) {
            return 0;
        }
    }

    private function flattenSeries(array $raw): array
    {
        $e = fn (string $t) => $this->dicomValue($raw, $t);

        return [
            'seriesNumber' => (string) ($e('00200011') ?? ''),
            'seriesDescription' => $e('0008103E'),
            'modality' => $e('00080060'),
            'instances' => (int) ($e('00201209') ?? 0),
            'seriesUid' => $e('0020000E'),
        ];
    }

    public function getSeriesByStudyUid(string $studyUid): array
    {
        $data = $this->client->get("studies/{$studyUid}/series", headers: [
            'Accept' => 'application/dicom+json',
        ]);

        return array_map(fn (array $s) => $this->flattenSeries($s), $data);
    }

    public function getInstances(string $studyUid, string $seriesUid): array
    {
        $data = $this->client->get("studies/{$studyUid}/series/{$seriesUid}/instances", headers: [
            'Accept' => 'application/dicom+json',
        ]);

        return array_map(fn (array $raw) => [
            'instanceNumber' => (string) ($this->dicomValue($raw, '00200013') ?? ''),
            'sopClassUid' => $this->dicomValue($raw, '00080016'),
            'instanceUid' => $this->dicomValue($raw, '00080018'),
            'numberOfFrames' => $this->dicomValue($raw, '00280008'),
        ], $data);
    }

    public function getInstanceMetadata(string $instanceUid): array
    {
        return $this->client->get("instances/{$instanceUid}/metadata", headers: [
            'Accept' => 'application/dicom+json',
        ]);
    }
}
