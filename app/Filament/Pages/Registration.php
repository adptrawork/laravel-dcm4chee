<?php

namespace App\Filament\Pages;

use App\Models\Server;
use App\Services\Dcm4chee\Client;
use App\Services\Dcm4chee\DicomHelper;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Schemas\Schema;

class Registration extends Page
{
    protected string $view = 'filament.pages.registration';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-plus';
    protected static string|\UnitEnum|null $navigationGroup = 'Operations';
    protected static ?int $navigationSort = 1;

    public ?array $data = [];
    public ?string $result = null;

    public static function canView(): bool
    {
        return auth()->user()?->can('create_order') ?? false;
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('patient_name')->required()->label('Patient Name'),
            TextInput::make('patient_id')->required()->label('Patient ID'),
            TextInput::make('patient_birthdate')->label('Birth Date (YYYYMMDD)'),
            Select::make('patient_sex')->options(['M' => 'Male', 'F' => 'Female', 'O' => 'Other']),
            TextInput::make('modality')->required()->default('CT'),
            Select::make('server_id')->label('PACS Server')
                ->options(Server::where('enabled', true)->pluck('name', 'id'))
                ->required(),
        ])->statePath('data');
    }

    public function register(): void
    {
        $server = Server::find($this->data['server_id']);
        if (!$server) {
            $this->result = 'Server not found';
            return;
        }

        try {
            $client = new Client($server);
            $patient = DicomHelper::buildPatientJson(
                $this->data['patient_name'],
                $this->data['patient_id'],
                $this->data['patient_birthdate'] ?? null,
                $this->data['patient_sex'] ?? null,
            );

            $client->post('patients', $patient);
            $this->result = 'Patient registered successfully';
        } catch (\Exception $e) {
            $this->result = 'Error: ' . $e->getMessage();
        }
    }
}
