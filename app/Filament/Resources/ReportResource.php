<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReportResource\Pages;
use App\Models\Order;
use App\Models\Report;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ReportResource extends Resource
{
    protected static ?string $model = Report::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';
    protected static string|\UnitEnum|null $navigationGroup = 'Clinical';
    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Select::make('order_id')->relationship('order', 'accession_number')
                ->searchable()->nullable()
                ->live()
                ->afterStateUpdated(function (Set $set, ?string $state) {
                    if (!$state) return;
                    $order = Order::with('worklistItem')->find($state);
                    if (!$order) return;
                    $set('accession_number', $order->accession_number);
                    $set('study_instance_uid', $order->worklistItem?->study_instance_uid);
                }),
            Select::make('worklist_item_id')->relationship('worklistItem', 'accession_number')
                ->searchable()->nullable(),
            TextInput::make('accession_number')->readOnly()->maxLength(64),
            TextInput::make('study_instance_uid')->readOnly()->maxLength(64),
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
            TextColumn::make('order.accession_number')->label('Order')->searchable(),
            TextColumn::make('study_instance_uid')->label('Study UID')->limit(20),
            TextColumn::make('radiologist.name')->label('Radiologist'),
            TextColumn::make('status')->badge()
                ->color(fn ($s) => match($s) { 'draft' => 'gray', 'final' => 'success', 'amended' => 'warning', default => 'gray' }),
            TextColumn::make('created_at')->dateTime(),
            TextColumn::make('finalized_at')->dateTime()->label('Finalized'),
        ])->defaultSort('created_at', 'desc')->actions([
            EditAction::make(),
            Action::make('finalize')
                ->label('Finalize')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn ($r) => $r->status === 'draft')
                ->requiresConfirmation()
                ->action(function (Report $r) {
                    $r->update(['status' => 'final', 'finalized_at' => now()]);
                    $r->order?->updateQuietly(['status' => Order::STATUS_REPORTED]);
                    Notification::make()->success()->title('Report finalized, order completed')->send();
                }),
            Action::make('amend')
                ->label('Amend')
                ->icon('heroicon-o-pencil-square')
                ->color('warning')
                ->visible(fn ($r) => $r->status === 'final')
                ->requiresConfirmation()
                ->action(fn (Report $r) => $r->update([
                    'status' => 'amended', 'finalized_at' => null,
                ])),
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
