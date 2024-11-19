<?php

declare(strict_types=1);

namespace App\Service\ImageUpload;

use App\DTO\StoredImageDTO;
use Cloudinary\Cloudinary;
use Symfony\Component\HttpFoundation\File\UploadedFile;

readonly class CloudinaryImageUploadService implements ImageUploadServiceInterface
{
    public function __construct(
        private Cloudinary $cloudinary
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

            return new StoredImageDTO(
                storageName: $this->getStorageName(),
                publicId: is_string($result['public_id'] ?? null) ? $result['public_id'] : '',
                url: is_string($result['secure_url'] ?? null) ? $result['secure_url'] : '',
                width: is_int($result['width'] ?? null) ? $result['width'] : null,
                height: is_int($result['height'] ?? null) ? $result['height'] : null,
                format: is_string($result['format'] ?? null) ? $result['format'] : null,
                createdAt: new \DateTimeImmutable(is_string($result['created_at'] ?? null) ? $result['created_at'] : 'now'),
            );
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to upload image: ' . $e->getMessage());
        }
    }

    public function delete(string $publicId): bool
    {
        try {
            $this->cloudinary->uploadApi()->destroy($publicId);
            return true;
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to delete image: ' . $e->getMessage());
        }
    }
}
