<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\ApiRequestException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class ApiClient
{
    public function __construct(
        private readonly HttpClientInterface $client,
        #[Autowire('%app.api.base_url%')]
        private readonly string $baseUrl
    ) {
    }

    /**
     * @throws ApiRequestException
     */
    public function get(string $endpoint, array $options = []): array
    {
        try {
            $response = $this->client->request(
                'GET',
                $this->buildUrl($endpoint),
                $options
            );

            $this->validateResponse($response);

            return $response->toArray();
        } catch (ExceptionInterface $e) {
            throw new ApiRequestException(
                'Failed to fetch data from API.',
                Response::HTTP_INTERNAL_SERVER_ERROR,
                $e
            );
        }
    }

    private function buildUrl(string $endpoint): string
    {
        return rtrim($this->baseUrl, '/') . '/' . ltrim($endpoint, '/');
    }

    /**
     * @throws TransportExceptionInterface
     */
    private function validateResponse(ResponseInterface $response): void
    {
        if ($response->getStatusCode() !== Response::HTTP_OK) {
            throw new ApiRequestException(
                sprintf('API request failed with status code %d', $response->getStatusCode()),
                $response->getStatusCode()
            );
        }
    }
}
