<?php

declare(strict_types=1);

namespace App\Service\ImageUpload;

use App\DTO\StoredImageDTO;
use Cloudinary\Api\ApiResponse;
use Cloudinary\Cloudinary;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

readonly class CloudinaryImageUploadService implements ImageUploadServiceInterface
{
    public function __construct(
        private Cloudinary $cloudinary,
        private LoggerInterface $logger,
    ) {
    }

    public function getStorageName(): string
    {
        return 'cloudinary';
    }

    public function upload(UploadedFile $file): StoredImageDTO
    {
        try {
            $result = $this->cloudinary->uploadApi()->upload(
                $file->getRealPath(),
                [
                    'resource_type' => 'image',
                    'folder' => 'items',
                    'transformation' => [
                        'quality' => 'auto',
                        'fetch_format' => 'auto',
                    ],
                ]
            );

            return $this->createStoredImageDTO($result);
        } catch (\Exception $e) {
            throw new \RuntimeException(sprintf('Failed to upload image: %s', $e->getMessage()));
        }
    }

    public function delete(string $publicId): bool
    {
        try {
            $this->cloudinary->uploadApi()->destroy($publicId);
            return true;
        } catch (\Exception $e) {
            throw new \RuntimeException(sprintf('Failed to delete image: %s', $e->getMessage()));
        }
    }

    private function createStoredImageDTO(ApiResponse $result): StoredImageDTO
    {
        $strCreatedAt = is_string($result['created_at'] ?? null) ? $result['created_at'] : 'now';
        try {
            $createdAt = new \DateTime($strCreatedAt);
        } catch (\Exception) {
            $this->logger->warning(sprintf('The created_at invalid DateTime format: %s', $strCreatedAt));
            $createdAt = new \DateTime();
        }

        return new StoredImageDTO(
            storageName: $this->getStorageName(),
            publicId: is_string($result['public_id'] ?? null) ? $result['public_id'] : '',
            url: is_string($result['secure_url'] ?? null) ? $result['secure_url'] : '',
            width: is_int($result['width'] ?? null) ? $result['width'] : null,
            height: is_int($result['height'] ?? null) ? $result['height'] : null,
            format: is_string($result['format'] ?? null) ? $result['format'] : null,
            createdAt: $createdAt->format(\DateTimeInterface::ATOM),
        );
    }
}
