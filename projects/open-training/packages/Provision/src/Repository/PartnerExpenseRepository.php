<?php declare(strict_types=1);

namespace OpenTraining\Provision\Repository;

use App\Entity\TrainingTerm;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use OpenTraining\Provision\Entity\PartnerExpense;

final class PartnerExpenseRepository
{
    /**
     * @var EntityRepository
     */
    private $entityRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityRepository = $entityManager->getRepository(PartnerExpense::class);
    }

    public function getExpenseForTrainingTerm(TrainingTerm $trainingTerm): float
    {
        return (float) $this->entityRepository->createQueryBuilder('pe')
            ->select('SUM(pe.amount) as expense')
            ->where('pe.trainingTerm = :trainingTerm')
            ->setParameter(':trainingTerm', $trainingTerm)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
