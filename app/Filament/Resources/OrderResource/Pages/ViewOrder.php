<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Filament\Resources\ReportResource;
use App\Jobs\PushWorklistToPacsJob;
use App\Models\Order;
use App\Models\Report;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('viewImages')
                ->label('Lihat Gambar')
                ->icon('heroicon-o-eye')
                ->color('primary')
                ->visible(fn (Order $record): bool => $record->worklistItem?->study_instance_uid !== null)
                ->url(fn (Order $record) => env('OHIF_URL', 'http://localhost:3000').'/viewer?StudyInstanceUIDs='.$record->worklistItem->study_instance_uid)
                ->openUrlInNewTab(),
            Action::make('pushToPacs')
                ->label('Push to PACS')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('warning')
                ->visible(fn (Order $record): bool => $record->status === Order::STATUS_PENDING
                    && (! $record->worklistItem || $record->worklistItem->status === 'failed')
                )
                ->action(function (Order $record) {
                    try {
                        $job = new PushWorklistToPacsJob($record);
                        $job->handle();
                        Notification::make()->success()->title('Pushed to PACS')->send();
                        $this->redirect(OrderResource::getUrl('view', ['record' => $record]));
                    } catch (\Throwable $e) {
                        Notification::make()->danger()->title('Push failed: '.$e->getMessage())->send();
                    }
                }),
            Action::make('createReport')
                ->label('Buat Laporan')
                ->icon('heroicon-o-document-plus')
                ->color('success')
                ->visible(fn (Order $record): bool => in_array($record->status, [Order::STATUS_COMPLETED, Order::STATUS_REPORTED])
                    && ! $record->report
                )
                ->action(function (Order $record) {
                    $report = Report::create([
                        'order_id' => $record->id,
                        'worklist_item_id' => $record->worklistItem?->id,
                        'accession_number' => $record->accession_number,
                        'study_instance_uid' => $record->worklistItem?->study_instance_uid,
                        'radiologist_id' => auth()->id(),
                        'status' => 'draft',
                    ]);
                    Notification::make()->success()->title('Laporan dibuat')->send();
                    $this->redirect(ReportResource::getUrl('edit', ['record' => $report]));
                }),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Order Progress')->schema([
                TextEntry::make('status')
                    ->badge()
                    ->color(fn ($s) => Order::STATUS_COLORS[$s] ?? 'gray')
                    ->formatStateUsing(fn ($s) => Order::STATUS_LABELS[$s] ?? $s),
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
                    ->color(fn ($s) => match ($s) {
                        'stat' => 'danger', 'urgent' => 'warning', default => 'gray'
                    }),
            ]),

            Section::make('Clinical Notes')->schema([
                TextEntry::make('clinical_notes')
                    ->html()
                    ->hidden(fn ($record) => ! $record->clinical_notes)
                    ->placeholder('No clinical notes'),
            ]),

            Section::make('Timestamps')->columns(3)->schema([
                TextEntry::make('created_at')->dateTime(),
                TextEntry::make('updated_at')->dateTime(),
            ]),
        ]);
    }
}
