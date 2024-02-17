<?php

declare(strict_types=1);

namespace App\FrameworkInfrastructure\Infrastructure\Security\OAuth\ClientTransformer;

use App\FrameworkInfrastructure\Infrastructure\Security\OAuth\DTO\ResourceOwnerDTO;
use League\OAuth2\Client\Provider\GithubResourceOwner;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class GithubResourceOwnerTransformer implements ClientResourceOwnerTransformerInterface
{
    /**
     * @param GithubResourceOwner $resourceOwner
     */
    public function transform(ResourceOwnerInterface $resourceOwner): ResourceOwnerDTO
    {
        return new ResourceOwnerDTO(
            $resourceOwner->getNickname(),
            $resourceOwner->getEmail(),
            (string) $resourceOwner->getId(),
            'github'
        );
    }
}