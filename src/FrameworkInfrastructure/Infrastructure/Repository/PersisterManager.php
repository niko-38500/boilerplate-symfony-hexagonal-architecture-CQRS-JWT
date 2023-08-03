<?php

declare(strict_types=1);

namespace App\FrameworkInfrastructure\Infrastructure\Repository;

use App\FrameworkInfrastructure\Domain\Repository\PersisterManagerInterface;
use Doctrine\ORM\EntityManagerInterface;

class PersisterManager implements PersisterManagerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function save(object $entity, bool $flush = false): void
    {
        $this->entityManager->persist($entity);

        if ($flush) {
            $this->entityManager->flush();
        }
    }
}
