<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\ItemEntity;
use App\Service\ImageUpload\ImageUploadServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ItemService
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly ImageUploadServiceInterface $imageUploadService,
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function uploadImage(ItemEntity $itemEntity, UploadedFile $file): void
    {
        // Upload new image
        $uploadResult = $this->imageUploadService->upload($file);

        // Delete existing image if present
        if ($itemEntity->getImage() !== null) {
            $deletedImage = false;
            try {
                $deletedImage = $this->imageUploadService->delete($itemEntity->getImage()['publicId']);
            } catch (\Exception) {
            }

            if (! $deletedImage) {
                // Don't need to rollback when can't delete image
                $this->logger->error(sprintf("Can't delete old image Item. The image ID: %s", $itemEntity->getImage()['publicId']));
            }
        }

        $itemEntity->setImage((array) $uploadResult);
        $this->entityManager->flush();
    }

    public function removeImage(ItemEntity $itemEntity): void
    {
        if ($itemEntity->getImage() !== null) {
            if ($this->imageUploadService->delete($itemEntity->getImage()['publicId'])) {
                $itemEntity->setImage(null);
                $this->entityManager->flush();
            }
        }
    }
}
