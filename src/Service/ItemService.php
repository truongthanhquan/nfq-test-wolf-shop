<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\ItemEntity;
use App\Service\ImageUpload\ImageUploadServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

readonly class ItemService
{
    public function __construct(
        private ImageUploadServiceInterface $imageUploadService,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function uploadImage(ItemEntity $itemEntity, UploadedFile $file): void
    {
        // Delete existing image if present
        if ($itemEntity->getImage() !== null) {
            $this->imageUploadService->delete($itemEntity->getImage()['publicId']);
        }

        // Upload new image
        $uploadResult = $this->imageUploadService->upload($file);

        $itemEntity->setImage((array) $uploadResult);
        $this->entityManager->flush();
    }

    public function removeImage(ItemEntity $itemEntity): void
    {
        if ($itemEntity->getImage() !== null) {
            $this->imageUploadService->delete($itemEntity->getImage()['publicId']);
            $itemEntity->setImage(null);
            $this->entityManager->flush();
        }
    }
}
