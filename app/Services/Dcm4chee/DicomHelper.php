<?php

declare(strict_types=1);

namespace App\Services\Dcm4chee;

use App\Dto\StudyData;
use App\Models\Device;

final class DicomHelper
{
    const PATIENT_TAGS = [
        'PatientName' => ['tag' => '00100010', 'vr' => 'PN'],
        'PatientID' => ['tag' => '00100020', 'vr' => 'LO'],
        'PatientBirthDate' => ['tag' => '00100030', 'vr' => 'DA'],
        'PatientSex' => ['tag' => '00100040', 'vr' => 'CS'],
    ];

    const STUDY_TAGS = [
        'StudyInstanceUID' => ['tag' => '0020000D', 'vr' => 'UI'],
        'StudyDate' => ['tag' => '00080020', 'vr' => 'DA'],
        'StudyTime' => ['tag' => '00080030', 'vr' => 'TM'],
        'StudyDescription' => ['tag' => '00081030', 'vr' => 'LO'],
        'StudyID' => ['tag' => '00200010', 'vr' => 'SH'],
        'AccessionNumber' => ['tag' => '00080050', 'vr' => 'SH'],
        'ReferringPhysicianName' => ['tag' => '00080090', 'vr' => 'PN'],
        'ModalitiesInStudy' => ['tag' => '00080061', 'vr' => 'CS'],
        'NumberOfStudyRelatedSeries' => ['tag' => '00201206', 'vr' => 'IS'],
        'NumberOfStudyRelatedInstances' => ['tag' => '00201208', 'vr' => 'IS'],
        'PatientName' => ['tag' => '00100010', 'vr' => 'PN'],
        'PatientID' => ['tag' => '00100020', 'vr' => 'LO'],
    ];

    const SERIES_TAGS = [
        'SeriesInstanceUID' => ['tag' => '0020000E', 'vr' => 'UI'],
        'SeriesNumber' => ['tag' => '00200011', 'vr' => 'IS'],
        'SeriesDescription' => ['tag' => '0008103E', 'vr' => 'LO'],
        'Modality' => ['tag' => '00080060', 'vr' => 'CS'],
        'NumberOfSeriesRelatedInstances' => ['tag' => '00201209', 'vr' => 'IS'],
    ];

    public static function buildPatientJson(string $name, string $patientId, ?string $birthDate = null, ?string $sex = null): array
    {
        $json = [];

        $json['00100010'] = [
            'vr' => 'PN',
            'Value' => [['Alphabetic' => $name]],
        ];

        $json['00100020'] = [
            'vr' => 'LO',
            'Value' => [$patientId],
        ];

        if ($birthDate) {
            $json['00100030'] = [
                'vr' => 'DA',
                'Value' => [str_replace('-', '', $birthDate)],
            ];
        }

        if ($sex) {
            $json['00100040'] = [
                'vr' => 'CS',
                'Value' => [$sex],
            ];
        }

        return $json;
    }

    public static function extractValue(array $dicomJson, string $tag): mixed
    {
        return $dicomJson[$tag]['Value'][0] ?? null;
    }

    public static function extractName(array $dicomJson): ?string
    {
        $val = self::extractValue($dicomJson, '00100010');

        if (is_array($val)) {
            return $val['Alphabetic'] ?? ($val['Phonetic'] ?? null);
        }

        return $val;
    }

    public static function buildMwlJson(\App\Models\Order $order): array
    {
        $patient = $order->patient;
        $json = [];

        $json['00100010'] = ['vr' => 'PN', 'Value' => [['Alphabetic' => $patient->name]]];
        $json['00100020'] = ['vr' => 'LO', 'Value' => [$patient->patient_id]];

        if ($patient->date_of_birth) {
            $json['00100030'] = ['vr' => 'DA', 'Value' => [$patient->date_of_birth->format('Ymd')]];
        }
        if ($patient->sex) {
            $json['00100040'] = ['vr' => 'CS', 'Value' => [$patient->sex]];
        }

        $json['00080050'] = ['vr' => 'SH', 'Value' => [$order->accession_number]];

        if ($order->requesting_physician) {
            $json['00080090'] = ['vr' => 'PN', 'Value' => [['Alphabetic' => $order->requesting_physician]]];
        }

        $sps = ['00400020' => ['vr' => 'CS', 'Value' => ['SCHEDULED']]];

        $modality = $order->modality ?? $order->procedure?->modality;
        if ($modality) {
            $sps['00400007'] = ['vr' => 'LO', 'Value' => [$modality]];
        }

        if ($order->requesting_physician) {
            $json['00321032'] = ['vr' => 'PN', 'Value' => [['Alphabetic' => $order->requesting_physician]]];
        }

        $desc = $order->procedure?->name;
        if ($desc) {
            $json['00321060'] = ['vr' => 'LO', 'Value' => [$desc]];
        }

        // Required SPS fields — ScheduledStationAETitle
        $stationAe = $order->device?->ae_title
            ?? Device::where('modality', $modality)->where('status', 'active')->value('ae_title')
            ?? 'DCM4CHEE';
        $sps['00400001'] = ['vr' => 'AE', 'Value' => [$stationAe]];
        $sps['00400009'] = ['vr' => 'SH', 'Value' => ['SPS-' . $order->accession_number]];

        $date = $order->scheduled_date?->format('Ymd') ?? now()->format('Ymd');
        $sps['00400002'] = ['vr' => 'DA', 'Value' => [$date]];
        $sps['00400003'] = ['vr' => 'TM', 'Value' => ['000000']];

        $json['00400100'] = ['vr' => 'SQ', 'Value' => [$sps]];

        return $json;
    }

    public static function flattenStudies(array $studies): array
    {
        return array_map(fn ($s) => self::flattenSingle($s, self::STUDY_TAGS), $studies);
    }

    public static function flattenToDto(array $studies): array
    {
        return array_map(fn (array $s) => StudyData::fromDicomJson($s), $studies);
    }

    public static function flattenPatient(array $patient): array
    {
        return self::flattenSingle($patient, self::PATIENT_TAGS);
    }

    public static function flattenSeries(array $series): array
    {
        return array_map(fn ($s) => self::flattenSingle($s, self::SERIES_TAGS), $series);
    }

    protected static function flattenSingle(array $dicomJson, array $tagMap): array
    {
        $result = [];

        foreach ($tagMap as $key => $meta) {
            $value = self::extractValue($dicomJson, $meta['tag']);

            if ($meta['vr'] === 'PN' && is_array($value)) {
                $result[$key] = $value['Alphabetic'] ?? json_encode($value);
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    public static function formatPatientName(?string $rawName): string
    {
        if ($rawName === null || $rawName === '-') return '-';
        if (!str_contains($rawName, '^')) return $rawName;
        $parts = array_filter(explode('^', $rawName));
        return implode(' ', array_reverse($parts));
    }

    public static function formatStudyDate(?string $rawDate): string
    {
        if ($rawDate === null || strlen($rawDate) !== 8) return $rawDate ?? '-';
        return \Carbon\Carbon::createFromFormat('Ymd', $rawDate)->format('d/m/Y');
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

    public static function normalizeModalities(array|string|null $modalities): array
    {
        if ($modalities === null || $modalities === '-') return [];
        if (is_string($modalities)) return [$modalities];
        return $modalities;
    }

    public static function prettyJson(array $data): string
    {
        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
