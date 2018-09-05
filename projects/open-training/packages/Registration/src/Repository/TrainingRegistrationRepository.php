<?php declare(strict_types=1);

namespace OpenTraining\Registration\Repository;

use Doctrine\ORM\EntityManagerInterface;
use OpenTraining\Registration\Entity\TrainingRegistration;

final class TrainingRegistrationRepository
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function save(TrainingRegistration $trainingRegistration): void
    {
        $this->entityManager->persist($trainingRegistration);
        $this->entityManager->flush();
    }
}
