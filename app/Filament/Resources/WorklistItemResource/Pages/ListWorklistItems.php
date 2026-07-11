<?php

namespace App\Filament\Resources\WorklistItemResource\Pages;

use App\Filament\Resources\WorklistItemResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWorklistItems extends ListRecords
{
    protected static string $resource = WorklistItemResource::class;
    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
