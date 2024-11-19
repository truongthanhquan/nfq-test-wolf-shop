<?php

declare(strict_types=1);

namespace App\Service\ImageUpload;

use App\DTO\StoredImageDTO;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface ImageUploadServiceInterface
{
    public function getStorageName(): string;

    public function upload(UploadedFile $file): StoredImageDTO;

    public function delete(string $publicId): bool;
}
