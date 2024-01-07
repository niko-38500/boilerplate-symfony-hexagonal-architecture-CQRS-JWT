<?php

declare(strict_types=1);

namespace App\User\Domain\Command;

use App\FrameworkInfrastructure\Domain\Command\CommandInterface;

readonly class RemoveTemporaryTokenCommand implements CommandInterface
{
    public function __construct(
        public string $token,
    ) {}
}
