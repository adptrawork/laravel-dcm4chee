<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityLogResource\Pages;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Spatie\Activitylog\Models\Activity;

class ActivityLogResource extends Resource
{
    protected static ?string $model = Activity::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-exclamation';
    protected static string|\UnitEnum|null $navigationGroup = 'Administration';
    protected static ?int $navigationSort = 99;

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('manage_users') ?? false;
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('created_at')->label('Time')->dateTime('Y-m-d H:i:s')->sortable(),
            TextColumn::make('causer.name')->label('User')->searchable(),
            TextColumn::make('event')->badge()->color(fn ($s) => match ($s) {
                'created' => 'success',
                'updated' => 'warning',
                'deleted' => 'danger',
                default => 'gray',
            }),
            TextColumn::make('description')->label('Action')->searchable()->limit(60),
            TextColumn::make('subject_type')->label('Type')->formatStateUsing(fn ($s) => class_basename($s))->searchable(),
            TextColumn::make('subject_id')->label('Subject ID'),
        ])->defaultSort('created_at', 'desc')->filters([
            SelectFilter::make('event')->options([
                'created' => 'Created',
                'updated' => 'Updated',
                'deleted' => 'Deleted',
            ]),
            SelectFilter::make('subject_type')->options(
                Activity::distinct('subject_type')->pluck('subject_type', 'subject_type')
                    ->map(fn ($v) => class_basename($v))->toArray()
            ),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivityLogs::route('/'),
        ];
    }
}
