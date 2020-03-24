<?php

declare(strict_types=1);

namespace Pehapkari\Training\Repository;

use Doctrine\Common\Proxy\Proxy;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ObjectRepository;
use Nette\Utils\DateTime;
use Pehapkari\Training\Entity\Training;
use Pehapkari\Training\Entity\TrainingTerm;

final class TrainingTermRepository
{
    private EntityManagerInterface $entityManager;

    /**
     * @var EntityRepository&ObjectRepository
     */
    private ObjectRepository $entityRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->entityRepository = $entityManager->getRepository(TrainingTerm::class);
    }

    public function getFinishedCount(): int
    {
        return count($this->getFinished());
    }

    /**
     * @return TrainingTerm[]
     */
    public function getUpcoming(): array
    {
        return $this->entityRepository->createQueryBuilder('tt')
            ->where('tt.startDateTime > CURRENT_DATE()')
            ->orderBy('tt.startDateTime')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return bool|Proxy|object|null
     */
    public function getReference($id)
    {
        return $this->entityManager->getReference(TrainingTerm::class, $id);
    }

    /**
     * @param int[] $ids
     * @return TrainingTerm[]
     */
    public function findByIds(array $ids): array
    {
        return $this->entityRepository->findBy(['id' => $ids]);
    }

    public function delete(TrainingTerm $trainingTerm): void
    {
        $this->entityManager->remove($trainingTerm);
        $this->entityManager->flush();
    }

    /**
     * @return Training[]
     */
    public function getRecentlyActive(): array
    {
        return $this->entityRepository->createQueryBuilder('tt')
            ->select('tt')
            ->andWhere('tt.startDateTime < :nextMonth')
            ->andWhere('tt.startDateTime > :weekAgo')
            ->setParameter(':weekAgo', DateTime::from('- 7 days'))
            ->setParameter(':nextMonth', DateTime::from('+ 30 days'))
            ->getQuery()
            ->getResult();
    }

    /**
     * To resolve provision
     */
    public function getCountOfPreviousTrainingTermsByTrainer(TrainingTerm $trainingTerm): int
    {
        return (int) $this->entityRepository->createQueryBuilder('tt')
            ->select('count(tt.id)')
            ->join('tt.training', 't')
            ->andWhere('tt.startDateTime < :currentDate')
            ->andWhere('tt.isProvisionPaid = true')
            ->andWhere('t.trainer = :currentTrainer')
            ->setParameter('currentDate', $trainingTerm->getStartDateTime())
            ->setParameter('currentTrainer', $trainingTerm->getTrainer())
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function save(TrainingTerm $trainingTerm): void
    {
        $this->entityManager->persist($trainingTerm);
        $this->entityManager->flush();
    }

    /**
     * @return TrainingTerm[]
     */
    private function getFinished(): array
    {
        return $this->entityRepository->createQueryBuilder('tt')
            ->where('tt.startDateTime < CURRENT_DATE()')
            ->andWhere('tt.isProvisionPaid = true')
            ->getQuery()
            ->getResult();
    }
}
