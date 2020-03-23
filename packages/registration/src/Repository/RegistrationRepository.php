<?php

declare(strict_types=1);

namespace Pehapkari\Registration\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Pehapkari\Registration\Entity\TrainingRegistration;

final class RegistrationRepository
{
    private EntityManagerInterface $entityManager;

    private EntityRepository $entityRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->entityRepository = $entityManager->getRepository(TrainingRegistration::class);
    }

    public function save(TrainingRegistration $trainingRegistration): void
    {
        $this->entityManager->persist($trainingRegistration);
        $this->entityManager->flush();
    }

    public function getFinishedCount(): int
    {
        return (int) $this->entityRepository->createQueryBuilder('tr')
            ->join('tr.trainingTerm', 'tt')
            ->select('count(tr.id)')
            ->andWhere('tt.startDateTime < CURRENT_DATE()')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param int[] $ids
     * @return TrainingRegistration[]
     */
    public function findWithoutInvoicesByIds(array $ids): array
    {
        return $this->entityRepository->findBy([
            'id' => $ids,
            'hasInvoice' => false,
        ]);
    }
}
