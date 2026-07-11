<?php

declare(strict_types=1);

namespace App\Dto;

final class InstanceData
{
    public function __construct(
        public readonly ?string $instanceUid,
        public readonly ?string $instanceNumber,
        public readonly ?string $sopClass,
        public readonly ?string $sopClassDescription,
    ) {}

    public static function fromDicomJson(array $json): self
    {
        $extract = fn (string $tag) => $json[$tag]['Value'][0] ?? null;

        return new self(
            instanceUid: $extract('00080018'),
            instanceNumber: $extract('00200013'),
            sopClass: $extract('00080016'),
            sopClassDescription: $extract('00080054'),
        );
    }
}
