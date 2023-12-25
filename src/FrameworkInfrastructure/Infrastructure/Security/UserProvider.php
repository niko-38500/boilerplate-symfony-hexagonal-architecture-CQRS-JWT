<?php

declare(strict_types=1);

namespace App\FrameworkInfrastructure\Infrastructure\Security;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserProvider
{
    public function __construct(
        private readonly TokenStorageInterface $tokenStorage
    ) {}

    public function getAuthenticatedUser(): UserInterface
    {
        $token = $this->tokenStorage->getToken();

        return $token->getUser();
    }
}
