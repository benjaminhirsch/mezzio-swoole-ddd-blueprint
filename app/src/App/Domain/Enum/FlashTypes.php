<?php

declare(strict_types=1);

namespace App\Domain\Enum;

enum FlashTypes: string
{
    case SUCCESS = 'success';
    case ERROR   = 'error';
    case WARNING = 'warning';
    case INFO    = 'info';
}
