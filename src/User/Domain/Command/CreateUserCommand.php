<?php

declare(strict_types=1);

namespace App\User\Domain\Command;

use App\FrameworkInfrastructure\Domain\Command\CommandInterface;
use App\User\Domain\Entity\User;

class CreateUserCommand implements CommandInterface
{
    public function __construct(
        public User $user
    ) {}
}
