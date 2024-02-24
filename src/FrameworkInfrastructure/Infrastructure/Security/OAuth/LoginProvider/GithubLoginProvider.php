<?php

declare(strict_types=1);

namespace App\FrameworkInfrastructure\Infrastructure\Security\OAuth\LoginProvider;

use App\FrameworkInfrastructure\Infrastructure\Security\OAuth\DTO\ResourceOwnerDTO;
use League\OAuth2\Client\Provider\GithubResourceOwner;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;

/**
 * @extends AbstractOAuthLoginProvider<GithubResourceOwner>
 */
class GithubLoginProvider extends AbstractOAuthLoginProvider
{
    public static function getUserPropertyPathForProvider(): string
    {
        return 'githubId';
    }

    public static function getCurrentAuthenticationProvider(): string
    {
        return 'github';
    }

    protected function transformClientResourceOwnerIntoDTO(ResourceOwnerInterface $resourceOwner): ResourceOwnerDTO
    {
        return new ResourceOwnerDTO(
            $resourceOwner->getNickname(),
            $resourceOwner->getEmail(),
            (string) $resourceOwner->getId(),
            self::getCurrentAuthenticationProvider()
        );
    }
}
