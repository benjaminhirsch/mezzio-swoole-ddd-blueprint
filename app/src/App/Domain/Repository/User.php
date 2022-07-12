<?php

declare(strict_types=1);

namespace App\Domain\Repository;

interface User
{
    public function getById(string $id): \App\Domain\Model\User;
}
