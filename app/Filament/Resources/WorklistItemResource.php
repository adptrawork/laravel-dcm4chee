<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorklistItemResource\Pages;
use App\Models\WorklistItem;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Artisan;

class WorklistItemResource extends Resource
{
    protected static ?string $model = WorklistItem::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static string|\UnitEnum|null $navigationGroup = 'Clinical';
    protected static ?int $navigationSort = 3;

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('view_worklist') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Select::make('server_id')->relationship('server', 'name')->required(),
            TextInput::make('accession_number')->required()->maxLength(64),
            TextInput::make('patient_name')->required()->maxLength(255),
            TextInput::make('patient_id')->required()->maxLength(64),
            Select::make('modality')->options([
                'CT' => 'CT', 'MR' => 'MRI', 'DX' => 'X-Ray', 'CR' => 'CR',
                'US' => 'Ultrasound', 'XA' => 'Angiography', 'NM' => 'Nuclear Medicine', 'OT' => 'Other',
            ])->required(),
            TextInput::make('procedure_description'),
            Select::make('status')->options(WorklistItem::STATUS_LABELS),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('accession_number')->searchable(),
            TextColumn::make('patient_name')->searchable(),
            TextColumn::make('patient_id')->searchable(),
            TextColumn::make('modality'),
            TextColumn::make('status')->badge()->color(fn ($state) => match ($state) {
                'registered' => 'gray',
                'mw_published' => 'info',
                'taken_by_modality' => 'primary',
                'acquired' => 'warning',
                'sent_to_pacs' => 'success',
                'archived' => 'success',
                'reported' => 'success',
                'verified' => 'success',
                'cancelled' => 'danger',
                'failed' => 'danger',
                default => 'gray',
            }),
            TextColumn::make('scheduled_date')->date(),
            TextColumn::make('created_at')->dateTime()->sortable(),
        ])->filters([
            SelectFilter::make('status')->options(WorklistItem::STATUS_LABELS)->multiple(),
            SelectFilter::make('modality')->options([
                'CT' => 'CT', 'MR' => 'MRI', 'DX' => 'X-Ray', 'US' => 'US',
            ]),
        ])->defaultSort('created_at', 'desc')
        ->headerActions([
            Action::make('sync_status')
                ->label('Sync Status from PACS')
                ->icon('heroicon-o-arrow-path')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Sync worklist status from PACS?')
                ->modalDescription('Will query PACS for completed studies and update matching worklist items.')
                ->action(function () {
                    $exitCode = Artisan::call('worklist:sync-status');
                    $output = Artisan::output();
                    Notification::make()
                        ->title($exitCode === 0 ? 'Sync completed' : 'Sync finished with warnings')
                        ->body($output)
                        ->{$exitCode === 0 ? 'success' : 'warning'}()
                        ->send();
                }),
            Action::make('process_queue')
                ->label('Process Queue (1 job)')
                ->icon('heroicon-o-play')
                ->color('gray')
                ->action(function () {
                    $exitCode = Artisan::call('queue:work', ['--once' => true, '--max-time' => 5]);
                    $output = Artisan::output();
                    Notification::make()
                        ->title($exitCode === 0 ? 'Done' : 'Nothing to process')
                        ->body($output ?: 'Queue is empty or sync mode is active')
                        ->information()
                        ->send();
                }),
        ])->actions([
            ActionGroup::make([
                Action::make('publish_mwl')
                    ->label('Publish MWL')->icon('heroicon-o-cloud-arrow-up')
                    ->color('info')
                    ->visible(fn (WorklistItem $record): bool => $record->status === WorklistItem::STATUS_REGISTERED)
                    ->action(fn (WorklistItem $record) => $record->update(['status' => WorklistItem::STATUS_MW_PUBLISHED])),
                Action::make('mark_taken')
                    ->label('Taken by Modality')->icon('heroicon-o-camera')
                    ->color('primary')
                    ->visible(fn (WorklistItem $record): bool => $record->status === WorklistItem::STATUS_MW_PUBLISHED)
                    ->action(fn (WorklistItem $record) => $record->update(['status' => WorklistItem::STATUS_TAKEN_BY_MODALITY])),
                Action::make('mark_acquired')
                    ->label('Acquired')->icon('heroicon-o-check-badge')
                    ->color('warning')
                    ->visible(fn (WorklistItem $record): bool => in_array($record->status, [WorklistItem::STATUS_ACQUIRING, WorklistItem::STATUS_TAKEN_BY_MODALITY]))
                    ->action(fn (WorklistItem $record) => $record->update(['status' => WorklistItem::STATUS_ACQUIRED, 'acquired_at' => now()])),
                Action::make('mark_sent')
                    ->label('Sent to PACS')->icon('heroicon-o-arrow-right-circle')
                    ->color('success')
                    ->visible(fn (WorklistItem $record): bool => $record->status === WorklistItem::STATUS_ACQUIRED)
                    ->action(fn (WorklistItem $record) => $record->update(['status' => WorklistItem::STATUS_SENT_TO_PACS, 'sent_at' => now()])),
                Action::make('mark_reported')
                    ->label('Reported')->icon('heroicon-o-document-text')
                    ->color('teal')
                    ->visible(fn (WorklistItem $record): bool => $record->status === WorklistItem::STATUS_SENT_TO_PACS)
                    ->action(fn (WorklistItem $record) => $record->update(['status' => WorklistItem::STATUS_REPORTED, 'reported_at' => now()])),
                Action::make('mark_verified')
                    ->label('Verify')->icon('heroicon-o-shield-check')
                    ->color('emerald')
                    ->visible(fn (WorklistItem $record): bool => $record->status === WorklistItem::STATUS_REPORTED)
                    ->requiresConfirmation()
                    ->action(fn (WorklistItem $record) => $record->update([
                        'status' => WorklistItem::STATUS_VERIFIED, 'verified_at' => now(), 'verified_by' => auth()->id(),
                    ])),
                Action::make('cancel')
                    ->label('Cancel')->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (WorklistItem $record): bool => !in_array($record->status, [WorklistItem::STATUS_CANCELLED, WorklistItem::STATUS_VERIFIED]))
                    ->requiresConfirmation()
                    ->action(fn (WorklistItem $record) => $record->update(['status' => WorklistItem::STATUS_CANCELLED])),
            ])->dropdown(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWorklistItems::route('/'),
            'create' => Pages\CreateWorklistItem::route('/create'),
            'edit' => Pages\EditWorklistItem::route('/{record}/edit'),
        ];
    }
}
