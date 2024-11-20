<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\DTO\StoredImageDTO;
use App\Entity\ItemEntity;
use App\Service\ImageUpload\ImageUploadServiceInterface;
use App\Service\ItemService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ItemServiceTest extends TestCase
{
    private ItemService $itemService;

    private MockObject $loggerMock;

    private MockObject $imageUploadServiceMock;

    private MockObject $entityManagerMock;

    protected function setUp(): void
    {
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->imageUploadServiceMock = $this->createMock(ImageUploadServiceInterface::class);
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);

        $this->itemService = new ItemService(
            $this->loggerMock,
            $this->imageUploadServiceMock,
            $this->entityManagerMock
        );
    }

    public function testUploadImageUploadsAndReplacesImage(): void
    {
        $itemEntity = new ItemEntity();
        $itemEntity->setImage([
            'publicId' => 'old-image-id',
        ]);
        $uploadedFile = $this->createMock(UploadedFile::class);

        $storedImageDTO = new StoredImageDTO(
            'cloudinary',
            'new-image-id',
            'url/to/new-image',
            800,
            600,
            'jpeg',
            '2024-11-19T12:00:00Z'
        );

        $this->imageUploadServiceMock
            ->expects($this->once())
            ->method('upload')
            ->with($uploadedFile)
            ->willReturn($storedImageDTO);

        $this->imageUploadServiceMock
            ->expects($this->once())
            ->method('delete')
            ->with('old-image-id')
            ->willReturn(true);

        $this->entityManagerMock
            ->expects($this->once())
            ->method('flush');

        $this->itemService->uploadImage($itemEntity, $uploadedFile);

        $this->assertSame(
            [
                'storageName' => 'cloudinary',
                'publicId' => 'new-image-id',
                'url' => 'url/to/new-image',
                'width' => 800,
                'height' => 600,
                'format' => 'jpeg',
                'createdAt' => '2024-11-19T12:00:00Z',
            ],
            $itemEntity->getImage()
        );
    }

    public function testUploadImageLogsErrorWhenDeleteFails(): void
    {
        $itemEntity = new ItemEntity();
        $itemEntity->setImage([
            'publicId' => 'old-image-id',
        ]);
        $uploadedFile = $this->createMock(UploadedFile::class);

        $storedImageDTO = new StoredImageDTO(
            'cloudinary',
            'new-image-id',
            'url/to/new-image',
            800,
            600,
            'jpeg',
            '2024-11-19T12:00:00Z'
        );

        $this->imageUploadServiceMock
            ->expects($this->once())
            ->method('upload')
            ->with($uploadedFile)
            ->willReturn($storedImageDTO);

        $this->imageUploadServiceMock
            ->expects($this->once())
            ->method('delete')
            ->with('old-image-id')
            ->willThrowException(new \Exception("Can't delete image"));

        $this->loggerMock
            ->expects($this->once())
            ->method('error')
            ->with("Can't delete old image Item. The image ID: old-image-id");

        $this->entityManagerMock
            ->expects($this->once())
            ->method('flush');

        $this->itemService->uploadImage($itemEntity, $uploadedFile);

        $this->assertSame(
            [
                'storageName' => 'cloudinary',
                'publicId' => 'new-image-id',
                'url' => 'url/to/new-image',
                'width' => 800,
                'height' => 600,
                'format' => 'jpeg',
                'createdAt' => '2024-11-19T12:00:00Z',
            ],
            $itemEntity->getImage()
        );
    }

    public function testRemoveImageDeletesAndFlushes(): void
    {
        $itemEntity = new ItemEntity();
        $itemEntity->setImage([
            'publicId' => 'image-id',
        ]);

        $this->imageUploadServiceMock
            ->expects($this->once())
            ->method('delete')
            ->with('image-id')
            ->willReturn(true);

        $this->entityManagerMock
            ->expects($this->once())
            ->method('flush');

        $this->itemService->removeImage($itemEntity);

        $this->assertNull($itemEntity->getImage());
    }

    public function testRemoveImageDoesNothingWhenNoImage(): void
    {
        $itemEntity = new ItemEntity();

        $this->imageUploadServiceMock
            ->expects($this->never())
            ->method('delete');

        $this->entityManagerMock
            ->expects($this->never())
            ->method('flush');

        $this->itemService->removeImage($itemEntity);

        $this->assertNull($itemEntity->getImage());
    }
}
