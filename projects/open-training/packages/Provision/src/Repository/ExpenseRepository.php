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
        $expenseByPartner = $this->entityRepository->createQueryBuilder('e')
            ->select('SUM(e.amount) as expense')
            ->where('e.trainingTerm = :trainingTerm')
            ->setParameter(':trainingTerm', $trainingTerm)
            ->groupBy('e.partner')
            ->getQuery()
            ->getArrayResult();

        return new TrainingTermExpenses(
            $expenseByPartner[Partner::OWNER] ?? 0.0,
            $expenseByPartner[Partner::ORGANIZER] ?? 0.0,
            $expenseByPartner[Partner::TRAINER] ?? 0.0
        );
    }
}
