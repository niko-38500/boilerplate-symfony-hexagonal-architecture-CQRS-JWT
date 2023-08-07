<?php

declare(strict_types=1);

namespace App\User\Domain\Command;

use App\FrameworkInfrastructure\Domain\Command\CommandInterface;

class RemoveTemporaryTokenCommand implements CommandInterface
{
    public function __construct(
        public readonly string $token,
    ) {
    }
}
