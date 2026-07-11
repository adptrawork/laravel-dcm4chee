<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeviceResource\Pages;
use App\Models\Device;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DeviceResource extends Resource
{
    protected static ?string $model = Device::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cpu-chip';
    protected static string|\UnitEnum|null $navigationGroup = 'Configuration';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Select::make('server_id')->relationship('server', 'name')->required(),
            TextInput::make('name')->required(),
            TextInput::make('ae_title')->required()->label('AE Title'),
            TextInput::make('hostname'),
            TextInput::make('port')->numeric(),
            TextInput::make('modality'),
            Select::make('status')->options(['active' => 'Active', 'inactive' => 'Inactive']),
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
