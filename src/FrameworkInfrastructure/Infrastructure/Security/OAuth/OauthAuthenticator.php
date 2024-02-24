<?php

declare(strict_types=1);

namespace App\FrameworkInfrastructure\Infrastructure\Security\OAuth;

use App\FrameworkInfrastructure\Domain\Jwt\JwtGeneratorInterface;
use App\FrameworkInfrastructure\Infrastructure\Exception\NotImplementedException;
use App\FrameworkInfrastructure\Infrastructure\Security\OAuth\Factory\OAuthClientTransformerFactory;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class OauthAuthenticator extends OAuth2Authenticator
{
    public function __construct(
        private readonly ClientRegistry $clientRegistry,
        private readonly JwtGeneratorInterface $tokenGenerator,
        private readonly OAuthClientTransformerFactory $clientTransformerFactory,
        private readonly OAuthUserLogin $oAuthUserLogin
    ) {}

    public function supports(Request $request): ?bool
    {
        return
            'oauth_check' === $request->attributes->get('_route')
            && $request->query->has('code')
            && $request->query->has('state');
    }

    public function authenticate(Request $request): Passport
    {
        $provider = $request->attributes->get('authenticator');

        $providerClient = $this->clientRegistry->getClient($provider);
        $providerClient->setAsStateless();

        $token = $this->fetchAccessToken($providerClient);

        $clientResourceOwner = $providerClient->fetchUserFromToken($token);

        try {
            $clientTransformer = $this->clientTransformerFactory->get($provider);
        } catch (NotImplementedException $e) {
            throw new CustomUserMessageAuthenticationException($e->getMessage());
        }

        $resourceOwner = $clientTransformer->transform($clientResourceOwner);

        $user = $this->oAuthUserLogin->loginUser($resourceOwner);

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
