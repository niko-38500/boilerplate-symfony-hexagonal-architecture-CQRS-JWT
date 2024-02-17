<?php

declare(strict_types=1);

namespace App\User\Domain\Repository;

use App\User\Domain\Entity\User;

interface UserRepositoryInterface
{
    public function findOneByEmail(string $email): ?User;

    public function findOneByEmailValidated(string $email): ?User;

    public function findOneByTemporaryToken(string $token): ?User;
    public function findOneByAuthenticatorProviderId(string $propertyPass, string $authenticatorId): ?User;
}
