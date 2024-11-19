<?php

declare(strict_types=1);

namespace App\Factory;

use App\DTO\ApiItemDTO;
use App\Entity\ItemEntity;

class ItemEntityFactory
{
    public function createFromApiItemDTO(ApiItemDTO $itemDTO): ItemEntity
    {
        $itemEntity = new ItemEntity();
        $itemEntity->setName($itemDTO->name);
        $itemEntity->setQuality(0);
        $itemEntity->setSellIn(0);
        $itemEntity->setAttributes($itemDTO->attributes);
        return $itemEntity;
    }
}
