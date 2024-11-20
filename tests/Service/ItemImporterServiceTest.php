<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\DTO\ApiItemDTO;
use App\Entity\ItemEntity;
use App\Factory\ItemEntityFactory;
use App\Item;
use App\Repository\ItemEntityRepository;
use App\Service\ApiItemService;
use App\Service\ItemImporterService;
use App\Service\ItemQualityUpdaterService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ItemImporterServiceTest extends TestCase
{
    private MockObject $itemApiServiceMock;

    private MockObject $itemEntityFactoryMock;

    private MockObject $itemEntityRepositoryMock;

    private MockObject $itemQualityUpdaterMock;

    private MockObject $entityManagerMock;

    private ItemImporterService $service;

    protected function setUp(): void
    {
        // Mock dependencies
        $this->itemApiServiceMock = $this->createMock(ApiItemService::class);
        $this->itemEntityFactoryMock = $this->createMock(ItemEntityFactory::class);
        $this->itemEntityRepositoryMock = $this->createMock(ItemEntityRepository::class);
        $this->itemQualityUpdaterMock = $this->createMock(ItemQualityUpdaterService::class);
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);

        // Instantiate the service
        $this->service = new ItemImporterService(
            $this->itemApiServiceMock,
            $this->itemEntityFactoryMock,
            $this->itemEntityRepositoryMock,
            $this->itemQualityUpdaterMock,
            $this->entityManagerMock
        );
    }

    public function testImportCreatesNewItemsAndUpdatesExisting(): void
    {
        $itemDTO = $this->createMock(ApiItemDTO::class);
        $itemDTO->name = 'item1';

        $itemEntity = $this->createMock(ItemEntity::class);

        $this->itemApiServiceMock->method('fetchItems')->willReturn([$itemDTO]);

        $this->itemEntityRepositoryMock->method('findOneByName')->willReturn(null);

        $this->itemEntityFactoryMock->method('createFromApiItemDTO')->willReturn($itemEntity);

        $this->entityManagerMock->expects($this->once())->method('beginTransaction');
        $this->entityManagerMock->expects($this->once())->method('persist')->with($itemEntity);
        $this->entityManagerMock->expects($this->once())->method('flush');
        $this->entityManagerMock->expects($this->once())->method('commit');

        $processedCount = $this->service->import();

        $this->assertEquals(1, $processedCount);
    }

    public function testImportWithExistingItemUpdatesQuality(): void
    {
        $itemDTO = $this->createMock(ApiItemDTO::class);
        $itemDTO->name = 'item1';


        $item = new Item('item1', 10, 20);
        $existingItemEntity = $this->createMock(ItemEntity::class);
        $existingItemEntity->method('__toItem')->willReturn($item);

        $this->itemApiServiceMock->method('fetchItems')->willReturn([$itemDTO]);

        $this->itemEntityRepositoryMock->method('findOneByName')->willReturn($existingItemEntity);

        $this->itemQualityUpdaterMock->expects($this->once())->method('updateQuality');

        $this->entityManagerMock->expects($this->once())->method('beginTransaction');
        $this->entityManagerMock->expects($this->once())->method('flush');
        $this->entityManagerMock->expects($this->once())->method('commit');

        $processedCount = $this->service->import();

        $this->assertEquals(1, $processedCount);
    }

    public function testImportHandlesBatchFlushes(): void
    {
        $itemDTO1 = $this->createMock(ApiItemDTO::class);
        $itemDTO1->name = 'item1';
        $itemDTO2 = $this->createMock(ApiItemDTO::class);
        $itemDTO2->name = 'item2';

        $this->itemApiServiceMock->method('fetchItems')->willReturn([$itemDTO1, $itemDTO2]);

        $this->itemEntityRepositoryMock->method('findOneByName')->willReturn(null);

        $itemEntity1 = $this->createMock(ItemEntity::class);
        $itemEntity2 = $this->createMock(ItemEntity::class);
        $this->itemEntityFactoryMock->method('createFromApiItemDTO')->willReturnOnConsecutiveCalls($itemEntity1, $itemEntity2);

        $this->entityManagerMock->expects($this->exactly(2))->method('persist');
        $this->entityManagerMock->expects($this->once())->method('flush');
        $this->entityManagerMock->expects($this->once())->method('commit');

        $processedCount = $this->service->import();

        $this->assertEquals(2, $processedCount);
    }

    public function testImportHandlesExceptionAndRollsBackTransaction(): void
    {
        $itemDTO = $this->createMock(ApiItemDTO::class);
        $itemDTO->name = 'item1';

        $this->itemApiServiceMock->method('fetchItems')->willReturn([$itemDTO]);

        $this->itemEntityRepositoryMock->method('findOneByName')
            ->willThrowException(new \Exception('Exception FindOne'));

        $this->entityManagerMock->expects($this->once())->method('beginTransaction');
        $this->entityManagerMock->expects($this->once())->method('rollback');
        $this->entityManagerMock->expects($this->never())->method('flush');
        $this->entityManagerMock->expects($this->never())->method('commit');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Import data from API failed');

        $this->service->import();
    }
}
