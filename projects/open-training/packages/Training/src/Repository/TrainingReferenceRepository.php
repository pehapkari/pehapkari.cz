<?php declare(strict_types=1);

namespace OpenTraining\Training\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use OpenTraining\Training\Entity\TrainingReference;

final class TrainingReferenceRepository
{
    /**
     * @var EntityRepository
     */
    private $entityRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityRepository = $entityManager->getRepository(TrainingReference::class);
    }

    /**
     * @return TrainingReference[]
     */
    public function fetchAll(): array
    {
        return $this->entityRepository->findAll();
    }
}
