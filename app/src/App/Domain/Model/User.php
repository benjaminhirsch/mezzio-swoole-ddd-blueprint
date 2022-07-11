<?php

declare(strict_types=1);

namespace App\Domain\Model;

use DateTimeInterface;

final class User
{
    public function __construct(
        public readonly Identifier $id,
        public readonly string $email,
        public readonly string $password,
        public readonly DateTimeInterface|null $lastLogin,
        public readonly DateTimeInterface $createdAt,
        public readonly DateTimeInterface $updatedAt,
    ) {
    }
}
