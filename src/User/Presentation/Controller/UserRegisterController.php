<?php

declare(strict_types=1);

namespace App\User\Presentation\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/api/register", name: 'register', methods: ['POST', 'GET'])]
class UserRegisterController extends AbstractController
{
    public function __invoke()
    {
        return $this->json('authorized');
    }
}