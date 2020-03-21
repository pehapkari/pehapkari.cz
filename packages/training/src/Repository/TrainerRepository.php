<?php

declare(strict_types=1);

namespace Pehapkari\Training\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Pehapkari\Training\Entity\Trainer;

final class TrainerRepository
{
    private EntityRepository $objectRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->objectRepository = $entityManager->getRepository(Trainer::class);
    }

    /**
     * @return Trainer[]
     */
    public function fetchAll(): array
    {
        return $this->objectRepository->findAll();
    }

    public function getCount(): int
    {
        return count($this->objectRepository->findAll());
    }

    /**
     * @return Trainer[]
     */
    public function fetchAllSortedByTrainingTermCount(): array
    {
        $trainers = $this->fetchAll();

        usort(
            $trainers,
            fn (Trainer $firstTrainer, Trainer $secondTrainer) => $secondTrainer->getTrainingTermCount() <=> $firstTrainer->getTrainingTermCount()
        );

        return $trainers;
    }
}
