<?php

declare(strict_types=1);

namespace App\Strategy;

use App\Item;

class XiaomiRedmiStrategy implements QualityUpdateStrategyInterface
{
    public function getItemName(): string
    {
        return 'Xiaomi Redmi Note 13';
    }

    public function updateQuality(Item $item): void
    {
        if ($item->sellIn > 0) {
            $item->quality = max(0, $item->quality - 2);
        } else {
            $item->quality = max(0, $item->quality - 4);
        }
        $item->sellIn--;
    }
}
