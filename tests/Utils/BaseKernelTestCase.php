<?php

declare(strict_types=1);

namespace App\Tests\Utils;

use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Router;

/**
 * @internal
 *
 * @coversNothing
 */
abstract class BaseKernelTestCase extends WebTestCase
{
    protected ?EntityManagerInterface $entityManager;
    protected Router $router;
    private AbstractDatabaseTool $databaseTool;

    public function setUp(): void
    {
        $container = self::getContainer();

        /** @var DatabaseToolCollection $databaseTool */
        $databaseTool = $container->get(DatabaseToolCollection::class);
        $this->databaseTool = $databaseTool->get();
        $this->databaseTool->loadFixtures($this->loadFixtures());

        $this->entityManager = $container->get(EntityManagerInterface::class);
        $this->router = $container->get('router');
    }

    public function tearDown(): void
    {
        unset($this->entityManager);
    }

    /**
     * @return string[]
     */
    protected function loadFixtures(): array
    {
        return [];
    }
}
