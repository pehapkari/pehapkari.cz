<?php declare(strict_types=1);

namespace OpenTraining\Training\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use OpenTraining\Training\Entity\Place;

final class PlaceRepository
{
    /**
     * @var EntityRepository
     */
    private $entityRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityRepository = $entityManager->getRepository(Place::class);
    }

    public function getMainPlace(): Place
    {
        $places = $this->entityRepository->findAll();

        return array_pop($places);
    }

    /**
     * @return Place[]
     */
    public function fetchAll(): array
    {
        return $this->entityRepository->findAll();
    }
}
