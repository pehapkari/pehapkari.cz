<?php declare(strict_types=1);

namespace Pehapkari\Registration\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Pehapkari\Registration\Entity\TrainingRegistration;

final class TrainingRegistrationRepository
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var EntityRepository
     */
    private $entityRepository;

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
            ->andWhere('tt.endDateTime < CURRENT_DATE()')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param int[] $ids
     * @return TrainingRegistration[]
     */
    public function findByIds(array $ids): array
    {
        return $this->entityRepository->findBy(['id' => $ids]);
    }

    /**
     * @return TrainingRegistration[]
     */
    public function getUnpaid(): array
    {
        return $this->entityRepository->findBy([
            'isPaid' => false,
        ]);
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
