<?php

declare(strict_types=1);

namespace App\FrameworkInfrastructure\Infrastructure\ValueObject;

readonly class ViolationItem
{
    public function __construct(
        public string $path,
        public string $message,
    ) {}
}
