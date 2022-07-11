<?php

declare(strict_types=1);

namespace App\Factory\Logger;

use Interop\Container\ContainerInterface;
use Laminas\Stratigility\Middleware\ErrorHandler;

use function assert;
use function is_array;
use function is_callable;

final class LoggingErrorListenerDelegatorFactory
{
    /**
     * @param mixed[]|null $options
     */
    public function __invoke(
        ContainerInterface $container,
        string $name,
        callable $callback,
        ?array $options = null
    ): ErrorHandler {
        $config = $container->get('config');
        assert(is_array($config) && isset($config['logger-listener']) && is_callable($config['logger-listener']));
        $handler = $callback();
        $handler->attachListener($config['logger-listener']);

        return $handler;
    }
}
