<?php

declare(strict_types=1);

namespace App\Factory\Reponse;

use App\Infrastructure\Response\HtmlResponseRenderer;
use Psr\Container\ContainerInterface;

use function assert;
use function is_callable;

final class HtmlResponseRendererFactory
{
    public function __invoke(ContainerInterface $container): HtmlResponseRenderer
    {
        $rendererFactory = $container->get('html-renderer');
        assert(is_callable($rendererFactory));

        return new HtmlResponseRenderer($rendererFactory);
    }
}
