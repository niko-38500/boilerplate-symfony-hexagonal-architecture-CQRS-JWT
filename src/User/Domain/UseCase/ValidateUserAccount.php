<?php

declare(strict_types=1);

namespace App\User\Domain\UseCase;

use App\FrameworkInfrastructure\Domain\Exception\NotFoundException;
use App\FrameworkInfrastructure\Domain\Jwt\JwtGeneratorInterface;
use App\FrameworkInfrastructure\Domain\MessengerDispatcherInterface;
use App\FrameworkInfrastructure\Domain\Query\QueryBus;
use App\FrameworkInfrastructure\Domain\Query\QueryDispatcherInterface;
use App\FrameworkInfrastructure\Domain\Repository\PersisterManagerInterface;
use App\User\Domain\Entity\User;
use App\User\Domain\Query\GetUserByEmailVerificationTokenQuery;
use App\User\Domain\Repository\UserRepositoryInterface;

class ValidateUserAccount
{
    public function __construct(
        private readonly PersisterManagerInterface $persisterManager,
        private readonly QueryBus  $queryDispatcher,
        private readonly JwtGeneratorInterface $jwtGenerator
    ) {}

    /**
     * @return string Return the JWT of the user
     */
    public function execute(string $token): string
    {
        $query = new GetUserByEmailVerificationTokenQuery($token);

        $user = $this->queryDispatcher->ask($query);

        if (is_null($user)) {
            throw new NotFoundException();
        }

        $user->validateAccount();
        $user->setEmailVerificationToken(null);

        $this->persisterManager->save($user, true);

        return $this->jwtGenerator->generate($user);
    }
}