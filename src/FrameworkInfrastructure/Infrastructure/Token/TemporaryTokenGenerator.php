<?php

declare(strict_types=1);

namespace App\FrameworkInfrastructure\Infrastructure\Token;

use App\FrameworkInfrastructure\Domain\Repository\PersisterManagerInterface;
use Carbon\CarbonImmutable;

class TemporaryTokenGenerator
{
    public function __construct(
        private readonly PersisterManagerInterface $persisterManager,
    ) {}

    public function generate(int $delay): TemporaryToken
    {
        $token = new TemporaryToken(
            md5(uniqid() . uniqid()),
            new CarbonImmutable(sprintf('now + %s minutes', $delay))
        );

        $this->persisterManager->save($token);

        return $token;
    }
}