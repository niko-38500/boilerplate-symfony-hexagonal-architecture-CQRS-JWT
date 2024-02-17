<?php

declare(strict_types=1);

namespace App\FrameworkInfrastructure\Infrastructure\Security\OAuth\Factory;

use App\FrameworkInfrastructure\Infrastructure\Exception\NotImplementedException;
use App\FrameworkInfrastructure\Infrastructure\Security\OAuth\ClientTransformer\ClientResourceOwnerTransformerInterface;
use App\FrameworkInfrastructure\Infrastructure\Security\OAuth\ClientTransformer\GithubResourceOwnerTransformer;

class OAuthClientTransformerFactory
{
    public const array AVAILABLE_TRANSFORMERS = ['github' => 'github'];

    /**
     * @throws NotImplementedException
     */
    public function get(string $authenticatorProvider): ClientResourceOwnerTransformerInterface
    {
        return match ($authenticatorProvider) {
            self::AVAILABLE_TRANSFORMERS['github'] => new GithubResourceOwnerTransformer(),
            default => throw new NotImplementedException(sprintf(
                '%s transformer is not implemented. Available transformers : %s',
                $authenticatorProvider,
                implode(', ', self::AVAILABLE_TRANSFORMERS)
            ))
        };
    }
}