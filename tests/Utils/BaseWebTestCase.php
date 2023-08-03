<?php

declare(strict_types=1);

namespace App\Tests\Utils;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Router;

class BaseWebTestCase extends WebTestCase
{
    protected static ?KernelBrowser $client;
    protected ?EntityManagerInterface $entityManager;
    protected Router $router;
    private AbstractDatabaseTool $databaseTool;

    public static function setUpBeforeClass(): void
    {
        self::$client = static::createClient();
    }

    public static function tearDownAfterClass(): void
    {
        self::$client->getKernel()->shutdown();
        self::ensureKernelShutdown();
    }

    public function setUp(): void
    {
        $container = self::getContainer();

        $this->databaseTool = $container->get(DatabaseToolCollection::class)->get();
        //        $this->databaseTool->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $this->databaseTool->loadFixtures();

        $this->entityManager = $container->get(EntityManagerInterface::class);
        $this->router = $container->get('router');
    }

    public function tearDown(): void
    {
        //        $purger = new ORMPurger($this->entityManager);
        //        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        //        $purger->purge();

        unset($this->entityManager);
    }
}
