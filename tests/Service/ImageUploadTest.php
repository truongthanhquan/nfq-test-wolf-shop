<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Service\ImageUpload\CloudinaryImageUploadService;
use Cloudinary\Api\ApiResponse;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Cloudinary;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageUploadTest extends TestCase
{
    private MockObject $uploadApi;

    private CloudinaryImageUploadService $cloudinaryImageUploadService;

    protected function setUp(): void
    {
        $cloudinary = $this->createMock(Cloudinary::class);
        $this->uploadApi = $this->createMock(UploadApi::class);
        $cloudinary->method('uploadApi')->willReturn($this->uploadApi);
        $logger = $this->createMock(LoggerInterface::class);
        $this->cloudinaryImageUploadService = new CloudinaryImageUploadService($cloudinary, $logger);
    }

    public function testCloudinaryImageUploadServiceUploadSuccess(): void
    {
        $this->uploadApi->method('upload')->willReturn(new ApiResponse([
            'public_id' => 'test_id',
            'secure_url' => 'https://test.com/image.jpg',
            'width' => 100,
            'height' => 200,
            'format' => 'jpg',
            'created_at' => '2017-06-23T13:59:18Z',
        ], []));
        $uploadedFile = $this->createMock(UploadedFile::class);
        $uploadedFile->method('getRealPath')->willReturn('/tmp/test.jpg');

        $result = $this->cloudinaryImageUploadService->upload($uploadedFile);

        $this->assertEquals('cloudinary', $result->storageName);
        $this->assertEquals('test_id', $result->publicId);
        $this->assertEquals('https://test.com/image.jpg', $result->url);
        $this->assertEquals(
            (new \DateTime('2017-06-23T13:59:18Z'))->format(\DateTimeInterface::ATOM),
            $result->createdAt
        );
    }

    public function testCloudinaryImageUploadServiceUploadException(): void
    {
        // Mock Cloudinary to throw an exception
        $this->uploadApi->method('upload')->willThrowException(new \Exception('Cloudinary error'));

        // Expect exception and verify message
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Failed to upload image: Cloudinary error');

        $uploadedFile = $this->createMock(UploadedFile::class);
        $uploadedFile->method('getRealPath')->willReturn('/tmp/test.jpg');
        $this->cloudinaryImageUploadService->upload($uploadedFile);
    }

    public function testCloudinaryImageUploadServiceDeleteSuccess(): void
    {
        $this->uploadApi->method('destroy')->willReturn(true);
        $result = $this->cloudinaryImageUploadService->delete('sample_public_id');
        $this->assertTrue($result);
    }

    public function testCloudinaryImageUploadServiceDeleteException(): void
    {
        // Mock Cloudinary to throw an exception during deletion
        $this->uploadApi->method('destroy')->willThrowException(new \Exception('Cloudinary delete error'));

        // Expect exception and verify message
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Failed to delete image: Cloudinary delete error');

        // Call the delete method
        $this->cloudinaryImageUploadService->delete('sample_public_id');
    }
}
