<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Notifier;

use App\User\Domain\Notifier\NotifierInterface;
use Symfony\Component\Notifier\NotifierInterface as SymfonyNotifierInterface;

class NotifierAdapter implements NotifierInterface
{
    public function __construct(
        private readonly SymfonyNotifierInterface $notifier
    ) {
    }
}
