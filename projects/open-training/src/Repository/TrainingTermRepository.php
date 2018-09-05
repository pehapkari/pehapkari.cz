<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\TrainingTerm;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

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
    public function fetchFinishedBackup(): array
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
