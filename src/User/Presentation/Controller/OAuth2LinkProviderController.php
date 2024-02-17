<?php

declare(strict_types=1);

namespace App\User\Presentation\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[
    Route('/api/v1/user/login/oauth/connect/{authenticator}', name: 'oauth_connect', methods: ['GET']),
    OA\Get(
        description: 'Generate oAuth2 link for authenticator provider authentication',
        summary: 'Obtain a link redirecting to the authenticator provider\'s authentication page',
        parameters: [
            new OA\Parameter(
                name: 'authenticator',
                description: 'The authenticator provider',
                in: 'path',
                required: true
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Return an authentication link to the authenticator provider'),
        ]
    ),
    OA\Tag(name: 'User')
]
final class OAuth2LinkProviderController extends AbstractController
{
    public function __construct(
        private readonly ClientRegistry $clientRegistry,
    ) {}

    public function __invoke(string $authenticator): JsonResponse
    {
        $oauthClient = $this->clientRegistry->getClient($authenticator);
        $oauthClient->setAsStateless();

        $targetUrl = $oauthClient->redirect(['user', 'read:user', 'user:email'], [])->getTargetUrl();

        return $this->json([
            'authentication_link' => $targetUrl,
        ]);
    }
}
