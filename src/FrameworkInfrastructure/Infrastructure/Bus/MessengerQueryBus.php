<?php

declare(strict_types=1);

namespace App\FrameworkInfrastructure\Infrastructure\Bus;

use App\FrameworkInfrastructure\Domain\Query\QueryBus;
use App\FrameworkInfrastructure\Domain\Query\QueryInterface;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

class MessengerQueryBus implements QueryBus
{
    use HandleTrait {
        handle as handleQuery;
    }

    public function __construct(MessageBusInterface $queryBus)
    {
        $this->messageBus = $queryBus;
    }

    public function ask(QueryInterface $query): mixed
    {
        return $this->handleQuery($query);
    }
}
