<?php

declare(strict_types=1);

namespace App\Tests\Strategy;

use App\Item;
use App\Strategy\QualityUpdateStrategyInterface;
use App\Strategy\XiaomiRedmiStrategy;
use PHPUnit\Framework\TestCase;

class XiaomiRedmiStrategyTest extends TestCase
{
    public const ITEM_NAME = 'Xiaomi Redmi Note 13';

    private QualityUpdateStrategyInterface $strategy;

    protected function setUp(): void
    {
        $this->strategy = new XiaomiRedmiStrategy();
    }

    public function testName(): void
    {
        $this->assertEquals(static::ITEM_NAME, $this->strategy->getItemName());
    }

    public function testNormalDecrease(): void
    {
        $item = new Item(static::ITEM_NAME, 10, 20);

        $this->strategy->updateQuality($item);

        $this->assertEquals(18, $item->quality);
        $this->assertEquals(9, $item->sellIn);
    }

    public function testExpiredDecrease(): void
    {
        $item = new Item(static::ITEM_NAME, 0, 20);

        $this->strategy->updateQuality($item);

        $this->assertEquals(16, $item->quality);
        $this->assertEquals(-1, $item->sellIn);
    }
}
