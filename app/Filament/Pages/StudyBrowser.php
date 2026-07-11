<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\Server;
use App\Services\Dcm4chee\StudyService;
use Filament\Pages\Page;

final class StudyBrowser extends Page
{
    protected string $view = 'filament.pages.study-browser';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-magnifying-glass';
    protected static string|\UnitEnum|null $navigationGroup = 'Imaging';
    protected static ?int $navigationSort = 0;

    public ?Server $server = null;
    public string $searchName = '';
    public string $searchId = '';
    public string $searchDate = '';
    public string $searchAccession = '';
    public array $studies = [];
    public bool $searched = false;
    public ?string $error = null;
    public int $page = 1;

    public static function canView(): bool
    {
        return auth()->user()?->can('view_studies') ?? false;
    }

    public function mount(): void
    {
        $this->server = Server::where('enabled', true)->first();
    }

    public function search(): void
    {
        $this->error = null;
        $this->studies = [];
        $this->page = 1;
        $this->loadStudies();
    }

    public function loadStudies(): void
    {
        if (!$this->server) {
            $this->error = 'No PACS server configured';
            return;
        }

        $svc = new StudyService($this->server);
        $date = $this->searchDate ? str_replace('-', '', $this->searchDate) : null;
        $limit = 10;
        $offset = ($this->page - 1) * $limit;

        try {
            $results = $svc->search(
                patientName: $this->searchName ?: null,
                patientId: $this->searchId ?: null,
                studyDate: $date,
                accessionNumber: $this->searchAccession ?: null,
                limit: $limit,
                offset: $offset,
            );

            if (empty($results) && $this->page > 1) {
                $this->page--;
                return;
            }

            $this->studies = $results;
        } catch (\Throwable $e) {
            $this->error = 'PACS query failed: ' . $e->getMessage();
            $this->studies = [];
        }

        $this->searched = true;
    }

    public function nextPage(): void
    {
        $this->page++;
        $this->loadStudies();
    }

    public function prevPage(): void
    {
        if ($this->page > 1) {
            $this->page--;
            $this->loadStudies();
        }
    }

    public function resetSearch(): void
    {
        $this->searchName = '';
        $this->searchId = '';
        $this->searchDate = '';
        $this->searchAccession = '';
        $this->studies = [];
        $this->searched = false;
        $this->error = null;
        $this->page = 1;
    }
}
