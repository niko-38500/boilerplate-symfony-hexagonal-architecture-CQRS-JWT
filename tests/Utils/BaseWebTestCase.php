<?php

declare(strict_types=1);

namespace App\Tests\Utils;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\DataCollector\DoctrineDataCollector;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\Profiler\Profile;
use Symfony\Component\Routing\Router;

/**
 * @internal
 *
 * @coversNothing
 */
abstract class BaseWebTestCase extends WebTestCase
{
    protected ?EntityManagerInterface $entityManager;
    protected Router $router;
    protected static ?KernelBrowser $client;

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
        /** @var EntityManagerInterface $em */
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $this->entityManager = $em;
        $this->router = static::getContainer()->get('router');
    }

    public function tearDown(): void
    {
        unset($this->entityManager);
    }

    protected function getProfiler(): Profile
    {
        if ($profiler = self::$client->getProfile()) {
            return $profiler;
        }

        throw new \RuntimeException(
            'To get the profiler you must enable it first with "self::$client->enableProfiler()"'
        );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function getProfilerDbQueries(): array
    {
        /** @var DoctrineDataCollector $db */
        $db = $this->getProfiler()->getCollector('db');

        return $this->filterFixturesAndTransactionDbQueries($db);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function filterFixturesAndTransactionDbQueries(DoctrineDataCollector $dataCollector): array
    {
        $queries = $dataCollector->getQueries();

        return array_values(array_filter($queries['default'], function (array $query) {
            if ('"START TRANSACTION"' === $query['sql'] || '"COMMIT"' === $query['sql']) {
                return false;
            }

            foreach ($query['backtrace'] as $backtrace) {
                if ('loadFixtures' === $backtrace['function']) {
                    return false;
                }
            }

            return true;
        }));
    }
}
