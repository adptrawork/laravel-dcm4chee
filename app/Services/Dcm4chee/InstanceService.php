<?php

declare(strict_types=1);

namespace App\Services\Dcm4chee;

use App\Models\Server;

final class InstanceService
{
    protected Client $client;

    public function __construct(
        protected Server $server,
    ) {
        $this->client = new Client($server);
    }

    public function getBySeriesUid(string $studyUid, string $seriesUid): array
    {
        $data = $this->client->get("studies/{$studyUid}/series/{$seriesUid}/instances", headers: [
            'Accept' => 'application/dicom+json',
        ]);

        return array_map(fn (array $i) => \App\Dto\InstanceData::fromDicomJson($i), $data);
    }
}
