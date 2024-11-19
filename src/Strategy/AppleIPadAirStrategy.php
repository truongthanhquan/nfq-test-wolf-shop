<?php

declare(strict_types=1);

namespace App\Strategy;

use App\Item;

class AppleIPadAirStrategy implements QualityUpdateStrategyInterface
{
    public function getItemName(): string
    {
        return 'Apple iPad Air';
    }

    public function updateQuality(Item $item): void
    {
        if ($item->sellIn <= 0) {
            $item->quality = 0;
        } elseif ($item->sellIn <= 5) {
            $item->quality = min(50, $item->quality + 3);
        } elseif ($item->sellIn <= 10) {
            $item->quality = min(50, $item->quality + 2);
        } else {
            $item->quality = min(50, $item->quality + 1);
        }
        $item->sellIn--;
    }
}
