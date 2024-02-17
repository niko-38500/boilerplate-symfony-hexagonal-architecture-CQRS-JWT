<?php

declare(strict_types=1);

namespace App\FrameworkInfrastructure\Infrastructure\Security\OAuth\DTO;

readonly class ResourceOwnerDTO
{
    public function __construct(
        public string $username,
        public string $email,
        public string $providerId,
        public string $currentProvider
    ) { }
}