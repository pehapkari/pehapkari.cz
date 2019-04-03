<?php declare(strict_types=1);

namespace Pehapkari\Training\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Pehapkari\Training\Entity\Trainer;

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
