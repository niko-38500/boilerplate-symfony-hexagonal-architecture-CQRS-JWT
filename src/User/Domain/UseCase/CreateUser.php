<?php

declare(strict_types=1);

namespace App\User\Domain\UseCase;

use App\FrameworkInfrastructure\Domain\Command\CommandBus;
use App\User\Domain\Command\CreateUserCommand;
use App\User\Domain\Entity\User;
use App\User\Presentation\DTO\UserInputDTO;

class CreateUser
{
    public function __construct(
        private readonly CommandBus $commandDispatcher
    ) {}

    public function execute(UserInputDTO $userDTO): void
    {
        $user = new User($userDTO->username, $userDTO->email, $userDTO->plainPassword);
        $command = new CreateUserCommand($user);

        $this->commandDispatcher->dispatch($command);
    }
}