<?php

declare(strict_types=1);

namespace App\Application\Event;

use App\Domain\Entity\User;

final class UserCreated
{
    private function __construct(public readonly User $user)
    {
    }

    public static function create(User $user): self
    {
        return new self($user);
    }
}
