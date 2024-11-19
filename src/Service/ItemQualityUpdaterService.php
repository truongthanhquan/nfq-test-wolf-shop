<?php

declare(strict_types=1);

namespace App\Service;

use App\Item;
use App\Strategy\QualityUpdateStrategyInterface;
use App\Strategy\StandardQualityStrategy;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

class ItemQualityUpdaterService
{
    /**
     * @var array<string, QualityUpdateStrategyInterface>
     */
    private array $strategies;

    /**
     * @param iterable<QualityUpdateStrategyInterface> $strategies
     */
    public function __construct(
        #[AutowireIterator('app.item_quality_strategy')]
        iterable $strategies,
        private readonly StandardQualityStrategy $standardStrategy
    ) {
        $this->strategies = [];
        foreach ($strategies as $strategy) {
            $this->strategies[$strategy->getItemName()] = $strategy;
        }
    }

    public function updateQuality(Item $item): void
    {
        $strategy = $this->strategies[$item->name] ?? $this->standardStrategy;
        $strategy->updateQuality($item);
    }
}
