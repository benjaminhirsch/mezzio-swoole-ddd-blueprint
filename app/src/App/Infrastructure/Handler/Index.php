<?php

declare(strict_types=1);

namespace App\Infrastructure\Handler;

use App\Domain\Repository\User as UserRepository;
use App\Infrastructure\Response\ResponseRenderer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class Index implements RequestHandlerInterface
{
    public function __construct(
        private readonly ResponseRenderer $renderer,
        private readonly UserRepository $user,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->renderer->render('app::index', $request, [
            'foo' => $this->user->getById('6b676d56-c85b-47b3-bbb6-1e3ff07c1643'),
        ]);
    }
}
