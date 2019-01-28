<?php declare(strict_types=1);

namespace OpenTraining\Training\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use OpenTraining\Training\Entity\TrainingFeedback;

final class TrainingFeedbackRepository
{
    /**
     * @var EntityRepository
     */
    private $entityRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityRepository = $entityManager->getRepository(TrainingFeedback::class);
        $this->entityManager = $entityManager;
    }

    /**
     * @return TrainingFeedback[]
     */
    public function fetchAll(): array
    {
        return $this->entityRepository->findAll();
    }

    public function save(TrainingFeedback $trainingFeedback): void
    {
        $this->entityManager->persist($trainingFeedback);
        $this->entityManager->flush();
    }
}
