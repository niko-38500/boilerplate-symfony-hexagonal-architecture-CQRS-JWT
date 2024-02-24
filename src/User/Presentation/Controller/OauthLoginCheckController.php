<?php

declare(strict_types=1);

namespace App\User\Presentation\Controller;

use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

#[
    Route('/api/v1/oauth/check/{authenticator}', name: 'oauth_check', methods: ['GET'], stateless: true),
    OA\Get(
        description: 'Redirect target for initiating the login or registration process of a user using an OAuth authenticator.',
        summary: 'Connect to the application via OAuth',
        parameters: [
            new OA\Parameter(
                name: 'authenticator',
                description: 'The authenticator provider',
                in: 'path',
                required: true
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Return the JWT'),
        ]
    ),
    OA\Tag(name: 'User')
]
class OauthLoginCheckController extends AbstractController
{
    public function __invoke(): void {}
}
