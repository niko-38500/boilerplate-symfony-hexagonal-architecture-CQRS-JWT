<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Email;

use App\FrameworkInfrastructure\Infrastructure\Token\TemporaryToken;
use App\User\Domain\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class UserRegistrationConfirmationEmail extends TemplatedEmail
{
    public function __construct(User $user, TemporaryToken $temporaryToken)
    {
        parent::__construct();

        $this
            ->to($user->getEmail())
            ->subject('Veuillez confirmer votre compte')
            ->htmlTemplate('email/user_registration_confirmation.html.twig')
            ->context([
                'name' => $user->getUsername(),
                'expirationDate' => $temporaryToken->getExpirationDate(),
                'token' => $temporaryToken->getToken()
            ])
        ;
    }
}