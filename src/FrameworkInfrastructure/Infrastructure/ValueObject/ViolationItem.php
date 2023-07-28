<?php

declare(strict_types=1);

namespace App\FrameworkInfrastructure\Infrastructure\ValueObject;

class ViolationItem
{
    public function __construct(
        public readonly string $path,
        public readonly string $message,
    ) {}
}