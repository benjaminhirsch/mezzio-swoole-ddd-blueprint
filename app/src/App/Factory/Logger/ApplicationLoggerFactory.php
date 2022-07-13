<?php

declare(strict_types=1);

namespace App\Factory\Logger;

use Monolog\Logger;
use Psr\Container\ContainerInterface;

use function assert;
use function is_array;

final class ApplicationLoggerFactory
{
    public function __invoke(ContainerInterface $container): Logger
    {
        $config = $container->get('config');
        assert(is_array($config));

        return LoggerFactory::createFromConfig(
            $container,
            $config['application-logger'] ?? null,
        );
    }
}
