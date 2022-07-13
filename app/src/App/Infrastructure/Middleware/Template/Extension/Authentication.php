<?php

declare(strict_types=1);

namespace App\Infrastructure\Middleware\Template\Extension;

use Mezzio\Authentication\UserInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

use function assert;

final class Authentication extends AbstractExtension implements MiddlewareInterface
{
    private UserInterface|null $user = null;

    /** @return TwigFunction[] */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('currentUser', [$this, 'currentUser']),
            new TwigFunction('isLoggedIn', [$this, 'isLoggedIn']),
        ];
    }

    public function currentUser(): UserInterface|null
    {
        return $this->user;
    }

    public function isLoggedIn(): bool
    {
        return $this->user !== null;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $userAttribute = $request->getAttribute(UserInterface::class);
        assert($userAttribute === null || $userAttribute instanceof UserInterface);
        $this->user = $userAttribute;

        return $handler->handle($request);
    }
}
