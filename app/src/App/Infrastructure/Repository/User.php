<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Application\Repository\User as UserInterface;
use App\Domain\Exception\ModelHydrationFailed;
use App\Domain\Exception\Repository\UserCreationFailed;
use App\Domain\Exception\UserNotFound;
use DateTimeImmutable;
use Ramsey\Uuid\Uuid;
use Swoole\Coroutine\PostgreSQL;
use Throwable;

use function assert;
use function is_array;
use function sprintf;

use const OPENSWOOLE_PGRES_COMMAND_OK;

final class User implements UserInterface
{
    public function __construct(private readonly PostgreSQL $connection)
    {
    }

    public function getById(string $id): \App\Domain\Entity\User
    {
        $this->connection->prepare(__METHOD__, 'SELECT * FROM users WHERE id = $1');
        $res    = $this->connection->execute(__METHOD__, [$id]);
        $result = $this->connection->fetchAssoc($res);

        if ($result === false) {
            throw UserNotFound::create(sprintf('User with `Id` %s not found', $id));
        }

        assert(is_array($result) && isset(
            $result['id'],
            $result['email'],
            $result['password'],
            $result['createdAt'],
            $result['updatedAt'],
        ));

        try {
            return new \App\Domain\Entity\User(
                Uuid::fromString($result['id']),
                $result['email'],
                $result['password'],
                $result['lastLogin'] !== null ? new DateTimeImmutable($result['lastLogin']) : null,
                new DateTimeImmutable($result['createdAt']),
                new DateTimeImmutable($result['updatedAt']),
            );
        } catch (Throwable $e) {
            throw ModelHydrationFailed::create(
                $e->getMessage(),
                $e->getCode(),
                $e->getPrevious(),
            );
        }
    }

    public function create(\App\Domain\Entity\User $user): void
    {
        $this->connection->query('START TRANSACTION');
        $this->connection->prepare(__METHOD__, 'INSERT INTO USERS ("id", "email", "password") VALUES ($1, $2, $3)');
        $result = $this->connection->execute(__METHOD__, [
            $user->id->toString(),
            $user->email,
            $user->password,
        ]);

        if (! $result || $this->connection->resultStatus !== OPENSWOOLE_PGRES_COMMAND_OK) {
            $this->connection->query('ROLLBACK');
            $this->connection->reset();

            throw UserCreationFailed::create('Unable to create user', $this->connection->errCode);
        }

        $this->connection->query('COMMIT');
    }
}
