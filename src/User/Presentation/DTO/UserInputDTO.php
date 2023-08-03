<?php

declare(strict_types=1);

namespace App\User\Presentation\DTO;

class UserInputDTO
{
    public function __construct(
        public readonly string $username,
        public readonly string $email,
        public readonly ?string $plainPassword = null
    ) {
    }
}
