<?php

declare(strict_types=1);

namespace Pehapkari\RouteUsage\Tests\EntityRepository;

use Doctrine\DBAL\Configuration;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Utils\DateTime;
use Pehapkari\PehapkariKernel;
use Pehapkari\RouteUsage\Entity\RouteVisit;
use Pehapkari\RouteUsage\EntityRepository\RouteVisitRepository;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

final class RouteVisitRepositoryTest extends AbstractKernelTestCase
{
    private RouteVisitRepository $routeVisitRepository;

    protected function setUp(): void
    {
        $this->bootKernel(PehapkariKernel::class);

        $this->routeVisitRepository = self::$container->get(RouteVisitRepository::class);
        $this->disableDoctrineLogger();
    }

    public function test(): void
    {
        $routeVisit = new RouteVisit('some_route', "{'route':'params'}", 'SomeController', DateTime::from('now'));

        $this->routeVisitRepository->save($routeVisit);

        $routeUsageStat = $this->routeVisitRepository->fetchAll();
        $this->assertCount(1, $routeUsageStat);
    }

    private function disableDoctrineLogger(): void
    {
        // @see https://stackoverflow.com/a/35222045/1348344
        // disable Doctrine logs in tests output
        $entityManager = self::$container->get(EntityManagerInterface::class);
        $entityManager->getConfiguration();
        $connection = $entityManager->getConnection();

        /** @var Configuration $configuration */
        $configuration = $connection->getConfiguration();
        $configuration->setSQLLogger(null);
    }
}
