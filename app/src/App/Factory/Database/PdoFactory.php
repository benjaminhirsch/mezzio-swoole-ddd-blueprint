<?php

declare(strict_types=1);

namespace App\Factory\Database;

use App\Domain\Exception\MissingConfiguration;
use App\Domain\Exception\Runtime;
use Psr\Container\ContainerInterface;
use Swoole\Coroutine\PostgreSQL;

use function assert;
use function is_array;
use function sprintf;

final class PdoFactory
{
    public function __invoke(ContainerInterface $container): PostgreSQL
    {
        if (! $container->has('config')) {
            throw Runtime::create('Missing application config!');
        }

        $config = $container->get('config');
        assert(is_array($config));
        $defaultConnectionConfig = $config['database']['default'] ?? null;
        if ($defaultConnectionConfig === null) {
            throw MissingConfiguration::create('Missing default connection configuration entry in config');
        }

        if (! isset($defaultConnectionConfig['adapter'])) {
            throw MissingConfiguration::create('Missing adapter in configuration');
        }

        if (! isset($defaultConnectionConfig['dbname'])) {
            throw MissingConfiguration::create('Missing dbname in configuration');
        }

        if (! isset($defaultConnectionConfig['host'])) {
            throw MissingConfiguration::create('Missing host in configuration');
        }

        if (! isset($defaultConnectionConfig['port'])) {
            throw MissingConfiguration::create('Missing port in configuration');
        }

        if (! isset($defaultConnectionConfig['user'])) {
            throw MissingConfiguration::create('Missing user in configuration');
        }

        if (! isset($defaultConnectionConfig['pass'])) {
            throw MissingConfiguration::create('Missing password (pass) in configuration');
        }

            $pg = new PostgreSQL();
            $pg->connect(sprintf(
                'host=%s;port=%d;dbname=%s;user=%s;password=%s',
                $defaultConnectionConfig['host'],
                $defaultConnectionConfig['port'],
                $defaultConnectionConfig['dbname'],
                $defaultConnectionConfig['user'],
                $defaultConnectionConfig['pass'],
            ));

            return $pg;
    }
}
