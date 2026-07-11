<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorklistItemResource\Pages;
use App\Models\WorklistItem;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WorklistItemResource extends Resource
{
    protected static ?string $model = WorklistItem::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static string|\UnitEnum|null $navigationGroup = 'Operations';

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('view_worklist') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Select::make('server_id')->relationship('server', 'name')->required(),
            TextInput::make('accession_number')->required(),
            TextInput::make('patient_name')->required(),
            TextInput::make('patient_id')->required(),
            TextInput::make('modality'),
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
        ])->actions([
            ActionGroup::make([
                Action::make('publish_mwl')
                    ->label('Publish MWL')->icon('heroicon-o-cloud-arrow-up')
                    ->color('info')->visible(fn ($r) => $r->status === 'registered')
                    ->action(fn ($r) => $r->update(['status' => 'mw_published'])),
                Action::make('mark_taken')
                    ->label('Taken by Modality')->icon('heroicon-o-camera')
                    ->color('primary')->visible(fn ($r) => $r->status === 'mw_published')
                    ->action(fn ($r) => $r->update(['status' => 'taken_by_modality'])),
                Action::make('mark_acquired')
                    ->label('Acquired')->icon('heroicon-o-check-badge')
                    ->color('warning')->visible(fn ($r) => in_array($r->status, ['acquiring', 'taken_by_modality']))
                    ->action(fn ($r) => $r->update(['status' => 'acquired', 'acquired_at' => now()])),
                Action::make('mark_sent')
                    ->label('Sent to PACS')->icon('heroicon-o-arrow-right-circle')
                    ->color('success')->visible(fn ($r) => $r->status === 'acquired')
                    ->action(fn ($r) => $r->update(['status' => 'sent_to_pacs', 'sent_at' => now()])),
                Action::make('mark_reported')
                    ->label('Reported')->icon('heroicon-o-document-text')
                    ->color('teal')->visible(fn ($r) => $r->status === 'sent_to_pacs')
                    ->action(fn ($r) => $r->update(['status' => 'reported', 'reported_at' => now()])),
                Action::make('mark_verified')
                    ->label('Verify')->icon('heroicon-o-shield-check')
                    ->color('emerald')->visible(fn ($r) => $r->status === 'reported')
                    ->requiresConfirmation()
                    ->action(fn ($r) => $r->update([
                        'status' => 'verified', 'verified_at' => now(), 'verified_by' => auth()->id(),
                    ])),
                Action::make('cancel')
                    ->label('Cancel')->icon('heroicon-o-x-circle')
                    ->color('danger')->visible(fn ($r) => !in_array($r->status, ['cancelled', 'verified']))
                    ->requiresConfirmation()
                    ->action(fn ($r) => $r->update(['status' => 'cancelled'])),
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
