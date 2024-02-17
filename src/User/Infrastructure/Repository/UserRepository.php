<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Repository;

use App\User\Domain\Entity\User;
use App\User\Domain\Repository\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;

readonly class UserRepository implements UserRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    public function findOneByEmail(string $email): ?User
    {
        return $this->createQueryBuilder()
            ->select('u')
            ->where('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findOneByEmailValidated(string $email): ?User
    {
        return $this->createQueryBuilder()
            ->select('u')
            ->where('u.email = :email')
            ->andWhere('u.isAccountValidated = TRUE')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findOneByTemporaryToken(string $token): ?User
    {
        return $this->createQueryBuilder()
            ->select('u')
            ->where('u.emailVerificationToken = :token')
            ->setParameter('token', $token)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findOneByAuthenticatorProviderId(string $propertyPass, string $authenticatorId): ?User
    {
        return $this->createQueryBuilder()
            ->select('u')
            ->where(sprintf('u.%s = :authenticatorProviderId', $propertyPass))
            ->setParameter('authenticatorProviderId', $authenticatorId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    private function createQueryBuilder(): QueryBuilder
    {
        return $this->entityManager->createQueryBuilder()->from(User::class, 'u');
    }
}
