<?php

declare(strict_types=1);

namespace App\Strategy;

use App\Item;

class AppleAirPodsStrategy implements QualityUpdateStrategyInterface
{
    public function getItemName(): string
    {
        return 'Apple AirPods';
    }

    public function updateQuality(Item $item): void
    {
        $item->quality = min(50, $item->quality + 1);
        $item->sellIn--;
    }
}
