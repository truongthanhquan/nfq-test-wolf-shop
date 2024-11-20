<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\DTO\ApiItemDTO;
use App\Factory\ApiItemDTOFactory;
use App\Service\ApiClient;
use App\Service\ApiItemService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ApiItemServiceTest extends TestCase
{
    private MockObject $apiClientMock;

    private MockObject $factoryMock;

    private ApiItemService $service;

    protected function setUp(): void
    {
        // Mock the ApiClient and ApiItemDTOFactory
        $this->apiClientMock = $this->createMock(ApiClient::class);
        $this->factoryMock = $this->createMock(ApiItemDTOFactory::class);

        // Mock the endpoint (you can hardcode this or get it from the config)
        $itemsEndpoint = 'https://api.example.com/objects';

        // Instantiate the service with the mocked dependencies
        $this->service = new ApiItemService(
            $this->apiClientMock,
            $this->factoryMock,
            $itemsEndpoint
        );
    }

    public function testFetchItemsSuccessfully(): void
    {
        // Example mock data to simulate the response from the API client
        $apiResponse = [
            [
                'name' => 'Item 1',
                'price' => 100,
            ],
            [
                'name' => 'Item 2',
                'price' => 200,
            ],
        ];

        // Mock the ApiClient to return the above mock data
        $this->apiClientMock
            ->expects($this->once())
            ->method('get')
            ->with('https://api.example.com/objects')
            ->willReturn($apiResponse);

        // Mock the ApiItemDTOFactory to return DTOs based on the API response
        $itemDTO1 = $this->createMock(ApiItemDTO::class);
        $itemDTO2 = $this->createMock(ApiItemDTO::class);

        // Define the factory behavior: when `create()` is called, return the mock DTOs
        $this->factoryMock
            ->method('create')
            ->willReturnCallback(function ($input) use ($apiResponse, $itemDTO1, $itemDTO2) {
                if ($input === $apiResponse[0]) {
                    return $itemDTO1;
                }
                return $itemDTO2;
            })
        ;

        // Call the method we are testing
        $items = $this->service->fetchItems();

        // Assert that the returned value is an array
        $this->assertIsArray($items);

        // Assert that the array contains two items (ApiItemDTO objects)
        $this->assertCount(2, $items);

        // Assert that the returned items are the expected DTOs
        $this->assertSame($itemDTO1, $items[0]);
        $this->assertSame($itemDTO2, $items[1]);
    }

    public function testFetchItemsReturnsEmptyArrayWhenNoItems(): void
    {
        // Mock the ApiClient to return an empty array (no items)
        $this->apiClientMock
            ->expects($this->once())
            ->method('get')
            ->with('https://api.example.com/objects')
            ->willReturn([]);

        // Call the method we are testing
        $items = $this->service->fetchItems();

        // Assert that the returned value is an empty array
        $this->assertIsArray($items);
        $this->assertEmpty($items);
    }

    public function testFetchItemsHandlesApiClientError(): void
    {
        // Mock the ApiClient to throw an exception (simulate an API error)
        $this->apiClientMock
            ->expects($this->once())
            ->method('get')
            ->with('https://api.example.com/objects')
            ->willThrowException(new \Exception('API request failed'));

        // Call the method and expect an exception
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('API request failed');

        // Call the method we are testing
        $this->service->fetchItems();
    }
}
