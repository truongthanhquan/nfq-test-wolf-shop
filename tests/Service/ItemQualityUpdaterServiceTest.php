<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Item;
use App\Service\ItemQualityUpdaterService;
use App\Strategy\AppleAirPodsStrategy;
use App\Strategy\StandardQualityStrategy;
use PHPUnit\Framework\TestCase;

class ItemQualityUpdaterServiceTest extends TestCase
{
    public function testUsesCorrectStrategy(): void
    {
        $standardStrategy = new StandardQualityStrategy();
        $airPodsStrategy = new AppleAirPodsStrategy();

        $service = new ItemQualityUpdaterService(
            [$airPodsStrategy],
            $standardStrategy
        );

        $airPodsItem = new Item('Apple AirPods', 10, 20);
        $standardItem = new Item('Unknown Item', 10, 20);

        $service->updateQuality($airPodsItem);
        $service->updateQuality($standardItem);

        $this->assertEquals(21, $airPodsItem->quality);
        $this->assertEquals(19, $standardItem->quality);
    }
}
