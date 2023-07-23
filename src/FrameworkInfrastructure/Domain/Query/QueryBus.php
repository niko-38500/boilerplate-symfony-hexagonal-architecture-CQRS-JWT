<?php

declare(strict_types=1);

namespace App\FrameworkInfrastructure\Domain\Query;

interface QueryBus
{
    public function ask(QueryInterface $query): mixed;
}