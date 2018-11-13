<?php declare(strict_types=1);

namespace OpenTraining\Provision;

use OpenTraining\Provision\Repository\PartnerExpenseRepository;
use OpenTraining\Provision\Repository\PartnerRepository;
use OpenTraining\Training\Entity\TrainingTerm;

final class ProvisionResolver
{
    /**
     * @var PartnerRepository
     */
    private $partnerRepository;

    /**
     * @var PartnerExpenseRepository
     */
    private $partnerExpenseRepository;

    public function __construct(
        PartnerRepository $partnerRepository,
        PartnerExpenseRepository $partnerExpenseRepository
    ) {
        $this->partnerRepository = $partnerRepository;
        $this->partnerExpenseRepository = $partnerExpenseRepository;
    }

    public function resolveForTrainingTerm(TrainingTerm $trainingTerm): void
    {
        $income = $trainingTerm->getIncome();
        $expense = $this->partnerExpenseRepository->getExpenseForTrainingTerm($trainingTerm);
        $profit = $income - $expense;

        $partnersWithExpense = $this->partnerRepository->fetchAllWithExpenseForTrainingTerm($trainingTerm);

        foreach ($partnersWithExpense as $partnerData) {
            $partnerProfit = $this->resolvePartnerProfit($profit, $partnerData);
            $partnerData->changeProfit($partnerProfit);
        }
    }

    private function resolvePartnerProfit(float $profit, PartnerData $partnerData): int
    {
        $result = $profit * $partnerData->getProvisionRate();

        // cover his or her expenses
        return (int) $result + $partnerData->getExpenses();
    }
}
