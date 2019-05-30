<?php declare(strict_types=1);

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

    /**
     * @return MarketingEvent[]
     */
    public function findActive(): array
    {
        return $this->entityRepository->createQueryBuilder('me')
            ->select()
            ->andWhere('me.isDone = FALSE')
            ->andWhere('me.plannedAt <= CURRENT_DATE()')
            ->getQuery()
            ->getResult();
    }

    public function save(MarketingEvent $marketingEvent): void
    {
        $this->entityManager->persist($marketingEvent);
        $this->entityManager->flush();
    }
}
