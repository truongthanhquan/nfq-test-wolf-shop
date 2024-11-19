<?php

declare(strict_types=1);

namespace App\DTO;

class StoredImageDTO
{
    public function __construct(
        public string $storageName,
        public ?string $publicId,
        public string $url,
        public ?int $width,
        public ?int $height,
        public ?string $format,
        public ?\DateTimeImmutable $createdAt = null
    ) {
    }
}
