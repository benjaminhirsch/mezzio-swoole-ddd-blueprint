<?php

declare(strict_types=1);

namespace App\Infrastructure\Response;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface ResponseRenderer
{
    /**
     * @param mixed[] $params
     */
    public function render(string $name, ServerRequestInterface $request, array $params = [], int $statusCode = 200): ResponseInterface;
}
