<?php

declare(strict_types=1);

namespace App\Infrastructure\Handler;

use App\Infrastructure\Response\ResponseRenderer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class Login implements RequestHandlerInterface
{
    public function __construct(private readonly ResponseRenderer $renderer)
    {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->renderer->render('app::login', $request);
    }
}
