<?php

declare(strict_types=1);

namespace App\Services\Dcm4chee;

use App\Models\Server;

final class SeriesService
{
    protected Client $client;

    public function __construct(
        protected Server $server,
    ) {
        $this->client = new Client($server);
    }

    public function getByStudyUid(string $studyUid): array
    {
        $data = $this->client->get("studies/{$studyUid}/series", headers: [
            'Accept' => 'application/dicom+json',
        ]);

        return array_map(fn (array $s) => \App\Dto\SeriesData::fromDicomJson($s), $data);
    }
}
