<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Training;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

final class TrainingRepository
{
    /**
     * @var EntityRepository
     */
    private $entityRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityRepository = $entityManager->getRepository(Training::class);
    }

    /**
     * @return Training[]
     */
    public function fetchAll(): array
    {
        return $this->entityRepository->findAll();
    }
}
