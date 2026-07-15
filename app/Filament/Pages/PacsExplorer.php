<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use Filament\Pages\Page;

class PacsExplorer extends Page
{
    protected string $view = 'filament.pages.pacs-explorer';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-table-cells';

    protected static string|\UnitEnum|null $navigationGroup = 'Imaging';

    protected static ?int $navigationSort = 1;

    public string $studyUid = '';

    public ?string $error = null;

    public static function canView(): bool
    {
        return auth()->user()?->can('view_studies') ?? false;
    }

    public function explore(): void
    {
        $this->error = null;

        $uid = trim($this->studyUid);
        if (empty($uid)) {
            $this->error = 'Study Instance UID harus diisi';

            return;
        }

        $this->redirect(url('/admin/studies/'.$uid));
    }
}
