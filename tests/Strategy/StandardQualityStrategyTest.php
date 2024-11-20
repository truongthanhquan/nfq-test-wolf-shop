<?php

declare(strict_types=1);

namespace App\Tests\Strategy;

use App\Item;
use App\Strategy\QualityUpdateStrategyInterface;
use App\Strategy\StandardQualityStrategy;
use PHPUnit\Framework\TestCase;

class StandardQualityStrategyTest extends TestCase
{
    public const ITEM_NAME = 'standard-is-anything-else-name';

    private QualityUpdateStrategyInterface $strategy;

    protected function setUp(): void
    {
        $this->strategy = new StandardQualityStrategy();
    }

    public function testName(): void
    {
        $this->assertEquals('standard', $this->strategy->getItemName());
    }

    public function testNormalDecrease(): void
    {
        $item = new Item(static::ITEM_NAME, 10, 20);

        $this->strategy->updateQuality($item);

        $this->assertEquals(19, $item->quality);
        $this->assertEquals(9, $item->sellIn);
    }

    public function testExpiredDecrease(): void
    {
        $item = new Item(static::ITEM_NAME, 0, 20);

        $this->strategy->updateQuality($item);

        $this->assertEquals(18, $item->quality);
        $this->assertEquals(-1, $item->sellIn);
    }
}
