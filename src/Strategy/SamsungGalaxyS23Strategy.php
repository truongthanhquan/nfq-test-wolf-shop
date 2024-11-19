<?php

declare(strict_types=1);

namespace App\Strategy;

use App\Item;

class SamsungGalaxyS23Strategy implements QualityUpdateStrategyInterface
{
    public function getItemName(): string
    {
        return 'Samsung Galaxy S23';
    }

    public function updateQuality(Item $item): void
    {
        // Legendary item - quality stays at 80, never changes
        $item->quality = 80;
    }
}
