<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\Server;
use App\Services\Dcm4chee\StudyService;
use App\Services\Dcm4chee\StudyService;
use Filament\Pages\Page;

class StudyDetail extends Page
{
    protected string $view = 'filament.pages.study-detail';
    protected static ?string $slug = 'studies/{studyUid}';
    protected static bool $shouldRegisterNavigation = false;

    public ?Server $server = null;
    public ?\App\Dto\StudyData $study = null;
    public array $series = [];
    public ?string $error = null;
    public ?string $activeTab = 'overview';
    public string $studyUid = '';
    public string $rawJson = '';

    public static function canView(): bool
    {
        return auth()->user()?->can('view_studies') ?? false;
    }

    public function mount(string $studyUid): void
    {
        $this->studyUid = $studyUid;
        $this->server = Server::where('enabled', true)->first();

        if (!$this->server) {
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

            $seriesSvc = new StudyService($this->server);
            $this->series = $seriesSvc->getSeriesByStudyUid($studyUid);
        } catch (\Throwable $e) {
            $this->error = 'Failed to load study: ' . $e->getMessage();
        }
    }

    public function getViewUrl(): ?string
    {
        if (!$this->study?->studyUid) return null;
        return config('services.ohif.url', 'http://localhost:3000')
            . '/viewer?StudyInstanceUIDs=' . $this->study->studyUid;
    }
}
