<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PatientResource\Pages;
use App\Models\Patient;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PatientResource extends Resource
{
    protected static ?string $model = Patient::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';
    protected static string|\UnitEnum|null $navigationGroup = 'Clinical';
    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('create_order') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('patient_id')->label('MRN')->required()->unique(ignoreRecord: true)->maxLength(64),
            TextInput::make('name')->required(),
            DatePicker::make('date_of_birth')->before('today'),
            Select::make('sex')->options(['M' => 'Male', 'F' => 'Female', 'O' => 'Other']),
            TextInput::make('phone')->tel()->regex('/^[\+\-\s\(\)\d]{7,20}$/'),
            TextInput::make('email')->email(),
            Textarea::make('address'),
            TextInput::make('national_id')->label('KTP'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('patient_id')->label('MRN')->searchable(),
            TextColumn::make('name')->searchable(),
            TextColumn::make('date_of_birth')->date(),
            TextColumn::make('sex'),
            TextColumn::make('phone'),
            TextColumn::make('created_at')->dateTime()->sortable()->toggleable(),
        ])->actions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPatients::route('/'),
            'create' => Pages\CreatePatient::route('/create'),
            'edit' => Pages\EditPatient::route('/{record}/edit'),
        ];
    }
}
