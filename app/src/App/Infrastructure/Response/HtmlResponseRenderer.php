<?php

declare(strict_types=1);

namespace App\Infrastructure\Response;

use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class HtmlResponseRenderer implements ResponseRenderer
{
    private TemplateRendererInterface $templateRenderer;

    public function __construct(TemplateRendererInterface $templateRenderer)
    {
        $this->templateRenderer = $templateRenderer;
    }

    /**
     * @param mixed[] $params
     */
    public function render(string $name, ServerRequestInterface $request, array $params = [], int $statusCode = 200): ResponseInterface
    {
        return new HtmlResponse($this->templateRenderer->render($name, $params), $statusCode);
    }
}
