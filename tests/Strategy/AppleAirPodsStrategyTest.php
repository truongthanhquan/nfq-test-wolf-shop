<?php

declare(strict_types=1);

namespace App\Tests\Strategy;

use App\Item;
use App\Strategy\AppleAirPodsStrategy;
use App\Strategy\QualityUpdateStrategyInterface;
use PHPUnit\Framework\TestCase;

class AppleAirPodsStrategyTest extends TestCase
{
    public const ITEM_NAME = 'Apple AirPods';

    private QualityUpdateStrategyInterface $strategy;

    protected function setUp(): void
    {
        $this->strategy = new AppleAirPodsStrategy();
    }

    public function testName(): void
    {
        $this->assertEquals(static::ITEM_NAME, $this->strategy->getItemName());
    }

    public function testUpdatesQuality(): void
    {
        $item = new Item(static::ITEM_NAME, 10, 20);
        $this->strategy->updateQuality($item);

        $this->assertEquals(21, $item->quality);
        $this->assertEquals(9, $item->sellIn);
        $this->assertEquals(static::ITEM_NAME, $this->strategy->getItemName());
    }

    public function testNeverExceeds50(): void
    {
        $item = new Item(static::ITEM_NAME, 10, 50);

        $this->strategy->updateQuality($item);

        $this->assertEquals(50, $item->quality);
    }
}
