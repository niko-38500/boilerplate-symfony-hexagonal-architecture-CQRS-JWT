<?php

declare(strict_types=1);

namespace App\Tests\Utils;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Routing\Router;

/**
 * @internal
 *
 * @coversNothing
 */
abstract class BaseKernelTestCase extends KernelTestCase
{
    protected ?EntityManagerInterface $entityManager;
    protected Router $router;

    public function setUp(): void
    {
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $this->router = self::getContainer()->get('router');
    }

    public function tearDown(): void
    {
        unset($this->entityManager);
    }
}
