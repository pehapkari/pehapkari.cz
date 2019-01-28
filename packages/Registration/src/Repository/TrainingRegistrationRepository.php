<?php declare(strict_types=1);

namespace OpenTraining\Registration\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use OpenTraining\Registration\Entity\TrainingRegistration;

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
     * @return TrainingRegistration[]
     */
    public function getUnpaid(): array
    {
        return $this->entityRepository->findBy([
            'isPaid' => false,
        ]);
    }
}
