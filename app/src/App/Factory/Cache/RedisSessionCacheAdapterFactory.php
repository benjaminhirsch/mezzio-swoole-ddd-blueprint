<?php

declare(strict_types=1);

namespace App\Factory\Cache;

use App\Domain\Exception\MissingService;
use Psr\Container\ContainerInterface;
use Redis;
use Symfony\Component\Cache\Adapter\RedisAdapter;

use function assert;

final class RedisSessionCacheAdapterFactory
{
    public function __invoke(ContainerInterface $container): RedisAdapter
    {
        if (! $container->has(Redis::class)) {
            throw MissingService::create('Missing Redis service in container');
        }

        $redis = $container->get(Redis::class);
        assert($redis instanceof Redis);

        return new RedisAdapter($redis, 'sessions', 0);
    }
}
