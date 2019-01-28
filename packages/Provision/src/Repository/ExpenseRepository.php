<?php declare(strict_types=1);

namespace OpenTraining\Provision\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use OpenTraining\Provision\Data\Partner;
use OpenTraining\Provision\Data\TrainingTermExpenses;
use OpenTraining\Provision\Entity\Expense;
use OpenTraining\Training\Entity\TrainingTerm;

final class ExpenseRepository
{
    /**
     * @var EntityRepository
     */
    private $entityRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityRepository = $entityManager->getRepository(Expense::class);
    }

    public function getExpensesByTrainingTerm(TrainingTerm $trainingTerm): TrainingTermExpenses
    {
        return new TrainingTermExpenses(
            $this->getExpesnseByTrainingTermAndPartner($trainingTerm, Partner::TRAINER),
            $this->getExpesnseByTrainingTermAndPartner($trainingTerm, Partner::ORGANIZER),
            $this->getExpesnseByTrainingTermAndPartner($trainingTerm, Partner::OWNER)
        );
    }

    private function getExpesnseByTrainingTermAndPartner(TrainingTerm $trainingTerm, string $partner): float
    {
        return (float) $this->entityRepository->createQueryBuilder('e')
            ->select('SUM(e.amount) as expense')
            ->andWhere('e.trainingTerm = :trainingTerm')
            ->setParameter(':trainingTerm', $trainingTerm)
            ->andWhere('e.partner = :partner')
            ->setParameter(':partner', $partner)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
