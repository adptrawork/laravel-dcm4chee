<?php

declare(strict_types=1);

namespace App\Dto;

use App\Services\Dcm4chee\DicomHelper;
use Livewire\Wireable;

final class SeriesData implements Wireable
{
    public function __construct(
        public readonly ?string $seriesNumber,
        public readonly ?string $seriesDescription,
        public readonly ?string $modality,
        public readonly int $instances,
        public readonly ?string $seriesUid,
    ) {}

    public static function fromDicomJson(array $json): self
    {
        $extract = fn (string $tag) => $json[$tag]['Value'][0] ?? null;

        return new self(
            seriesNumber: (string) ($extract('00200011') ?? ''),
            seriesDescription: $extract('0008103E'),
            modality: $extract('00080060'),
            instances: (int) ($extract('00201209') ?? 0),
            seriesUid: $extract('0020000E'),
        );
    }

    public function modalityColor(): string
    {
        return DicomHelper::modalityColor($this->modality ?? '');
    }

    public function toLivewire(): array
    {
        return [
            'seriesNumber' => $this->seriesNumber,
            'seriesDescription' => $this->seriesDescription,
            'modality' => $this->modality,
            'instances' => $this->instances,
            'seriesUid' => $this->seriesUid,
        ];
    }

    public static function fromLivewire($value): static
    {
        return new static(...$value);
    }
}
