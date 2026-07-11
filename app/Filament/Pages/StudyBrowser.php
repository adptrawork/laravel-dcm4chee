<?php

namespace App\Filament\Pages;

use App\Models\Server;
use App\Services\Dcm4chee\DicomHelper;
use App\Services\Dcm4chee\StudyService;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Pages\Page;

class StudyBrowser extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.pages.study-browser';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-magnifying-glass';
    protected static string|\UnitEnum|null $navigationGroup = 'Operations';
    protected static ?int $navigationSort = 2;

    public ?Server $server = null;
    public string $searchName = '';
    public string $searchId = '';
    public string $searchDate = '';
    public string $searchAccession = '';

    public function mount(): void
    {
        $this->server = Server::where('enabled', true)->first();
    }

    public function table(Table $table): Table
    {
        $svc = $this->server ? new StudyService($this->server) : null;
        return $table->query(
            $svc ? $svc->search(...$this->getQueryParams()) : collect()
        )->columns([
            TextColumn::make('PatientName')->label('Patient')->searchable(),
            TextColumn::make('PatientID')->label('ID')->searchable(),
            TextColumn::make('StudyDate')->label('Date'),
            TextColumn::make('ModalitiesInStudy')->label('Modality'),
            TextColumn::make('AccessionNumber')->label('Accession'),
            TextColumn::make('StudyDescription')->label('Description'),
        ])->actions([
            Action::make('view_ohif')
                ->label('View')
                ->icon('heroicon-o-eye')
                ->color('primary')
                ->url(fn (array $record): string => 'http://localhost:3000/viewer?StudyInstanceUIDs=' . ($record['StudyInstanceUID'] ?? ''))
                ->openUrlInNewTab(),
        ]);
    }

    protected function getQueryParams(): array
    {
        return [
            $this->searchName ?: null,
            $this->searchId ?: null,
            $this->searchDate ?: null,
            $this->searchAccession ?: null,
        ];
    }
}
