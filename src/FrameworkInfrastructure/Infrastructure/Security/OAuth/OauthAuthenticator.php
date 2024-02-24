<?php

declare(strict_types=1);

namespace App\FrameworkInfrastructure\Infrastructure\Security\OAuth;

use App\FrameworkInfrastructure\Domain\Jwt\JwtGeneratorInterface;
use App\FrameworkInfrastructure\Infrastructure\Security\OAuth\LoginProvider\AbstractOAuthLoginProvider;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class OauthAuthenticator extends OAuth2Authenticator
{
    /**
     * @param AbstractOAuthLoginProvider<ResourceOwnerInterface> $oAuthLogger
     */
    public function __construct(
        private readonly ClientRegistry $clientRegistry,
        private readonly JwtGeneratorInterface $tokenGenerator,
        private readonly AbstractOAuthLoginProvider $oAuthLogger
    ) {}

    public function supports(Request $request): ?bool
    {
        return
            'oauth_check' === $request->attributes->get('_route')
            && $request->query->has('code')
            && $request->query->has('state')
            && $request->attributes->has('authenticator');
    }

    public function authenticate(Request $request): Passport
    {
        $provider = $request->attributes->get('authenticator');

        $providerClient = $this->clientRegistry->getClient($provider);
        $providerClient->setAsStateless();

        $token = $this->fetchAccessToken($providerClient);

        $resourceOwner = $providerClient->fetchUserFromToken($token);

        $user = $this->oAuthLogger->loginUser($resourceOwner);

        return new SelfValidatingPassport(
            new UserBadge($user->getUserIdentifier(), static fn () => $user)
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return new JsonResponse([
            'message' => 'Authentication success',
            'token' => $this->tokenGenerator->generate($token->getUser()),
        ]);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse([
            'message' => 'Authentication Failure',
        ], Response::HTTP_UNAUTHORIZED);
    }
}
