<?php

declare(strict_types=1);

namespace App\User\Presentation\Controller;

use App\FrameworkInfrastructure\Domain\Exception\NotFoundException;
use App\User\Domain\UseCase\ValidateUserAccount;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/registration/validation', name: 'user_account_validation', methods: ['GET'])]
class ConfirmRegistrationController extends AbstractController
{
    public function __construct(
        private readonly ValidateUserAccount $validateUserAccount
    ) {
    }

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
