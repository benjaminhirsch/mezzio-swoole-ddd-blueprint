<?php

declare(strict_types=1);

namespace App\Infrastructure\Response;

use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class HtmlResponseRenderer implements ResponseRenderer
{
    /** @var callable */
    private $rendererFactory;

    public function __construct(callable $rendererFactory)
    {
        $this->rendererFactory = $rendererFactory;
    }

    /**
     * @param mixed[] $params
     */
    public function render(string $name, ServerRequestInterface $request, array $params = [], int $statusCode = 200): ResponseInterface
    {
        $renderer = ($this->rendererFactory)();

        return new HtmlResponse($renderer->render($name, $params), $statusCode);
    }
}
