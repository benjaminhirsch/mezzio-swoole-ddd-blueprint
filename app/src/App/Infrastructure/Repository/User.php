<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Exception\ModelHydrationFailed;
use App\Domain\Exception\UserNotFound;
use App\Domain\Repository\User as UserInterface;
use DateTimeImmutable;
use PDO;
use Ramsey\Uuid\Uuid;
use Throwable;

use function assert;
use function is_array;
use function sprintf;

final class User implements UserInterface
{
    public function __construct(private readonly PDO $connection)
    {
    }

    public function getById(string $id): \App\Domain\Model\User
    {
        $stmt = $this->connection->prepare('select * from users where id = ?');
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

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
            return new \App\Domain\Model\User(
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
}
