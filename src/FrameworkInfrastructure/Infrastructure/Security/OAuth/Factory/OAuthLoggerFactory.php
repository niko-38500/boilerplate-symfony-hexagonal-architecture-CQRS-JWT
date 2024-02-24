<?php

declare(strict_types=1);

namespace App\FrameworkInfrastructure\Infrastructure\Security\OAuth\Factory;

use App\FrameworkInfrastructure\Infrastructure\Exception\NotImplementedException;
use App\FrameworkInfrastructure\Infrastructure\Security\OAuth\LoginProvider\AbstractOAuthLoginProvider;
use App\FrameworkInfrastructure\Infrastructure\Security\OAuth\LoginProvider\GithubLoginProvider;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class OAuthLoggerFactory
{
    /** @var string[] */
    public const array AVAILABLE_LOGGER = ['github' => 'github'];

    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly GithubLoginProvider $githubLogger
    ) {}

    /**
     * @return AbstractOAuthLoginProvider<covariant ResourceOwnerInterface>
     *
     * @throws NotImplementedException
     */
    public function __invoke(): AbstractOAuthLoginProvider
    {
        $authenticatorProvider = $this->requestStack->getCurrentRequest()->attributes->get('authenticator');

        return match ($authenticatorProvider) {
            self::AVAILABLE_LOGGER['github'] => $this->githubLogger,

            default => throw new NotImplementedException(sprintf(
                '%s transformer is not implemented. Available transformers : %s',
                $authenticatorProvider,
                implode(', ', self::AVAILABLE_LOGGER)
            ))
        };
    }
}
