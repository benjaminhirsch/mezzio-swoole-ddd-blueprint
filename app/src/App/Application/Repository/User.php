<?php

declare(strict_types=1);

namespace App\Application\Repository;

interface User
{
    public function getById(string $id): \App\Domain\Entity\User;

    public function create(\App\Domain\Entity\User $user): void;
}
