<?php

declare(strict_types=1);

namespace App\Services\Dcm4chee;

use Carbon\Carbon;

final class DicomHelper
{
    public static function formatPatientName(?string $rawName): string
    {
        if ($rawName === null || $rawName === '-') {
            return '-';
        }
        if (! str_contains($rawName, '^')) {
            return $rawName;
        }
        $parts = array_filter(explode('^', $rawName));

        return implode(' ', array_reverse($parts));
    }

    public static function formatStudyDate(?string $rawDate): string
    {
        if ($rawDate === null || strlen($rawDate) !== 8) {
            return $rawDate ?? '-';
        }

        return Carbon::createFromFormat('Ymd', $rawDate)->format('d/m/Y');
    }

    public static function modalityColor(string $modality): string
    {
        return match ($modality) {
            'CT' => 'warning',
            'MR' => 'info',
            'CR', 'DX' => 'success',
            'US' => 'danger',
            default => 'gray',
        };
    }
}
