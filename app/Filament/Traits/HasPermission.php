<?php

namespace App\Filament\Traits;

trait HasPermission
{
    public static function canViewAny(): bool
    {
        return auth()->user()?->can(static::$permission ?? 'view_dashboard') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can(static::$permission ?? 'view_dashboard') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can(static::$permission ?? 'view_dashboard') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can(static::$permission ?? 'view_dashboard') ?? false;
    }
}
