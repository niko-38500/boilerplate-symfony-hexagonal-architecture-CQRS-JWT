<?php

declare(strict_types=1);

namespace App\FrameworkInfrastructure\Infrastructure\Token;

use Carbon\CarbonImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
class TemporaryToken
{
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'string')]
        private readonly string $token,

        #[ORM\Column(type: 'datetime_immutable')]
        private readonly \DateTimeImmutable $expiresAt,
    ) {}

    public function getToken(): string
    {
        return $this->token;
    }

    public function getExpirationDate(): CarbonImmutable
    {
        return new CarbonImmutable($this->expiresAt);
    }

    public function isExpired(): bool
    {
        return $this->expiresAt < CarbonImmutable::now();
    }
}