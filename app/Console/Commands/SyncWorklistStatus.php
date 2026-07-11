<?php

namespace App\Console\Commands;

use App\Models\Server;
use App\Models\WorklistItem;
use App\Services\Dcm4chee\StudyService;
use Illuminate\Console\Command;

class SyncWorklistStatus extends Command
{
    protected $signature = 'worklist:sync-status
        {--server= : Server ID to query (default: first enabled)}
        {--dry-run : Show what would change without updating}';

    protected $description = 'Poll PACS QIDO-RS to sync worklist item statuses';

    const PRE_SCAN_STATUSES = [
        WorklistItem::STATUS_MW_PUBLISHED,
        WorklistItem::STATUS_TAKEN_BY_MODALITY,
        WorklistItem::STATUS_ACQUIRING,
    ];

    public function handle(StudyService $studyService): int
    {
        $serverId = $this->option('server');
        $server = $serverId
            ? Server::find($serverId)
            : Server::where('enabled', true)->first();

        if (!$server) {
            $this->error('No enabled PACS server found');
            return Command::FAILURE;
        }

        $studyService = new StudyService($server);
        $isDryRun = (bool) $this->option('dry-run');

        $items = WorklistItem::whereIn('status', self::PRE_SCAN_STATUSES)
            ->whereNotNull('accession_number')
            ->get();

        if ($items->isEmpty()) {
            $this->info('No worklist items pending sync');
            return Command::SUCCESS;
        }

        $this->info("Syncing {$items->count()} worklist items...");

        $updated = 0;
        foreach ($items as $item) {
            $studies = $studyService->search(
                accessionNumber: $item->accession_number,
                limit: 1,
            );

            if (empty($studies)) {
                continue;
            }

            $study = $studies[0];
            $studyUid = $study['StudyInstanceUID'] ?? null;
            $seriesCount = (int) ($study['NumberOfStudyRelatedSeries'] ?? 0);
            $instanceCount = (int) ($study['NumberOfStudyRelatedInstances'] ?? 0);

            $oldStatus = $item->status;

            if ($isDryRun) {
                $this->line("  [DRY] {$item->accession_number}: {$oldStatus} → sent_to_pacs (UID: {$studyUid}, {$seriesCount} series, {$instanceCount} instances)");
                continue;
            }

            $item->update([
                'status' => WorklistItem::STATUS_SENT_TO_PACS,
                'study_instance_uid' => $studyUid,
                'sent_at' => now(),
            ]);

            $this->line("  {$item->accession_number}: {$oldStatus} → sent_to_pacs");
            $updated++;
        }

        $this->info("Updated {$updated} items");
        return Command::SUCCESS;
    }
}
