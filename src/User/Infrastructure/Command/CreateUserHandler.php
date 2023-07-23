<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Command;

use App\FrameworkInfrastructure\Domain\Command\CommandHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;

class CreateUserHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {}

    public function __invoke(CreateUserCommand $command): void
    {
        $this->entityManager->persist($command->user);
        $this->entityManager->flush();
    }
}