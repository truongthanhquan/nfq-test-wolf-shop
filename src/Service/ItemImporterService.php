<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\ApiItemDTO;
use App\Entity\ItemEntity;
use App\Factory\ItemEntityFactory;
use App\Repository\ItemEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Exception\RuntimeException;

readonly class ItemImporterService
{
    public function __construct(
        private ApiItemService $itemApiService,
        private ItemEntityFactory $itemEntityFactory,
        private ItemEntityRepository $itemEntityRepository,
        private ItemQualityUpdaterService $itemQualityUpdater,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function import(): int
    {
        $items = $this->itemApiService->fetchItems();
        $countItemProcessed = 0;
        $this->entityManager->beginTransaction();
        $itemLoaded = [];
        try {
            foreach ($items as $item) {
                $itemEntity = $itemLoaded[$item->name] ?? $this->getItemEntityByName($item->name);

                if ($itemEntity) {
                    $this->updateQuality($itemEntity);
                } else {
                    $itemEntity = $this->createNewItem($item);
                }

                $countItemProcessed++;

                // Flush in batches of 10 items
                if ($countItemProcessed % 10 === 0) {
                    $this->entityManager->flush();
                    $itemLoaded = [];
                } else {
                    //Make sure don't load item again
                    $itemLoaded[$item->name] = $itemEntity;
                }
            }

            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (\Exception $e) {
            $this->entityManager->rollback();
            throw new RuntimeException(
                message: sprintf('Import data from API failed. Error: %s', $e->getMessage()),
                code: $e->getCode(),
                previous: $e
            );
        }

        return $countItemProcessed;
    }

    private function getItemEntityByName(string $name): ?ItemEntity
    {
        return $this->itemEntityRepository->findOneByName($name);
    }

    private function updateQuality(ItemEntity $itemEntity): void
    {
        $item = $itemEntity->__toItem();
        $this->itemQualityUpdater->updateQuality($item);
        $itemEntity->setQuality($item->quality);
        $itemEntity->setSellIn($item->sellIn);
    }

    private function createNewItem(ApiItemDTO $item): ItemEntity
    {
        $itemEntity = $this->itemEntityFactory->createFromApiItemDTO($item);
        $this->entityManager->persist($itemEntity);
        return $itemEntity;
    }
}
