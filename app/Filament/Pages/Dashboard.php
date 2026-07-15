<?php

namespace App\Filament\Pages;

use App\Models\Order;
use App\Models\Server;
use App\Models\WorklistItem;
use App\Services\Dcm4chee\Client;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class Dashboard extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.pages.dashboard';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-home';

    protected static string|\UnitEnum|null $navigationGroup = 'Clinical';

    protected static ?int $navigationSort = 0;

    public static function canView(): bool
    {
        return auth()->user()?->can('view_dashboard') ?? false;
    }

    public ?string $pacsStatus = 'Checking...';

    public int $ordersWaiting = 0;

    public int $ordersScheduled = 0;

    public int $ordersInProgress = 0;

    public int $readyToRead = 0;

    public int $reportedToday = 0;

    public int $queuePending = 0;

    public int $queueFailed = 0;

    public function mount(): void
    {
        $server = Server::where('enabled', true)->first();

        $this->ordersWaiting = Order::where('status', Order::STATUS_PENDING)->count();
        $this->ordersScheduled = Order::where('status', Order::STATUS_SCHEDULED)->count();
        $this->ordersInProgress = Order::where('status', Order::STATUS_IN_PROGRESS)->count();
        $this->readyToRead = Order::where('status', Order::STATUS_COMPLETED)->count();
        $this->reportedToday = Order::where('status', Order::STATUS_REPORTED)
            ->whereDate('updated_at', today())->count();
        $this->queuePending = DB::table('jobs')->count();
        $this->queueFailed = DB::table('failed_jobs')->count();

        if (! $server) {
            $this->pacsStatus = 'No server configured';

            return;
        }

        try {
            $client = new Client($server);
            $studies = $client->get('studies', ['limit' => 1]);
            $this->pacsStatus = 'Connected';
        } catch (\Exception $e) {
            $this->pacsStatus = class_basename($e).': '.$e->getMessage();
        }
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(WorklistItem::whereIn('status', [
                WorklistItem::STATUS_REGISTERED,
                WorklistItem::STATUS_MW_PUBLISHED,
                WorklistItem::STATUS_TAKEN_BY_MODALITY,
                WorklistItem::STATUS_ACQUIRING,
                WorklistItem::STATUS_ACQUIRED,
            ])->orderBy('created_at', 'desc')->limit(10))
            ->columns([
                TextColumn::make('accession_number')->searchable(),
                TextColumn::make('patient_name')->searchable(),
                TextColumn::make('modality'),
                TextColumn::make('status')->badge()
                    ->color(fn ($s) => match ($s) {
                        'registered' => 'gray',
                        'mw_published' => 'info',
                        'taken_by_modality' => 'primary',
                        'acquiring', 'acquired' => 'warning',
                        'sent_to_pacs' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')->dateTime()->since()->sortable(),
            ])
            ->actions([
                Action::make('view')
                    ->label('Open')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn ($record) => $record ? "/admin/worklist-items/{$record->id}/edit" : '#'),
            ]);
    }
}
