<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Command;

use App\FrameworkInfrastructure\Domain\Command\CommandHandlerInterface;
use App\User\Domain\Command\CreateUserCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateUserHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {}

    public function __invoke(CreateUserCommand $command): void
    {
        $user = $command->user;

        $hashedPassword = $this->passwordHasher->hashPassword($user, $user->getPlainPassword());
        $user->setPassword($hashedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}