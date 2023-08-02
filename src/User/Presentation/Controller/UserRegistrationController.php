<?php

declare(strict_types=1);

namespace App\User\Presentation\Controller;

use App\User\Domain\UseCase\CreateUser;
use App\User\Presentation\DTO\UserInputDTO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/api/registration", name: 'user_register', methods: ['POST'])]
class UserRegistrationController extends AbstractController
{
    public function __construct(
        private readonly CreateUser $createUser
    ) {}

    public function __invoke(#[MapRequestPayload] UserInputDTO $userDTO): JsonResponse
    {
        $this->createUser->execute($userDTO);

        return $this->json([
            'createdAt' => time(),
            'status' => 'Ressource created',
            'code' => Response::HTTP_CREATED,
            'message' => 'Utilisateur créer avec succès'
        ], Response::HTTP_CREATED);
    }
}