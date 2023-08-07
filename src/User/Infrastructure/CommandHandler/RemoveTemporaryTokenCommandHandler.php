<?php

declare(strict_types=1);

namespace App\User\Infrastructure\CommandHandler;

use App\FrameworkInfrastructure\Domain\Command\CommandHandlerInterface;
use App\FrameworkInfrastructure\Domain\Repository\PersisterManagerInterface;
use App\FrameworkInfrastructure\Infrastructure\TemporaryToken\TemporaryToken;
use App\User\Domain\Command\RemoveTemporaryTokenCommand;
use Doctrine\ORM\EntityManagerInterface;

class RemoveTemporaryTokenCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly PersisterManagerInterface $persisterManager,
    ) {
    }

    public function __invoke(RemoveTemporaryTokenCommand $removeTemporaryTokenCommand): void
    {
        $token = $this->entityManager->getRepository(TemporaryToken::class)->find($removeTemporaryTokenCommand->token);

        $this->persisterManager->hardDelete($token);
    }
}
