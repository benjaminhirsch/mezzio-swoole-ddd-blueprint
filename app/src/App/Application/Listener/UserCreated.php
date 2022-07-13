<?php

declare(strict_types=1);

namespace App\Application\Listener;

use App\Application\Event\UserCreated as Event;
use Psr\Log\LoggerInterface;

use function sprintf;

final class UserCreated
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public function __invoke(Event $event): void
    {
        $this->logger->info(sprintf('New user `%s` registered', $event->user->id));
    }
}
