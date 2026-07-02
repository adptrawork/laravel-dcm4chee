<?php

namespace App\Services\Ris;

use App\Models\Server;
use App\Models\WorklistItem;
use App\Services\Dcm4chee\DicomHelper;
use App\Services\Dcm4chee\StudyService;

class StudyPollerService
{
    public function poll(Server $server): array
    {
        $matched = 0;

        $items = WorklistItem::byServer($server->id)
            ->whereIn('status', [
                WorklistItem::STATUS_MW_PUBLISHED,
                WorklistItem::STATUS_TAKEN_BY_MODALITY,
                WorklistItem::STATUS_ACQUIRING,
                WorklistItem::STATUS_ACQUIRED,
            ])
            ->whereNotNull('accession_number')
            ->get();

        if ($items->isEmpty()) {
            return ['matched' => 0, 'message' => 'No items to poll.'];
        }

        $service = new StudyService($server);
        $accessions = $items->pluck('accession_number')->toArray();

        foreach ($accessions as $accession) {
            $studiesData = $service->search(accessionNumber: $accession, limit: 5);

            if (empty($studiesData)) continue;

            $item = $items->firstWhere('accession_number', $accession);
            if (!$item) continue;

            $study = DicomHelper::flattenStudies($studiesData)[0];
            $studyUid = $study['StudyInstanceUID'] ?? null;

            if (!$studyUid) continue;

            $seriesCount = (int) ($study['NumberOfStudyRelatedSeries'] ?? 0);
            $instanceCount = (int) ($study['NumberOfStudyRelatedInstances'] ?? 0);

            $updates = [
                'study_instance_uid' => $studyUid,
            ];

            if ($instanceCount > 0) {
                $updates['status'] = WorklistItem::STATUS_ARCHIVED;
                $updates['archived_at'] = now();
            } else {
                $updates['status'] = WorklistItem::STATUS_SENT_TO_PACS;
            }

            $item->update($updates);
            $matched++;
        }

        return ['matched' => $matched, 'message' => "Matched {$matched} items."];
    }
}
