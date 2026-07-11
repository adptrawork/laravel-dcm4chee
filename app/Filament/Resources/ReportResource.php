<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReportResource\Pages;
use App\Models\Report;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ReportResource extends Resource
{
    protected static ?string $model = Report::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';
    protected static string|\UnitEnum|null $navigationGroup = 'Operations';

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('write_report') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Select::make('worklist_item_id')->relationship('worklistItem', 'accession_number')
                ->searchable()->nullable(),
            TextInput::make('accession_number'),
            Select::make('radiologist_id')->relationship('radiologist', 'name')
                ->default(auth()->id())
                ->required(),
            RichEditor::make('clinical_history')->columnSpanFull(),
            RichEditor::make('findings')->columnSpanFull(),
            RichEditor::make('impression')->columnSpanFull(),
            RichEditor::make('conclusion')->columnSpanFull(),
            Select::make('status')->options([
                'draft' => 'Draft', 'final' => 'Final', 'amended' => 'Amended',
            ])->default('draft')->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('accession_number')->searchable(),
            TextColumn::make('radiologist.name')->label('Radiologist'),
            TextColumn::make('status')->badge()
                ->color(fn ($s) => match($s) { 'draft' => 'gray', 'final' => 'success', 'amended' => 'warning', default => 'gray' }),
            TextColumn::make('created_at')->dateTime(),
            TextColumn::make('finalized_at')->dateTime()->label('Finalized'),
        ])->actions([
            EditAction::make(),
            Action::make('finalize')
                ->label('Finalize')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn ($r) => $r->status === 'draft')
                ->requiresConfirmation()
                ->action(fn ($r) => $r->update(['status' => 'final', 'finalized_at' => now()])),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReports::route('/'),
            'create' => Pages\CreateReport::route('/create'),
            'edit' => Pages\EditReport::route('/{record}/edit'),
        ];
    }
}
