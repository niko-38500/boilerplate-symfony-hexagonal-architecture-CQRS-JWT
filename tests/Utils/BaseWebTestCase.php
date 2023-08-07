<?php

declare(strict_types=1);

namespace App\Tests\Utils;

use Symfony\Bridge\Doctrine\DataCollector\DoctrineDataCollector;
use Symfony\Bridge\Doctrine\DataCollector\DoctrineDataCollector as BaseCollector;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpKernel\Profiler\Profile;
use Symfony\Component\Routing\Router;

/**
 * @internal
 *
 * @coversNothing
 */
class BaseWebTestCase extends BaseKernelTestCase
{
    protected static ?KernelBrowser $client;
    protected Router $router;

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
        parent::setUp();

        $this->router = static::getContainer()->get('router');
    }

    protected function getProfiler(): Profile
    {
        if ($profiler = self::$client->getProfile()) {
            return $profiler;
        }

        throw new \LogicException(
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
    protected function filterFixturesAndTransactionDbQueries(BaseCollector $dataCollector): array
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
