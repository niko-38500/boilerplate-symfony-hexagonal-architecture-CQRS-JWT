<?php

namespace App\FrameworkInfrastructure\Domain\Jwt;

use Symfony\Component\Security\Core\User\UserInterface;

interface JwtGeneratorInterface
{
    public function generate(UserInterface $user): string;
}
