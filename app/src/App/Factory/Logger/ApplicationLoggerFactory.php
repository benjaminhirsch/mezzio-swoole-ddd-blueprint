<?php

declare(strict_types=1);

namespace App\Factory\Logger;

use Monolog\Logger;
use Psr\Container\ContainerInterface;

final class ApplicationLoggerFactory
{
    public function __invoke(ContainerInterface $container): Logger
    {
        return LoggerFactory::createFromConfig(
            $container,
            $container->get('config')['application-logger'] ?? null
        );
    }
}
