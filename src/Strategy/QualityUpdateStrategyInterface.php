<?php

declare(strict_types=1);

namespace App\Strategy;

use App\Item;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.item_quality_strategy')]
interface QualityUpdateStrategyInterface
{
    public function getItemName(): string;

    public function updateQuality(Item $item): void;
}
