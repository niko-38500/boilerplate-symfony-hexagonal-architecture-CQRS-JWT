<?php

declare(strict_types=1);

namespace App\User\Presentation\Controller;

use App\User\Domain\Entity\User;
use App\User\Infrastructure\Command\CreateUserCommand;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/api/login", name: "login")]
class LoginController extends AbstractController
{
    public function __construct(
        private readonly MessageBusInterface $bus,
    ) {}

    public function __invoke(UserPasswordHasherInterface $a): JsonResponse
    {
        $user = new User('johnny', 'nico@hotmail.fr', '');
        $p = $a->hashPassword($user, 'azerty');
        $user = new User('johnny', 'john@doe.fr', $p);
        $this->bus->dispatch(new CreateUserCommand($user));
        return $this->json("ok");
    }
}