<?php

declare(strict_types=1);

namespace App\User\Presentation\DTO;

final readonly class UserInputDTO
{
    public function __construct(
        public string $username,
        public string $email,
        public string $plainPassword,
    ) {}
}
