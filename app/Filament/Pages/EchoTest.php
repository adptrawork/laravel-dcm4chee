<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\Server;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class EchoTest extends Page
{
    protected string $view = 'filament.pages.echo-test';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-signal';

    protected static string|\UnitEnum|null $navigationGroup = 'Imaging';

    protected static ?int $navigationSort = 3;

    public array $results = [];

    public int $queuePending = 0;

    public int $queueFailed = 0;

    public static function canView(): bool
    {
        return auth()->user()?->can('view_studies') ?? false;
    }

    public function mount(): void
    {
        $this->queuePending = DB::table('jobs')->count();
        $this->queueFailed = DB::table('failed_jobs')->count();
    }

    public function test_echo(int $serverId): void
    {
        $server = Server::find($serverId);
        if (! $server) {
            Notification::make()->danger()->title('Server not found')->send();

            return;
        }

        $this->results[$serverId] = $server->testConnection();
    }

    public function test_all(): void
    {
        foreach (Server::all() as $server) {
            $this->results[$server->id] = ['running' => true, 'ok' => false, 'steps' => []];
            $this->results[$server->id] = $server->testConnection();
        }
    }
}
