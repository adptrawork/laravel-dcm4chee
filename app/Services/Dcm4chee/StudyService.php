<?php

declare(strict_types=1);

namespace App\Services\Dcm4chee;

use App\Dto\SeriesData;
use App\Models\Server;

final class StudyService
{
    protected Client $client;
    protected const int DEFAULT_LIMIT = 50;

    public function __construct(
        protected Server $server,
    ) {
        $this->client = new Client($server);
    }

    public function search(
        ?string $patientName = null,
        ?string $patientId = null,
        ?string $studyDate = null,
        ?string $accessionNumber = null,
        ?string $studyUid = null,
        int $limit = self::DEFAULT_LIMIT,
        int $offset = 0,
    ): array {
        $query = [
            'limit' => $limit,
            'offset' => $offset,
        ];

        if ($patientName) $query['PatientName'] = $patientName;
        if ($patientId) $query['PatientID'] = $patientId;
        if ($studyDate) $query['StudyDate'] = $studyDate;
        if ($accessionNumber) $query['AccessionNumber'] = $accessionNumber;
        if ($studyUid) $query['StudyInstanceUID'] = $studyUid;

        $data = $this->client->get('studies', $query, [
            'Accept' => 'application/dicom+json',
        ]);

        return DicomHelper::flattenToDto($data);
    }

    public function count(
        ?string $patientName = null,
        ?string $patientId = null,
        ?string $studyDate = null,
        ?string $accessionNumber = null,
    ): int {
        $query = ['limit' => 0, 'offset' => 0];

        if ($patientName) $query['PatientName'] = $patientName;
        if ($patientId) $query['PatientID'] = $patientId;
        if ($studyDate) $query['StudyDate'] = $studyDate;
        if ($accessionNumber) $query['AccessionNumber'] = $accessionNumber;

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

    public function series(string $studyUid): array
    {
        return $this->client->get("studies/{$studyUid}/series", headers: [
            'Accept' => 'application/dicom+json',
        ]);
    }

    public function instances(string $studyUid, string $seriesUid): array
    {
        return $this->client->get("studies/{$studyUid}/series/{$seriesUid}/instances", headers: [
            'Accept' => 'application/dicom+json',
        ]);
    }

    public function rendered(string $studyUid, string $seriesUid, string $instanceUid): string
    {
        return $this->client->raw('GET', "studies/{$studyUid}/series/{$seriesUid}/instances/{$instanceUid}/rendered", [
            'headers' => ['Accept' => 'image/jpeg'],
        ])->body();
    }

    public function thumbnail(string $studyUid, string $seriesUid, string $instanceUid): string
    {
        return $this->client->raw('GET', "studies/{$studyUid}/series/{$seriesUid}/instances/{$instanceUid}/rendered", [
            'query' => ['viewport' => 256, 'dw' => 256, 'dh' => 256],
            'headers' => ['Accept' => 'image/jpeg'],
        ])->body();
    }

    public function getSeriesByStudyUid(string $studyUid): array
    {
        $data = $this->client->get("studies/{$studyUid}/series", headers: [
            'Accept' => 'application/dicom+json',
        ]);

        return array_map(fn (array $s) => SeriesData::fromDicomJson($s), $data);
    }

    public function metadata(string $studyUid, string $seriesUid, string $instanceUid): array
    {
        return $this->client->get("studies/{$studyUid}/series/{$seriesUid}/instances/{$instanceUid}/metadata", headers: [
            'Accept' => 'application/dicom+json',
        ]);
    }
}
