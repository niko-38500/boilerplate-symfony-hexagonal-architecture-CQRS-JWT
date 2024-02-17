<?php

declare(strict_types=1);

namespace App\FrameworkInfrastructure\Infrastructure\Jwt;

use App\FrameworkInfrastructure\Domain\Jwt\JwtGeneratorInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final readonly class JwtGenerator implements JwtGeneratorInterface
{
    public function __construct(
        private JWTTokenManagerInterface $JWTTokenManager,
    ) {}

    public function generate(UserInterface $user): string
    {
        return $this->JWTTokenManager->create($user);
    }
}
