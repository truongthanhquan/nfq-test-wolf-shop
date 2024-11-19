<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\ApiImageUploadDTO;
use App\Entity\ItemEntity;
use App\Service\ItemService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/items')]
class ItemController extends AbstractController
{
    public function __construct(
        private readonly ItemService $itemService,
        private readonly ValidatorInterface $validator
    ) {
    }

    #[Route('/{id}/image', methods: ['POST'])]
    public function uploadImage(Request $request, ItemEntity $itemEntity): JsonResponse
    {
        $imageFile = $request->files->get('image');
        if (! $imageFile instanceof UploadedFile) {
            throw new BadRequestHttpException('Image file is not valid');
        }

        $imageUploadDto = new ApiImageUploadDTO();
        $imageUploadDto->setImage($imageFile);
        $validationErrors = $this->validator->validate($imageUploadDto);
        if (count($validationErrors) > 0) {
            throw new BadRequestHttpException((string) $validationErrors);
        }

        try {
            $this->itemService->uploadImage($itemEntity, $imageFile);
        } catch (FileException $e) {
            throw new BadRequestHttpException('Error uploading image: ' . $e->getMessage());
        }

        return $this->json([
            'success' => true,
            'data' => [
                'imgUrl' => $itemEntity->getImageUrl(),
            ],
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id}/image', methods: ['DELETE'])]
    public function removeImage(ItemEntity $itemEntity): JsonResponse
    {
        $this->itemService->removeImage($itemEntity);

        return $this->json([
            'success' => true,
            'message' => 'Image removed successfully',
        ]);
    }
}
