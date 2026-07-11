<?php

declare(strict_types=1);

namespace App\Dto;

use App\Services\Dcm4chee\DicomHelper;

final class StudyData
{
    public function __construct(
        public readonly ?string $patientName,
        public readonly ?string $patientId,
        public readonly ?string $studyDate,
        public readonly ?string $studyTime,
        public readonly ?string $studyDescription,
        public readonly ?string $accessionNumber,
        public readonly array $modalities,
        public readonly int $series,
        public readonly int $instances,
        public readonly ?string $studyUid,
        public readonly ?string $referringPhysician,
    ) {}

    public static function fromDicomJson(array $json): self
    {
        $extract = fn (string $tag) => $json[$tag]['Value'][0] ?? null;
        $extractPn = function (string $tag) use ($extract): ?string {
            $val = $extract($tag);
            return is_array($val) ? ($val['Alphabetic'] ?? null) : (is_string($val) ? $val : null);
        };

        $modalities = $json['00080061']['Value'] ?? [];
        if (is_string($modalities)) $modalities = [$modalities];

        return new self(
            patientName: $extractPn('00100010'),
            patientId: $extract('00100020'),
            studyDate: $extract('00080020'),
            studyTime: $extract('00080030'),
            studyDescription: $extract('00081030'),
            accessionNumber: $extract('00080050'),
            modalities: $modalities,
            series: (int) ($extract('00201206') ?? 0),
            instances: (int) ($extract('00201208') ?? 0),
            studyUid: $extract('0020000D'),
            referringPhysician: $extractPn('00080090'),
        );
    }

    public function formattedPatientName(): string
    {
        return DicomHelper::formatPatientName($this->patientName);
    }

    public function formattedStudyDate(): string
    {
        return DicomHelper::formatStudyDate($this->studyDate);
    }

    public function modalityColors(): array
    {
        return array_map(fn (string $m) => [
            'label' => $m,
            'color' => DicomHelper::modalityColor($m),
        ], $this->modalities);
    }

    public function ohifUrl(): ?string
    {
        if (!$this->studyUid) return null;
        return config('services.ohif.url', 'http://localhost:3000')
            . '/viewer?StudyInstanceUIDs=' . $this->studyUid;
    }
}
