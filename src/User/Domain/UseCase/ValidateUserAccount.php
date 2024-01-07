<?php

declare(strict_types=1);

namespace App\User\Domain\UseCase;

use App\FrameworkInfrastructure\Domain\Command\CommandBus;
use App\FrameworkInfrastructure\Domain\Exception\NotFoundException;
use App\FrameworkInfrastructure\Domain\Jwt\JwtGeneratorInterface;
use App\FrameworkInfrastructure\Domain\Query\QueryBus;
use App\FrameworkInfrastructure\Domain\Repository\PersisterManagerInterface;
use App\User\Domain\Command\RemoveTemporaryTokenCommand;
use App\User\Domain\Query\GetUserByEmailVerificationTokenQuery;

readonly class ValidateUserAccount
{
    public function __construct(
        private PersisterManagerInterface $persisterManager,
        private QueryBus $queryDispatcher,
        private CommandBus $commandBus,
        private JwtGeneratorInterface $jwtGenerator,
    ) {}

    /**
     * @return string Return the JWT of the user
     *
     * @throws NotFoundException
     */
    public function execute(string $token): string
    {
        $query = new GetUserByEmailVerificationTokenQuery($token);

        $user = $this->queryDispatcher->ask($query);

        if (is_null($user)) {
            throw new NotFoundException();
        }

        $removeTokenCommand = new RemoveTemporaryTokenCommand($user->getEmailVerificationToken());
        $this->commandBus->dispatch($removeTokenCommand);

        $user->validateAccount();
        $user->setEmailVerificationToken(null);

        $this->persisterManager->save($user, true);

        return $this->jwtGenerator->generate($user);
    }
}
