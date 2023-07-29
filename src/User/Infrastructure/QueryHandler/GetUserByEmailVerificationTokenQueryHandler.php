<?php

declare(strict_types=1);

namespace App\User\Infrastructure\QueryHandler;

use App\FrameworkInfrastructure\Domain\Query\QueryHandlerInterface;
use App\User\Domain\Entity\User;
use App\User\Domain\Query\GetUserByEmailVerificationTokenQuery;
use App\User\Domain\Repository\UserRepositoryInterface;

class GetUserByEmailVerificationTokenQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {}

    public function __invoke(GetUserByEmailVerificationTokenQuery $query): ?User
    {
        return $this->userRepository->findOneByTemporaryToken($query->token);
    }
}