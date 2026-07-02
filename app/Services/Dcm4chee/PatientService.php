<?php

namespace App\Services\Dcm4chee;

use App\Models\Server;

class PatientService
{
    protected Client $client;

    public function __construct(
        protected Server $server,
    ) {
        $this->client = new Client($server);
    }

    public function search(?string $name = null, ?string $patientId = null, ?string $studyDate = null, int $limit = 50, int $offset = 0): array
    {
        $query = [
            'limit' => $limit,
            'offset' => $offset,
        ];

        if ($name) {
            $query['PatientName'] = $name;
        }

        if ($patientId) {
            $query['PatientID'] = $patientId;
        }

        if ($studyDate) {
            $query['StudyDate'] = $studyDate;
        }

        return $this->client->get('patients', $query, [
            'Accept' => 'application/dicom+json',
        ]);
    }

    public function findById(string $patientId): array
    {
        return $this->client->get("patients/{$patientId}", headers: [
            'Accept' => 'application/dicom+json',
        ]);
    }

    public function create(array $dicomJson): array
    {
        return $this->client->post('patients', $dicomJson, [
            'Content-Type' => 'application/dicom+json',
        ]);
    }

    public function update(string $patientId, array $dicomJson): array
    {
        return $this->client->put("patients/{$patientId}", $dicomJson, [
            'Content-Type' => 'application/dicom+json',
        ]);
    }

    public function delete(string $patientId): array
    {
        return $this->client->delete("patients/{$patientId}");
    }

    public function studies(string $patientId): array
    {
        return $this->client->get("patients/{$patientId}/studies", headers: [
            'Accept' => 'application/dicom+json',
        ]);
    }
}
