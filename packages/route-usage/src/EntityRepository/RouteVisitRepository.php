<?php

declare(strict_types=1);

namespace Pehapkari\RouteUsage\EntityRepository;

use Doctrine\ORM\EntityManagerInterface;
use Pehapkari\RouteUsage\Entity\RouteVisit;
use Pehapkari\RouteUsage\ValueObject\RouteUsageStat;
use Symplify\EasyHydrator\ArrayToValueObjectHydrator;

final class RouteVisitRepository
{
    private EntityManagerInterface $entityManager;

    private ArrayToValueObjectHydrator $arrayToValueObjectHydrator;

    public function __construct(
        EntityManagerInterface $entityManager,
        ArrayToValueObjectHydrator $arrayToValueObjectHydrator
    ) {
        $this->entityManager = $entityManager;
        $this->arrayToValueObjectHydrator = $arrayToValueObjectHydrator;
    }

    public function save(RouteVisit $routeVisit): void
    {
        $this->entityManager->persist($routeVisit);
        $this->entityManager->flush();
    }

    /**
     * @return RouteUsageStat[]
     */
    public function fetchAll(): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->from(RouteVisit::class, 'r');
        $queryBuilder->select('r.route, r.controller, r.routeParams, r.uniqueRouteHash, COUNT(r.id) as usage_count');
        $queryBuilder->groupBy('r.route, r.controller, r.routeParams, r.uniqueRouteHash');
        $queryBuilder->orderBy('usage_count', 'DESC');

        $query = $queryBuilder->getQuery();
        $result = $query->getResult();

        return $this->arrayToValueObjectHydrator->hydrateArrays($result, RouteUsageStat::class);
    }
}
