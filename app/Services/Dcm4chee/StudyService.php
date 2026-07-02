<?php

namespace App\Services\Dcm4chee;

use App\Models\Server;

class StudyService
{
    protected Client $client;

    public function __construct(
        protected Server $server,
    ) {
        $this->client = new Client($server);
    }

    public function search(?string $patientName = null, ?string $patientId = null, ?string $studyDate = null, ?string $accessionNumber = null, ?string $studyUid = null, int $limit = 50, int $offset = 0): array
    {
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

        return $this->client->get('studies', $query, [
            'Accept' => 'application/dicom+json',
        ]);
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

    public function metadata(string $studyUid, string $seriesUid, string $instanceUid): array
    {
        return $this->client->get("studies/{$studyUid}/series/{$seriesUid}/instances/{$instanceUid}/metadata", headers: [
            'Accept' => 'application/dicom+json',
        ]);
    }

    public function rendered(string $studyUid, string $seriesUid, string $instanceUid): string
    {
        $response = $this->client->raw('GET', "studies/{$studyUid}/series/{$seriesUid}/instances/{$instanceUid}/rendered", [
            'headers' => [
                'Accept' => 'image/jpeg',
            ],
        ]);

        return $response->body();
    }

    public function thumbnail(string $studyUid, string $seriesUid, string $instanceUid): string
    {
        $response = $this->client->raw('GET', "studies/{$studyUid}/series/{$seriesUid}/instances/{$instanceUid}/rendered", [
            'query' => ['viewport' => 256, 'dw' => 256, 'dh' => 256],
            'headers' => [
                'Accept' => 'image/jpeg',
            ],
        ]);

        return $response->body();
    }
}
