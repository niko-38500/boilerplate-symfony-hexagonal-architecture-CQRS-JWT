<?php

namespace App\FrameworkInfrastructure\Domain\Repository;

use App\User\Domain\Entity\User;

interface PersisterManagerInterface
{

    public function save(object $entity, bool $flush = false): void;
}