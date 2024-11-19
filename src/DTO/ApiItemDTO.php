<?php

declare(strict_types=1);

namespace App\DTO;

class ApiItemDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public ?array $attributes = null,
    ) {
    }
}
