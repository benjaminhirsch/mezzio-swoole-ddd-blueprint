<?php

declare(strict_types=1);

namespace App\Factory\Logger;

use App\Domain\Exception\MissingConfiguration;
use App\Infrastructure\Logger\LoggingErrorListener;
use DateTimeZone;
use Monolog\Logger;
use Psr\Container\ContainerInterface;

final class LoggerListenerFactory
{
    public function __invoke(ContainerInterface $container): LoggingErrorListener
    {
        $loggerConfig = $container->get('config')['exception-logger'] ?? null;

        if ($loggerConfig === null) {
            throw new MissingConfiguration(
                'Logger values are missing in config'
            );
        }

        if (! isset($loggerConfig['channel'])) {
            throw new MissingConfiguration(
                'The channel name for the loggerConfig is missing in the configuration'
            );
        }

        if (! isset($loggerConfig['handlers'])) {
            throw new MissingConfiguration(
                'Missing handlers configuration for the loggerConfig'
            );
        }

        if (! isset($loggerConfig['processors'])) {
            throw new MissingConfiguration(
                'Missing processors configuration for the loggerConfig'
            );
        }

        if (! isset($loggerConfig['timezone'])) {
            throw new MissingConfiguration(
                'Missing timezone configuration for the loggerConfig'
            );
        }

        $logger = new Logger($loggerConfig['channel']);
        $logger->setTimezone(new DateTimeZone($loggerConfig['timezone']));

        foreach ($loggerConfig['handlers'] as $processor) {
            $logger->pushHandler($container->get($processor));
        }

        foreach ($loggerConfig['processors'] as $processor) {
            $logger->pushProcessor($container->get($processor));
        }

        return new LoggingErrorListener($logger);
    }
}
