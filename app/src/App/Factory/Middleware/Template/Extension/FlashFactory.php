<?php

declare(strict_types=1);

namespace App\Factory\Middleware\Template\Extension;

use App\Infrastructure\Middleware\Template\Extension\Flash;
use App\Infrastructure\Response\HtmlRenderer;
use Psr\Container\ContainerInterface;

use function assert;
use function is_callable;

final class FlashFactory
{
    public function __invoke(ContainerInterface $container): Flash
    {
        $rendererFactory = $container->get(HtmlRenderer::class);
        assert(is_callable($rendererFactory));

        return new Flash($rendererFactory);
    }
}
