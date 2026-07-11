<?php

namespace App\Filament\Pages;

use App\Models\Server;
use App\Models\WorklistItem;
use App\Services\Dcm4chee\Client;
use Filament\Pages\Page;

class Dashboard extends Page
{
    protected string $view = 'filament.pages.dashboard';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-home';
    protected static ?int $navigationSort = 0;

    public static function canView(): bool
    {
        return auth()->user()?->can('view_dashboard') ?? false;
    }

    public int $totalStudies = 0;
    public int $totalPatients = 0;
    public int $pendingWorklist = 0;
    public ?string $pacsStatus = null;

    public function mount(): void
    {
        $server = Server::where('enabled', true)->first();
        if (!$server) {
            $this->pacsStatus = 'No server configured';
            return;
        }

        try {
            $client = new Client($server);
            $studies = $client->get('studies', ['limit' => 1]);
            $this->pacsStatus = 'Connected';

            $this->pendingWorklist = WorklistItem::byStatus(WorklistItem::STATUS_REGISTERED)->count();
        } catch (\Exception $e) {
            $this->pacsStatus = 'Error: ' . $e->getMessage();
        }
    }
}
