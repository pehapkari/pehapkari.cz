<?php

declare(strict_types=1);

namespace Pehapkari\Training\Repository;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Pehapkari\Training\Entity\Trainer;

final class TrainerRepository
{
    /**
     * @var ObjectRepository
     */
    private $objectRepository;

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
}
