<?php

declare(strict_types=1);

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
    public function fetchPublic(): array
    {
        return $this->entityRepository->findBy([
            'isPublic' => true,
        ]);
    }

    /**
     * Trainings with active term today - last 30 days
     * @return Training[]
     */
    public function getRecentlyActive(): array
    {
        return $this->entityRepository->createQueryBuilder('t')
            // https://stackoverflow.com/a/41887524/1348344
            ->select('t')
            ->join('t.trainingTerms', 'tt')
            // pick only very recent trainings +/- 7 days
            ->andWhere('tt.startDateTime < :nextWeek')
            ->andWhere('tt.startDateTime > :weekAgo')
            ->setParameter(':weekAgo', DateTime::from('- 7 days'))
            ->setParameter(':nextWeek', DateTime::from('+ 7 days'))
            // has at least one registration
            ->join('tt.registrations', 'tr')
            // dual group is required, compatible for mysql + mariadb
            ->addGroupBy('t.id')
            ->addGroupBy('tt.startDateTime')
            // prefer closest to now - so people rating training at the day of training will get the date fo training :)
            // see https://stackoverflow.com/questions/21490993/expected-known-function-got-timedifffunction-not-found-in-doctrine-orm
            ->orderBy('time_diff(tt.startDateTime, CURRENT_TIMESTAMP())', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Training[]
     */
    public function getInactiveTrainings(): array
    {
        $trainings = $this->entityRepository->createQueryBuilder('t')
            ->andWhere('t.isPublic = true')
            ->orderBy('t.name')
            ->getQuery()
            ->getResult();

        return array_filter($trainings, function (Training $training): bool {
            return ! $training->isActive();
        });
    }

    public function getById(int $id): Training
    {
        return $this->entityRepository->find($id);
    }
}
