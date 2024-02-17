<?php

declare(strict_types=1);

namespace App\User\Presentation\Controller;

use App\User\Domain\UseCase\CreateUser;
use App\User\Presentation\DTO\UserInputDTO;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[
    Route('/api/v1/user/login/registration', name: 'user_register', methods: ['POST']),
    OA\Post(
        description: 'Register a user',
        summary: 'Register a user',
        requestBody: new OA\RequestBody(required: true),
        responses: [
            new OA\Response(response: 200, description: 'User account is created'),
        ]
    ),
    OA\Tag(name: 'User', description: 'Actions related to the user')
]
class UserRegistrationController extends AbstractController
{
    public function __construct(
        private readonly CreateUser $createUser,
    ) {}

    public function __invoke(#[MapRequestPayload] UserInputDTO $userDTO): JsonResponse
    {
        $this->createUser->execute($userDTO);

        return $this->json([
            'createdAt' => time(),
            'status' => 'Ressource created',
            'code' => Response::HTTP_CREATED,
            'message' => 'Utilisateur créer avec succès',
        ], Response::HTTP_CREATED);
    }
}
