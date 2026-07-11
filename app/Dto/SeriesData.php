<?php

declare(strict_types=1);

namespace App\Dto;

use App\Services\Dcm4chee\DicomHelper;

final class SeriesData
{
    public function __construct(
        public readonly ?string $seriesUid,
        public readonly ?string $seriesNumber,
        public readonly ?string $seriesDescription,
        public readonly ?string $modality,
        public readonly int $instances,
    ) {}

    public static function fromDicomJson(array $json): self
    {
        $extract = fn (string $tag) => $json[$tag]['Value'][0] ?? null;

        return new self(
            seriesUid: $extract('0020000E'),
            seriesNumber: $extract('00200011'),
            seriesDescription: $extract('0008103E'),
            modality: $extract('00080060'),
            instances: (int) ($extract('00201209') ?? 0),
        );
    }

    public function modalityColor(): string
    {
        return DicomHelper::modalityColor($this->modality ?? '');
    }
}
