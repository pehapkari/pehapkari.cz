<?php declare(strict_types=1);

namespace Pehapkari\Provision\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Pehapkari\Provision\Data\Partner;
use Pehapkari\Provision\Data\TrainingTermExpenses;
use Pehapkari\Provision\Entity\Expense;
use Pehapkari\Training\Entity\TrainingTerm;

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
            $this->getExpensesByTrainingTermAndPartner($trainingTerm, Partner::TRAINER),
            $this->getExpensesByTrainingTermAndPartner($trainingTerm, Partner::ORGANIZER),
            $this->getExpensesByTrainingTermAndPartner($trainingTerm, Partner::OWNER)
        );
    }

    private function getExpensesByTrainingTermAndPartner(TrainingTerm $trainingTerm, string $partner): float
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
