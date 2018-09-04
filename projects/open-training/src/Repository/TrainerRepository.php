<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Trainer;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

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
