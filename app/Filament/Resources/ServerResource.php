<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServerResource\Pages;
use App\Models\Server;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use App\Services\Dcm4chee\Client;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ServerResource extends Resource
{
    protected static ?string $model = Server::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-server-stack';
    protected static string|\UnitEnum|null $navigationGroup = 'Administration';

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('manage_servers') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Server Info')->columns(2)->schema([
                TextInput::make('name')->required(),
                TextInput::make('base_url')->required()->label('Base URL')->url(),
                TextInput::make('archive')->default('dcm4chee-arc'),
                TextInput::make('aet')->default('DCM4CHEE')->label('AE Title')->maxLength(16)->regex('/^[A-Z0-9_]+$/'),
            ]),
            Section::make('Authentication')->columns(2)->schema([
                TextInput::make('username')->required()->minLength(2),
                TextInput::make('password')->password()->required()->minLength(2),
                TextInput::make('timeout')->numeric()->default(30)->minValue(1)->maxValue(300),
                Toggle::make('ssl_verify')->label('Verify SSL'),
            ]),
            Section::make('Status')->columns(2)->schema([
                Toggle::make('enabled')->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('base_url')->limit(40),
            TextColumn::make('aet')->label('AE Title'),
            IconColumn::make('enabled')->boolean(),
            TextColumn::make('created_at')->dateTime()->sortable(),
        ])->actions([
            Action::make('test_connection')
                ->label('Test Connection')
                ->icon('heroicon-o-signal')
                ->color('success')
                ->action(function (Server $server) {
                    try {
                        $client = new Client($server);
                        $client->get('patients', ['limit' => 1]);
                        Notification::make()->success()->title('Connection OK')->send();
                    } catch (\Throwable $e) {
                        Notification::make()->danger()
                            ->title('Connection Failed')
                            ->body($e->getMessage())
                            ->send();
                    }
                }),
            EditAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServers::route('/'),
            'create' => Pages\CreateServer::route('/create'),
            'edit' => Pages\EditServer::route('/{record}/edit'),
        ];
    }
}
