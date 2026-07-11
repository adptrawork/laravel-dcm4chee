<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Order Progress')->schema([
                TextEntry::make('status')
                    ->badge()
                    ->color(fn ($s) => \App\Models\Order::STATUS_COLORS[$s] ?? 'gray')
                    ->formatStateUsing(fn ($s) => \App\Models\Order::STATUS_LABELS[$s] ?? $s),
                TextEntry::make('timeline')
                    ->label('')
                    ->html()
                    ->state(fn ($record) => view('components.order-timeline', ['order' => $record])->render()),
            ])->columns(1),

            Section::make('Patient & Order')->columns(2)->schema([
                TextEntry::make('patient.name')->label('Patient'),
                TextEntry::make('patient.patient_id')->label('MRN'),
                TextEntry::make('accession_number')->label('Accession No.'),
                TextEntry::make('modality'),
                TextEntry::make('device.name')->label('Device'),
                TextEntry::make('device.ae_title')->label('AE Title'),
                TextEntry::make('requesting_physician')->label('Referring'),
                TextEntry::make('scheduled_date')->date(),
                TextEntry::make('priority')->badge()
                    ->color(fn ($s) => match($s) { 'stat' => 'danger', 'urgent' => 'warning', default => 'gray' }),
            ]),

            Section::make('Clinical Notes')->schema([
                TextEntry::make('clinical_notes')
                    ->html()
                    ->hidden(fn ($record) => !$record->clinical_notes)
                    ->placeholder('No clinical notes'),
            ]),

            Section::make('Timestamps')->columns(3)->schema([
                TextEntry::make('created_at')->dateTime(),
                TextEntry::make('updated_at')->dateTime(),
            ]),
        ]);
    }
}
