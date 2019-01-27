<?php declare(strict_types=1);

namespace OpenTraining\Training\Repository;

use Doctrine\ORM\EntityManagerInterface;
use OpenTraining\Training\Entity\Watchdog;

final class WatchdogRepository
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function save(Watchdog $watchdog): void
    {
        $this->entityManager->persist($watchdog);
        $this->entityManager->flush();
    }
}
