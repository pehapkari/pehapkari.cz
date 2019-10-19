<?php

declare(strict_types=1);

namespace Pehapkari\Marketing\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Pehapkari\Marketing\Entity\MarketingEvent;

final class MarketingEventRepository
{
    /**
     * @var EntityRepository
     */
    private $entityRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->entityRepository = $entityManager->getRepository(MarketingEvent::class);
    }

    public function getNextActiveEvent(): ?MarketingEvent
    {
        return $this->entityRepository->createQueryBuilder('me')
            ->select()
            ->andWhere('me.plannedAt <= CURRENT_DATE()')
            ->andWhere('me.publishedAt IS NULL')
            ->orderBy('me.plannedAt')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function save(MarketingEvent $marketingEvent): void
    {
        $this->entityManager->persist($marketingEvent);
        $this->entityManager->flush();
    }

    public function getLatestPublishedEventByPlatform(string $platform): ?MarketingEvent
    {
        return $this->entityRepository->createQueryBuilder('me')
            ->select()
            ->andWhere('me.platform = :platform')
            ->andWhere('me.publishedAt IS NOT NULL')
            ->orderBy('me.plannedAt', 'DESC')
            ->setParameter('platform', $platform)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
