<?php declare(strict_types=1);

namespace OpenTraining\Training\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use OpenTraining\Training\Entity\TrainingTerm;

final class TrainingTermRepository
{
    /**
     * @var EntityRepository
     */
    private $entityRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityRepository = $entityManager->getRepository(TrainingTerm::class);
    }

    /**
     * @return TrainingTerm[]
     */
    public function fetchFinished(): array
    {
        return $this->entityRepository->createQueryBuilder('tt')
            ->where('tt.endDateTime < CURRENT_DATE()')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return TrainingTerm[]
     */
    public function fetchFinishedWithoutPaidProvision(): array
    {
        return $this->entityRepository->createQueryBuilder('tt')
            ->where('tt.endDateTime < CURRENT_DATE()')
            ->andWhere('tt.isProvisionPaid = false')
            ->getQuery()
            ->getResult();
    }
}
