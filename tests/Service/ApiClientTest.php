<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Exception\ApiRequestException;
use App\Service\ApiClient;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class ApiClientTest extends TestCase
{
    public function testCaseSuccessfulRequest(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);
        $response->method('toArray')->willReturn([[
            'name' => 'Google Pixel 6 Pro',
        ]]);

        $baseUrl = 'http://api.test';
        $httpClient->method('request')
            ->with('GET', $baseUrl . '/objects', [])
            ->willReturn($response);

        $apiClient = new ApiClient($httpClient, $baseUrl);

        $result = $apiClient->get('objects');
        $this->assertEquals([[
            'name' => 'Google Pixel 6 Pro',
        ]], $result);
    }

    public function testCaseFailedRequest(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $response->method('getStatusCode')->willReturn(500);

        $httpClient->method('request')->willReturn($response);

        $apiClient = new ApiClient($httpClient, 'http://api.test');

        $this->expectException(ApiRequestException::class);
        $apiClient->get('objects');
    }
}
