<?php

declare(strict_types=1);

namespace App\FrameworkInfrastructure\Infrastructure\Repository;

use App\FrameworkInfrastructure\Domain\Repository\PersisterManagerInterface;
use Doctrine\ORM\EntityManagerInterface;

readonly class PersisterManager implements PersisterManagerInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    public function save(object $entity, bool $flush = false): void
    {
        $this->entityManager->persist($entity);

        if ($flush) {
            $this->entityManager->flush();
        }
    }

    public function hardDelete(object $entity, bool $flush = false): void
    {
        $this->entityManager->remove($entity);

        if ($flush) {
            $this->entityManager->flush();
        }
    }
}
