<?php

declare(strict_types=1);

namespace App\FrameworkInfrastructure\Domain\Command;

interface CommandBus
{
    public function dispatch(CommandInterface $command): void;
}