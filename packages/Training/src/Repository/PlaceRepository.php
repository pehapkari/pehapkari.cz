<?php declare(strict_types=1);

namespace Pehapkari\Training\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Pehapkari\Training\Entity\Place;

final class PlaceRepository
{
    /**
     * @var string
     */
    private $relativeUploadDestination;

    /**
     * @var EntityRepository
     */
    private $entityRepository;

    public function __construct(EntityManagerInterface $entityManager, string $relativeUploadDestination)
    {
        $this->entityRepository = $entityManager->getRepository(Place::class);
        $this->relativeUploadDestination = $relativeUploadDestination;
    }

    /**
     * @return Place[]
     */
    public function fetchActive(): array
    {
        /** @var Place[] $places */
        $places = $this->entityRepository->findBy(['isPublic' => true]);

        // there is some postLoad weird bug, that skips @see SetUploadDestinationOnPostLoadEventSubscriber,
        // 40 mins wasted
        // this took 3 mins to write and works
        foreach ($places as $place) {
            $place->setRelativeUploadDestination($this->relativeUploadDestination);
        }

        return $places;
    }
}
