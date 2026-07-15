<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeviceResource\Pages;
use App\Models\Device;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DeviceResource extends Resource
{
    protected static ?string $model = Device::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cpu-chip';

    protected static string|\UnitEnum|null $navigationGroup = 'Administration';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Select::make('server_id')->relationship('server', 'name')->required(),
            TextInput::make('name')->required(),
            TextInput::make('ae_title')->required()->label('AE Title')->maxLength(16)->regex('/^[A-Z0-9_]+$/'),
            TextInput::make('hostname')->maxLength(255),
            TextInput::make('port')->numeric()->minValue(1)->maxValue(65535),
            Select::make('modality')->options([
                'CT' => 'CT', 'MR' => 'MRI', 'DX' => 'X-Ray', 'CR' => 'CR',
                'US' => 'Ultrasound', 'XA' => 'Angiography', 'NM' => 'Nuclear Medicine', 'OT' => 'Other',
            ]),
            Select::make('status')->options([
                'unknown' => 'Unknown', 'active' => 'Active', 'inactive' => 'Inactive',
            ])->default('unknown'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable(),
            TextColumn::make('ae_title')->label('AE Title'),
            TextColumn::make('modality'),
            TextColumn::make('status'),
            TextColumn::make('last_echo_at')->dateTime()->label('Last Echo'),
        ])->actions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDevices::route('/'),
            'create' => Pages\CreateDevice::route('/create'),
            'edit' => Pages\EditDevice::route('/{record}/edit'),
        ];
    }
}
