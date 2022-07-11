<?php

declare(strict_types=1);

namespace App\Factory\Logger;

use App\Domain\Exception\MissingConfiguration;
use DateTimeZone;
use Monolog\Handler\HandlerInterface;
use Monolog\Logger;
use Monolog\Processor\ProcessorInterface;
use Psr\Container\ContainerInterface;

use function assert;
use function is_iterable;
use function is_string;

final class LoggerFactory
{
    /**
     * @param array<mixed>|null $config
     */
    public static function createFromConfig(ContainerInterface $container, ?array $config): Logger
    {
        if ($config === null) {
            throw MissingConfiguration::create(
                'Logger values are missing in config'
            );
        }

        if (! isset($config['channel'])) {
            throw MissingConfiguration::create(
                'The channel name for the loggerConfig is missing in the configuration'
            );
        }

        if (! isset($config['handlers'])) {
            throw MissingConfiguration::create(
                'Missing handlers configuration for the loggerConfig'
            );
        }

        if (! isset($config['processors'])) {
            throw MissingConfiguration::create(
                'Missing processors configuration for the loggerConfig'
            );
        }

        if (! isset($config['timezone'])) {
            throw MissingConfiguration::create(
                'Missing timezone configuration for the loggerConfig'
            );
        }

        assert(is_string($config['channel']));
        assert(is_string($config['timezone']));
        assert(is_iterable($config['handlers']));
        assert(is_iterable($config['processors']));

        $logger = new Logger($config['channel']);
        $logger->setTimezone(new DateTimeZone($config['timezone']));

        foreach ($config['handlers'] as $processor) {
            $resolvedProcessor = $container->get($processor);
            assert($resolvedProcessor instanceof HandlerInterface);
            $logger->pushHandler($resolvedProcessor);
        }

        foreach ($config['processors'] as $processor) {
            $resolvedProcessor = $container->get($processor);
            assert($resolvedProcessor instanceof ProcessorInterface);
            $logger->pushProcessor($resolvedProcessor);
        }

        return $logger;
    }
}
