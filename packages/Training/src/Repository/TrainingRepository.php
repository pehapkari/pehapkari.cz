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
            ->andWhere('tt.startDateTime < :nextWeek')
            ->andWhere('tt.endDateTime > :weekAgo')
            ->setParameter(':weekAgo', DateTime::from('- 7 days'))
            ->setParameter(':nextWeek', DateTime::from('+ 7 days'))
            ->addGroupBy('tt.startDateTime')
            ->orderBy('tt.startDateTime') // put more recent first
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Training[]
     */
    public function fetchInactiveTrainings(): array
    {
        $trainings = $this->entityRepository->createQueryBuilder('t')
            ->orderBy('t.name')
            ->getQuery()
            ->getResult();

        return array_filter($trainings, function (Training $training): bool {
            return ! $training->isActive();
        });
    }
}
