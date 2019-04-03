<?php declare(strict_types=1);

namespace Pehapkari\Training\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Nette\Utils\DateTime;
use Pehapkari\Training\Entity\Training;

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

    /**
     * Trainings with active term today - last 30 days
     * @return Training[]
     */
    public function fetchRecentlyActive(): array
    {
        return $this->entityRepository->createQueryBuilder('t')
            ->join('t.trainingTerms', 'tt')
            ->andWhere('tt.startDateTime >= CURRENT_DATE()')
            ->andWhere('tt.endDateTime < :weekAgo')
            ->setParameter(':weekAgo', DateTime::from('- 30 days'))
            ->groupBy('t.id')
            ->getQuery()
            ->getResult();
    }
}
