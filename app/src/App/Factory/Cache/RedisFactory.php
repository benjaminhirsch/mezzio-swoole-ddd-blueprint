<?php

declare(strict_types=1);

namespace App\Factory\Cache;

use App\Domain\Exception\MissingConfiguration;
use App\Domain\Exception\Runtime;
use Psr\Container\ContainerInterface;
use Redis;
use RedisException;

use function assert;
use function is_array;

final class RedisFactory
{
    public function __invoke(ContainerInterface $container): Redis
    {
        if (! $container->has('config')) {
            throw Runtime::create('Missing application config!');
        }

        $config = $container->get('config');
        assert(is_array($config));
        $defaultConnectionConfig = $config['redis']['default'] ?? null;

        if (! isset($defaultConnectionConfig['host'])) {
            throw MissingConfiguration::create('Missing host in configuration');
        }

        if (! isset($defaultConnectionConfig['port'])) {
            throw MissingConfiguration::create('Missing port in configuration');
        }

        if (! isset($defaultConnectionConfig['timeout'])) {
            throw MissingConfiguration::create('Missing timeout in configuration');
        }

        if (! isset($defaultConnectionConfig['retryInterval'])) {
            throw MissingConfiguration::create('Missing retryInterval in configuration');
        }

        if (! isset($defaultConnectionConfig['readTimeout'])) {
            throw MissingConfiguration::create('Missing readTimeout in configuration');
        }

        try {
            $redis = new Redis();
            if (! $redis->isConnected()) {
                $redis->connect(
                    $defaultConnectionConfig['host'],
                    $defaultConnectionConfig['port'],
                    $defaultConnectionConfig['timeout'],
                    $defaultConnectionConfig['persistentId'] ?? null,
                    $defaultConnectionConfig['retryInterval'],
                    $defaultConnectionConfig['readTimeout'],
                    $defaultConnectionConfig['context'] ?? null,
                );
            }
        } catch (RedisException $e) {
            throw Runtime::create('Unable to create redis service', $e->getCode(), $e);
        }

        return $redis;
    }
}
