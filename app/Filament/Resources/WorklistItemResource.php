<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorklistItemResource\Pages;
use App\Models\WorklistItem;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WorklistItemResource extends Resource
{
    protected static ?string $model = WorklistItem::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static string|\UnitEnum|null $navigationGroup = 'Operations';

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
            EditAction::make(),
            Action::make('sync_from_pacs')
                ->label('Sync')
                ->icon('heroicon-o-arrow-path')
                ->color('warning'),
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
