<?php

declare(strict_types=1);

namespace App\User\Presentation\Controller;

use App\FrameworkInfrastructure\Domain\Exception\NotFoundException;
use App\User\Domain\UseCase\ValidateUserAccount;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[
    Route('/api/v1/user/login/registration/validation', name: 'user_account_validation', methods: ['GET']),
    OA\Get(
        description: 'Validate a user account from a temporary token (30 minutes validity)',
        summary: 'Validate a new user account',
        parameters: [
            new OA\Parameter(
                name: 'token',
                description: 'Generated temporary token linked to the account confirmation mail',
                in: 'query',
                required: true
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'User account is validated'),
            new OA\Response(response: 404, description: 'Validation token is not present or not found'),
        ]
    ),
    OA\Tag(name: 'User', description: 'Actions related to the user')
]
class ConfirmRegistrationController extends AbstractController
{
    public function __construct(
        private readonly ValidateUserAccount $validateUserAccount,
    ) {}

    /**
     * @throws NotFoundException
     */
    public function __invoke(Request $request): JsonResponse
    {
        $token = $request->query->get('token');

        if (!$token) {
            throw new NotFoundException();
        }

        $jwtToken = $this->validateUserAccount->execute($token);

        $response = $this->json([
            'createdAt' => time(),
            'status' => 'ok',
            'code' => Response::HTTP_OK,
            'message' => 'Votre compte à été validé avec succès',
        ], Response::HTTP_OK);

        $response->headers->setCookie(Cookie::create('user_id', $jwtToken, strtotime('now + 10 days')));

        return $response;
    }
}
