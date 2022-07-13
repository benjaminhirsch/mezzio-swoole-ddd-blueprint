<?php

declare(strict_types=1);

namespace App\Infrastructure\Logger;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Throwable;

final class LoggingErrorListener
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function __invoke(
        Throwable $throwable,
        ServerRequestInterface $serverRequest,
        ResponseInterface $response,
    ): void {
        $this->logger->error(
            $throwable::class . ': ' . $throwable->getMessage(),
            [
                'file' => $throwable->getFile(),
                'line' => $throwable->getLine(),
                'trace' => $throwable->getTraceAsString(),
                'error_code' => $throwable->getCode(),
                'previous_exception' => $throwable->getPrevious() !== null
                    ? (string) $throwable->getPrevious()
                    : null,
            ],
        );
    }
}
