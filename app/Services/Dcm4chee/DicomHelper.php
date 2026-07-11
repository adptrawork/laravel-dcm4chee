<?php

namespace App\Services\Dcm4chee;

class DicomHelper
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

    public static function flattenStudies(array $studies): array
    {
        return array_map(fn ($s) => self::flattenSingle($s, self::STUDY_TAGS), $studies);
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

    public static function prettyJson(array $data): string
    {
        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
