<?php

declare(strict_types=1);

namespace App\Tests\Strategy;

use App\Item;
use App\Strategy\QualityUpdateStrategyInterface;
use App\Strategy\SamsungGalaxyS23Strategy;
use PHPUnit\Framework\TestCase;

class SamsungGalaxyS23StrategyTest extends TestCase
{
    public const ITEM_NAME = 'Samsung Galaxy S23';

    private QualityUpdateStrategyInterface $strategy;

    protected function setUp(): void
    {
        $this->strategy = new SamsungGalaxyS23Strategy();
    }

    public function testName(): void
    {
        $this->assertEquals(static::ITEM_NAME, $this->strategy->getItemName());
    }

    public function testMaintainsQuality(): void
    {
        $item = new Item(static::ITEM_NAME, 10, 50);

        $this->strategy->updateQuality($item);

        $this->assertEquals(80, $item->quality);
    }
}
