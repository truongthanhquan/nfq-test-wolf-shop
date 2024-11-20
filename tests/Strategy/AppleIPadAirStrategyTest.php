<?php

declare(strict_types=1);

namespace App\Tests\Strategy;

use App\Item;
use App\Strategy\AppleIPadAirStrategy;
use App\Strategy\QualityUpdateStrategyInterface;
use PHPUnit\Framework\TestCase;

class AppleIPadAirStrategyTest extends TestCase
{
    public const ITEM_NAME = 'Apple iPad Air';

    private QualityUpdateStrategyInterface $strategy;

    protected function setUp(): void
    {
        $this->strategy = new AppleIPadAirStrategy();
    }

    public function testName(): void
    {
        $this->assertEquals(static::ITEM_NAME, $this->strategy->getItemName());
    }

    public function testNormalPeriod(): void
    {
        $item = new Item(static::ITEM_NAME, 11, 20);

        $this->strategy->updateQuality($item);

        $this->assertEquals(21, $item->quality);
        $this->assertEquals(10, $item->sellIn);
    }

    public function testWithin10Days(): void
    {
        $item = new Item(static::ITEM_NAME, 10, 20);
        $this->strategy->updateQuality($item);
        $this->assertEquals(22, $item->quality);
    }

    public function testWithin5Days(): void
    {
        $item = new Item(static::ITEM_NAME, 5, 20);
        $this->strategy->updateQuality($item);
        $this->assertEquals(23, $item->quality);
    }

    public function testExpired(): void
    {
        $item = new Item(static::ITEM_NAME, 0, 20);
        $this->strategy->updateQuality($item);

        $this->assertEquals(0, $item->quality);
    }
}
