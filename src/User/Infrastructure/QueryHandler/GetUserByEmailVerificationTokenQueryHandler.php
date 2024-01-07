<?php

declare(strict_types=1);

namespace App\User\Infrastructure\QueryHandler;

use App\FrameworkInfrastructure\Domain\Query\QueryHandlerInterface;
use App\FrameworkInfrastructure\Infrastructure\TemporaryToken\TemporaryToken;
use App\User\Domain\Entity\User;
use App\User\Domain\Query\GetUserByEmailVerificationTokenQuery;
use App\User\Domain\Repository\UserRepositoryInterface;
use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;

readonly class GetUserByEmailVerificationTokenQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private EntityManagerInterface $entityManager,
    ) {}

    public function __invoke(GetUserByEmailVerificationTokenQuery $query): ?User
    {
        /** @var ?TemporaryToken $storedToken */
        $storedToken = $this->entityManager->createQueryBuilder()
            ->from(TemporaryToken::class, 't')
            ->select('t')
            ->where('t.token = :token')
            ->andWhere('t.expiresAt > :now')
            ->setParameters([
                'token' => $query->token,
                'now' => CarbonImmutable::now(),
            ])
            ->getQuery()
            ->getOneOrNullResult()
        ;

        if (!$storedToken) {
            return null;
        }

        return $this->userRepository->findOneByTemporaryToken($storedToken->getToken());
    }
}
