<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\ApiItemDTO;
use App\Factory\ApiItemDTOFactory;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class ApiItemService
{
    public function __construct(
        private readonly ApiClient $apiClient,
        private readonly ApiItemDTOFactory $factory,
        #[Autowire('%app.api.endpoints.items%')]
        private readonly string $itemsEndpoint,
    ) {
    }

    /**
     * @return ApiItemDTO[]
     */
    public function fetchItems(): array
    {
        $items = $this->apiClient->get($this->itemsEndpoint);
        return array_map(
            fn (array $item) => $this->factory->create($item),
            $items
        );
    }
}