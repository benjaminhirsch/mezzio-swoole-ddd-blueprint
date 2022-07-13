<?php

declare(strict_types=1);

namespace App\Factory\Repository;

use App\Infrastructure\Repository\User;
use Psr\Container\ContainerInterface;
use Swoole\Coroutine\PostgreSQL;

use function assert;

final class UserFactory
{
    public function __invoke(ContainerInterface $container): User
    {
        $connection = $container->get(PostgreSQL::class);
        assert($connection instanceof PostgreSQL);

        return new User($connection);
    }
}
