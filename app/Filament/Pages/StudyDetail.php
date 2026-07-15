<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\Server;
use App\Services\Dcm4chee\StudyService;
use Filament\Pages\Page;

class StudyDetail extends Page
{
    protected string $view = 'filament.pages.study-detail';

    protected static ?string $slug = 'studies/{studyUid}';

    protected static bool $shouldRegisterNavigation = false;

    public ?Server $server = null;

    public array $study = [];

    public array $series = [];

    public array $instances = [];

    public array $instanceMetadata = [];

    public ?string $error = null;

    public ?string $activeTab = 'overview';

    public string $studyUid = '';

    public string $rawJson = '';

    public ?string $selectedSeriesUid = null;

    public ?string $selectedInstanceUid = null;

    public ?string $metaSubTab = 'formatted';

    public string $instanceRawJson = '';

    public static function canView(): bool
    {
        return auth()->user()?->can('view_studies') ?? false;
    }

    public function mount(string $studyUid): void
    {
        $this->studyUid = $studyUid;
        $this->server = Server::where('enabled', true)->first();

        if (! $this->server) {
            $this->error = 'No PACS server configured';

            return;
        }

        try {
            $svc = new StudyService($this->server);
            $results = $svc->search(studyUid: $studyUid);

            if (empty($results)) {
                $this->error = 'Study not found';

                return;
            }

            $this->study = $results[0];
            $this->rawJson = json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            $this->series = $svc->getSeriesByStudyUid($studyUid);
        } catch (\Throwable $e) {
            $this->error = 'Failed to load study: '.$e->getMessage();
        }
    }

    public function selectSeries(string $seriesUid): void
    {
        if ($seriesUid === $this->selectedSeriesUid) {
            $this->selectedSeriesUid = null;
            $this->instances = [];
            $this->selectedInstanceUid = null;
            $this->instanceMetadata = [];

            return;
        }

        $this->selectedSeriesUid = $seriesUid;
        $this->selectedInstanceUid = null;
        $this->instanceMetadata = [];

        try {
            $svc = new StudyService($this->server);
            $this->instances = $svc->getInstances($this->studyUid, $seriesUid);
        } catch (\Throwable $e) {
            $this->error = 'Failed to load instances: '.$e->getMessage();
        }
    }

    public function selectInstance(string $instanceUid): void
    {
        if ($instanceUid === $this->selectedInstanceUid) {
            $this->selectedInstanceUid = null;
            $this->instanceMetadata = [];

            return;
        }

        $this->selectedInstanceUid = $instanceUid;

        try {
            $svc = new StudyService($this->server);
            $this->instanceMetadata = $svc->getInstanceMetadata($instanceUid);
            $this->instanceRawJson = json_encode($this->instanceMetadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        } catch (\Throwable $e) {
            $this->error = 'Failed to load instance metadata: '.$e->getMessage();
        }
    }
}
