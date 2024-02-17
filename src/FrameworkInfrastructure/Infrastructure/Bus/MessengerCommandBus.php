<?php

declare(strict_types=1);

namespace App\FrameworkInfrastructure\Infrastructure\Bus;

use App\FrameworkInfrastructure\Domain\Command\CommandBus;
use App\FrameworkInfrastructure\Domain\Command\CommandInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class MessengerCommandBus implements CommandBus
{
    public function __construct(
        private MessageBusInterface $commandBus,
    ) {}

    public function dispatch(CommandInterface $command): void
    {
        $this->commandBus->dispatch($command);
    }
}
