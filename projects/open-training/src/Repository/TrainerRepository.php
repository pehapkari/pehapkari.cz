<?php declare(strict_types=1);

namespace OpenTraining\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use OpenTraining\Entity\Trainer;

final class TrainerRepository
{
    /**
     * @var EntityRepository
     */
    private $entityRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityRepository = $entityManager->getRepository(Trainer::class);
    }

    /**
     * @return Trainer[]
     */
    public function fetchAll(): array
    {
        return $this->entityRepository->findAll();
    }
}
