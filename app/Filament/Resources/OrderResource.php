<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Device;
use App\Models\Order;
use App\Models\Patient;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-plus';
    protected static string|\UnitEnum|null $navigationGroup = 'Clinical';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Wizard::make([

                Step::make('Pasien')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Select::make('patient_id')
                            ->label('Cari Pasien')
                            ->placeholder('Ketik nama atau MRN...')
                            ->searchable()
                            ->getSearchResultsUsing(fn (string $search) => Patient::query()
                                ->where('name', 'like', "%{$search}%")
                                ->orWhere('patient_id', 'like', "%{$search}%")
                                ->limit(10)
                                ->pluck('name', 'id'))
                            ->getOptionLabelUsing(fn ($value): string => Patient::find($value)?->name ?? '')
                            ->createOptionForm([
                                Grid::make(2)->schema([
                                    TextInput::make('name')->label('Nama Pasien')->required(),
                                    DatePicker::make('date_of_birth')->label('Tanggal Lahir'),
                                    Select::make('sex')->options(['M' => 'Laki-laki', 'F' => 'Perempuan', 'O' => 'Lainnya']),
                                    TextInput::make('phone')->label('No. Telepon'),
                                ]),
                            ])
                            ->createOptionUsing(function (array $data): int {
                                $data['patient_id'] = 'MRN-' . now()->format('Ymd') . '-' . str_pad(
                                    Patient::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT
                                );
                                return Patient::create($data)->id;
                            })
                            ->required(),
                    ]),

                Step::make('Pemeriksaan')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('modality')
                                ->label('Jenis Pemeriksaan')
                                ->options([
                                    'CT' => 'CT Scan',
                                    'MR' => 'MRI',
                                    'CR' => 'X-Ray (CR)',
                                    'DX' => 'X-Ray (DX)',
                                    'US' => 'Ultrasound',
                                ])
                                ->required()
                                ->live()
                                ->afterStateUpdated(fn ($set) => $set('device_id', null)),

                            Select::make('device_id')
                                ->label('Alat / Ruangan')
                                ->options(fn (Get $get): array => $get('modality')
                                    ? Device::where('modality', $get('modality'))
                                        ->where('status', 'active')
                                        ->pluck('name', 'id')
                                        ->toArray()
                                    : [])
                                ->disabled(fn (Get $get): bool => !$get('modality'))
                                ->helperText(fn (Get $get): ?string => !$get('modality')
                                    ? 'Pilih jenis pemeriksaan dulu' : null)
                                ->required(),

                            TextInput::make('requesting_physician')->label('Dokter Perujuk')->required()->maxLength(255),

                            DatePicker::make('scheduled_date')
                                ->label('Tanggal Pemeriksaan')
                                ->default(now())
                                ->afterOrEqual('today'),

                            Select::make('priority')
                                ->options(['routine' => 'Routine', 'urgent' => 'Urgent', 'stat' => 'STAT'])
                                ->default('routine'),
                        ]),

                        Textarea::make('clinical_notes')
                            ->label('Catatan Klinis')
                            ->placeholder('Keluhan, indikasi pemeriksaan, dsb.')
                            ->rows(3)
                            ->maxLength(5000),
                    ]),

                Step::make('Konfirmasi')
                    ->icon('heroicon-o-check-circle')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('accession_number')
                                ->label('Accession No.')
                                ->disabled()
                                ->dehydrated(false)
                                ->default('Akan digenerate otomatis'),

                            TextInput::make('_device_info')
                                ->label('AE Title')
                                ->disabled()
                                ->dehydrated(false)
                                ->default(fn (Get $get): string => Device::find($get('device_id'))?->ae_title ?? '-'),
                        ]),
                    ]),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('accession_number')->searchable(),
            TextColumn::make('patient.name')->label('Patient')->searchable(),
            TextColumn::make('modality'),
            TextColumn::make('device.name')->label('Device')->toggleable(),
            TextColumn::make('requesting_physician')->label('Referring'),
            TextColumn::make('status')->badge()
                ->color(fn ($s) => \App\Models\Order::STATUS_COLORS[$s] ?? 'gray'),
            TextColumn::make('priority')->badge()
                ->color(fn ($s) => match ($s) {
                    'routine' => 'gray',
                    'urgent' => 'warning',
                    'stat' => 'danger',
                    default => 'gray',
                }),
            TextColumn::make('scheduled_date')->date()->sortable(),
            TextColumn::make('created_at')->dateTime()->sortable()->toggleable(),
        ])->filters([
            SelectFilter::make('status')->options(Order::STATUS_LABELS),
            SelectFilter::make('priority')->options([
                'routine' => 'Routine', 'urgent' => 'Urgent', 'stat' => 'STAT',
            ]),
        ])->defaultSort('created_at', 'desc')->actions([
            ViewAction::make(),
            EditAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
