<?php

declare(strict_types=1);

namespace App\Factory\Cache;

use App\Domain\Exception\MissingService;
use PDO;
use Psr\Container\ContainerInterface;
use Symfony\Component\Cache\Adapter\PdoAdapter;

use function assert;

final class PdoSessionCacheAdapterFactory
{
    public function __invoke(ContainerInterface $container): PdoAdapter
    {
        if (! $container->has(PDO::class)) {
            throw MissingService::create('Missing PDO service in container');
        }

        $pdo = $container->get(PDO::class);
        assert($pdo instanceof PDO);

        return new PdoAdapter($pdo, '', 0, ['db_table' => 'user_sessions']);
    }
}
