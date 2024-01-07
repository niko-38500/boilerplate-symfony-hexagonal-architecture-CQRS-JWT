<?php

declare(strict_types=1);

namespace App\User\Infrastructure\CommandHandler;

use App\FrameworkInfrastructure\Domain\Command\CommandHandlerInterface;
use App\FrameworkInfrastructure\Domain\Repository\PersisterManagerInterface;
use App\FrameworkInfrastructure\Infrastructure\TemporaryToken\TemporaryTokenGenerator;
use App\User\Domain\Command\CreateUserCommand;
use App\User\Infrastructure\Email\UserRegistrationConfirmationEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

readonly class CreateUserHandler implements CommandHandlerInterface
{
    public function __construct(
        private PersisterManagerInterface $persisterManager,
        private UserPasswordHasherInterface $passwordHasher,
        private MailerInterface $mailer,
        private TemporaryTokenGenerator $tokenGenerator,
        private int $emailConfirmationTokenExpirationDelay,
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
