<?php

declare(strict_types=1);

namespace App\User\Presentation\Controller;

use App\FrameworkInfrastructure\Domain\Exception\NotFoundException;
use App\User\Domain\UseCase\ValidateUserAccount;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/registration/validation', name: "user_account_validation", methods: ["GET"])]
class ConfirmRegistrationController extends AbstractController
{
    public function __construct(
        private readonly ValidateUserAccount $validateUserAccount
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

        return $this->json([
            'createdAt' => time(),
            'status' => 'ok',
            'code' => Response::HTTP_ACCEPTED,
            'data' => [
                'token' => $jwtToken,
            ],
        ], Response::HTTP_ACCEPTED);
    }
}