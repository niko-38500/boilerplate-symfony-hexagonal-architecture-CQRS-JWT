<?php

declare(strict_types=1);

namespace App\User\Domain\Query;

use App\FrameworkInfrastructure\Domain\Query\QueryInterface;
use App\User\Domain\Entity\User;

/**
 * @template-implements QueryInterface<User|null>
 */
class GetUserByEmailVerificationTokenQuery implements QueryInterface
{
    public function __construct(
        public readonly string $token
    ) {}
}