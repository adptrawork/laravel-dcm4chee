<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\Server;
use App\Services\Dcm4chee\Client;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;

class DicomUpload extends Page
{
    protected string $view = 'filament.pages.dicom-upload';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-up-on-square';

    protected static string|\UnitEnum|null $navigationGroup = 'Imaging';

    protected static ?int $navigationSort = 2;

    public ?array $data = [];

    public ?string $result = null;

    public ?string $studyUid = null;

    public static function canView(): bool
    {
        return auth()->user()?->can('view_studies') ?? false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('dicom')
                    ->label('Select DICOM file(s)')
                    ->acceptedFileTypes(['application/dicom', 'application/octet-stream'])
                    ->multiple()
                    ->preserveFilenames()
                    ->required()
                    ->maxSize(102400),
            ])
            ->statePath('data');
    }

    public function upload(): void
    {
        $this->result = null;
        $this->studyUid = null;

        $server = Server::where('enabled', true)->first();
        if (! $server) {
            Notification::make()->danger()->title('No PACS server configured')->send();

            return;
        }

        $files = $this->form->getState()['dicom'] ?? [];
        if (empty($files)) {
            Notification::make()->danger()->title('Select at least one DICOM file')->send();

            return;
        }

        $uploaded = $failed = [];

        foreach ($files as $file) {
            $path = storage_path('app/public/'.$file);
            if (! file_exists($path)) {
                $failed[] = $file;

                continue;
            }

            try {
                $content = file_get_contents($path);
                $boundary = '---'.uniqid();
                $body = "--{$boundary}\r\nContent-Type: application/dicom\r\n\r\n".$content."\r\n--{$boundary}--\r\n";

                $client = new Client($server);
                $resp = $client->raw('POST', 'studies', [
                    'body' => $body,
                    'headers' => [
                        'Content-Type' => 'multipart/related; type=application/dicom; boundary='.$boundary,
                        'Accept' => 'application/dicom+json',
                    ],
                ]);

                $uploaded[] = ['file' => $file, 'status' => $resp->status()];

                if ($resp->successful()) {
                    $json = $resp->json();
                    $this->studyUid = $json['0020000D']['Value'][0] ?? null;
                }
            } catch (\Throwable $e) {
                $failed[] = $file.': '.$e->getMessage();
            }

            @unlink($path);
        }

        $this->result = json_encode([
            'uploaded' => $uploaded,
            'failed' => $failed,
        ], JSON_PRETTY_PRINT);

        if (! empty($uploaded)) {
            Notification::make()->success()
                ->title(count($uploaded).' file(s) uploaded')
                ->send();
        }
        if (! empty($failed)) {
            Notification::make()->danger()
                ->title(count($failed).' file(s) failed')
                ->send();
        }
    }
}
