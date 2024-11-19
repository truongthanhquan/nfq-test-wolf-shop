<?php

declare(strict_types=1);

namespace App\Factory;

use App\DTO\ApiItemDTO;

class ApiItemDTOFactory
{
    public function create(array $item): ApiItemDTO
    {
        return new ApiItemDTO(
            id: (int) ($item['id'] ?? null),
            name: (string) ($item['name'] ?? ''),
            attributes: (array) ($item['attributes'] ?? null)
        );
    }
}
