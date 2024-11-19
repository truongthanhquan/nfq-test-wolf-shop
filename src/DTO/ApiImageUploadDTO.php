<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class ApiImageUploadDTO
{
    #[Assert\NotNull(message: 'Please upload an image file.')]
    #[Assert\Image(
        maxSize: '10M',
        mimeTypes: ['image/jpeg', 'image/png', 'image/gif'],
        mimeTypesMessage: 'Please upload a valid image file (JPEG, PNG, or GIF).'
    )]
    private ?UploadedFile $image;

    public function getImage(): ?UploadedFile
    {
        return $this->image;
    }

    public function setImage(?UploadedFile $image): self
    {
        $this->image = $image;
        return $this;
    }
}
