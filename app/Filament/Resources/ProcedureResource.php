<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProcedureResource\Pages;
use App\Models\Procedure;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProcedureResource extends Resource
{
    protected static ?string $model = Procedure::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-list-bullet';
    protected static string|\UnitEnum|null $navigationGroup = 'Configuration';

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('manage_procedures') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make()->columns(2)->schema([
                TextInput::make('code')->required(),
                TextInput::make('name')->required(),
                Textarea::make('description'),
                Select::make('modality')->options([
                    'CT' => 'CT', 'MR' => 'MRI', 'DX' => 'X-Ray',
                    'US' => 'Ultrasound', 'CR' => 'CR', 'XA' => 'Angiography',
                    'NM' => 'Nuclear Medicine', 'OT' => 'Other',
                ]),
                TextInput::make('body_part'),
                TextInput::make('estimated_duration')->numeric()->suffix('min'),
                TextInput::make('default_physician'),
                TextInput::make('default_room'),
            ]),
            Section::make('Contrast')->columns(2)->schema([
                Toggle::make('requires_contrast'),
                Textarea::make('contrast_detail'),
            ]),
            Section::make()->columns(2)->schema([
                TextInput::make('sort_order')->numeric(),
                Toggle::make('is_active')->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('code')->searchable(),
            TextColumn::make('name')->searchable(),
            TextColumn::make('modality'),
            TextColumn::make('body_part'),
            IconColumn::make('is_active')->boolean(),
        ])->actions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProcedures::route('/'),
            'create' => Pages\CreateProcedure::route('/create'),
            'edit' => Pages\EditProcedure::route('/{record}/edit'),
        ];
    }
}
