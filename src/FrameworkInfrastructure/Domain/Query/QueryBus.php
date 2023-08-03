<?php

declare(strict_types=1);

namespace App\FrameworkInfrastructure\Domain\Query;

interface QueryBus
{
    /**
     * @template T
     *
     * @param QueryInterface<T> $query
     *
     * @return T
     */
    public function ask(QueryInterface $query);
}
