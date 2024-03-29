<?php

declare(strict_types=1);

namespace App\Factory\Reponse;

use Mezzio\Template\TemplateRendererInterface;
use Mezzio\Twig\TwigRendererFactory;
use Psr\Container\ContainerInterface;

final class TemplateRendererInterfaceFactory
{
    public function __invoke(ContainerInterface $container): callable
    {
        return static fn (): TemplateRendererInterface => (new TwigRendererFactory())($container);
    }
}
