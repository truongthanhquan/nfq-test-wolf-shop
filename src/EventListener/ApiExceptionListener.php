<?php

declare(strict_types=1);

namespace App\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

#[AsEventListener(event: 'kernel.exception', priority: -1)]
readonly class ApiExceptionListener
{
    public function __construct(
        private LoggerInterface $logger
    ) {
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $request = $event->getRequest();
        $pathInfo = $request->getPathInfo();
        if (! str_starts_with($pathInfo, '/api/')) {
            return;
        }

        $exception = $event->getThrowable();

        $statusCode = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : 500;
        $message = $exception->getMessage();

        $this->logger->error('API Exception', [
            'code' => $statusCode,
            'message' => $message,
            'request_method' => $request->getMethod(),
            'request_uri' => $request->getRequestUri(),
            'client_ip' => $request->getClientIp(),
            'exception' => $exception, // For debugging
        ]);

        $response = new JsonResponse([
            'success' => 0,
            'message' => $message,
        ], $statusCode);


        $event->setResponse($response);
    }
}
