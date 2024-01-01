<?php

declare(strict_types=1);

namespace App\User\Infrastructure\CommandHandler;

use App\FrameworkInfrastructure\Domain\Command\CommandHandlerInterface;
use App\FrameworkInfrastructure\Domain\Repository\PersisterManagerInterface;
use App\FrameworkInfrastructure\Infrastructure\Token\TemporaryTokenGenerator;
use App\User\Domain\Command\CreateUserCommand;
use App\User\Infrastructure\Email\UserRegistrationConfirmationEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateUserHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly PersisterManagerInterface $persisterManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly MailerInterface $mailer,
        private readonly TemporaryTokenGenerator $tokenGenerator,
        private readonly int $emailConfirmationTokenExpirationDelay
    ) {}

    public function __invoke(CreateUserCommand $command): void
    {
        $user = $command->user;

        $hashedPassword = $this->passwordHasher->hashPassword($user, $user->getPlainPassword());
        $user->setPassword($hashedPassword);
        $user->eraseCredentials();

        $token = $this->tokenGenerator->generate($this->emailConfirmationTokenExpirationDelay);

        $user->setEmailVerificationToken($token->getToken());

        $this->persisterManager->save($user, true);

        $this->mailer->send(new UserRegistrationConfirmationEmail($user, $token));
    }
}
