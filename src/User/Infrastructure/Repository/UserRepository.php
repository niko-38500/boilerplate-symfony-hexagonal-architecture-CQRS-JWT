<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Repository;

use App\FrameworkInfrastructure\Infrastructure\Token\TemporaryToken;
use App\User\Domain\Entity\User;
use App\User\Domain\Repository\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

class UserRepository implements UserRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {}

    private function createQueryBuilder(): QueryBuilder
    {
        return $this->entityManager->createQueryBuilder()->from(User::class, 'u');
    }

    public function findOneByEmail(string $email): ?User
    {
        return $this->createQueryBuilder()
            ->select('u')
            ->where('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneByEmailValidated(string $email): ?User
    {
        return $this->createQueryBuilder()
            ->select('u')
            ->where('u.email = :email')
            ->andWhere('u.isAccountValidated = TRUE')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneByTemporaryToken(string $token): ?User
    {
        /** @var ?TemporaryToken $storedToken */
        $storedToken = $this->entityManager->createQueryBuilder()
            ->from(TemporaryToken::class, 't')
            ->select('t')
            ->where('t.token = :token')
            ->andWhere('t.expiresAt > :now')
            ->setParameters([
                'token' => $token,
                'now' => new \DateTimeImmutable()
            ])
            ->getQuery()
            ->getOneOrNullResult();

        if (!$storedToken) {
            return null;
        }

        return $this->createQueryBuilder()
            ->select('u')
            ->where('u.emailVerificationToken = :token')
            ->setParameter('token', $storedToken->getToken())
            ->getQuery()
            ->getOneOrNullResult();
    }
}