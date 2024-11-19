<?php

namespace App\Tests\Strategy;

use App\Item;
use App\Strategy\AppleAirPodsStrategy;
use PHPUnit\Framework\TestCase;

class AppleAirPodsStrategyTest extends TestCase
{
    // Apple AirPods Strategy Tests
    public function testAppleAirPodsStrategyUpdatesQuality(): void
    {
        $strategy = new AppleAirPodsStrategy();
        $item = new Item('Apple AirPods', 10, 20);

        $strategy->updateQuality($item);

        $this->assertEquals(21, $item->quality);
        $this->assertEquals(9, $item->sellIn);
        $this->assertEquals('Apple AirPods', $strategy->getItemName());
    }

    public function testAppleAirPodsQualityNeverExceeds50(): void
    {
        $strategy = new AppleAirPodsStrategy();
        $item = new Item('Apple AirPods', 10, 50);

        $strategy->updateQuality($item);

        $this->assertEquals(50, $item->quality);
    }

}