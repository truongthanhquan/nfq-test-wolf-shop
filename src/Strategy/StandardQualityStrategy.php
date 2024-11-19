<?php

declare(strict_types=1);

namespace App\Strategy;

use App\Item;

class StandardQualityStrategy implements QualityUpdateStrategyInterface
{
    public function getItemName(): string
    {
        return 'standard';
    }

    public function updateQuality(Item $item): void
    {
        if ($item->sellIn > 0) {
            $item->quality = max(0, $item->quality - 1);
        } else {
            $item->quality = max(0, $item->quality - 2);
        }
        $item->sellIn--;
    }
}
