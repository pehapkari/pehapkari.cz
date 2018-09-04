<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\TrainingReference;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

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
