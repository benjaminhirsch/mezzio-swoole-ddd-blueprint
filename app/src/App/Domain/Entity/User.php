<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use DateTimeInterface;
use Ramsey\Uuid\UuidInterface;

final class User
{
    public function __construct(
        public readonly UuidInterface $id,
        public readonly string $email,
        public readonly string $password,
        public readonly DateTimeInterface|null $lastLogin,
        public readonly DateTimeInterface $createdAt,
        public readonly DateTimeInterface $updatedAt,
    ) {
    }
}
