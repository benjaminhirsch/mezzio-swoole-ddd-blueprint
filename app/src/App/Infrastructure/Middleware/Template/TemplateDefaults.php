<?php

declare(strict_types=1);

namespace App\Infrastructure\Middleware\Template;

use Laminas\InputFilter\InputFilterInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class TemplateDefaults implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $handler->handle($request->withAttribute(self::class, [
            'inputFilter' => $request->getAttribute(InputFilterInterface::class),
        ]));
    }
}
