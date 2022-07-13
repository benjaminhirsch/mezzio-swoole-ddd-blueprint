<?php

declare(strict_types=1);

namespace App\Factory\Logger;

use Interop\Container\ContainerInterface;
use Laminas\Stratigility\Middleware\ErrorHandler;

final class LoggingErrorListenerDelegatorFactory
{
    /** @param mixed[]|null $options */
    public function __invoke(ContainerInterface $container, string $name, callable $callback, array|null $options = null): ErrorHandler
    {
        $handler = $callback();
        $handler->attachListener($container->get('logger-listener'));

        return $handler;
    }
}
