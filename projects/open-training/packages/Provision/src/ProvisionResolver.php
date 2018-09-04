<?php declare(strict_types=1);

namespace OpenTraining\Provision;

use App\Entity\TrainingTerm;
//use OpenTraining\Provision\Data\PartnerData;
//use OpenTraining\Provision\Data\ProvisionData;
use OpenTraining\Provision\Repository\PartnerExpenseRepository;
use OpenTraining\Provision\Repository\PartnerRepository;

final class ProvisionResolver
{
    /**
     * @todo reword to database approach
     *
     * To cover dual tax payments by main invoicing entity
     * 10 000  profit ~= 2000 taxes
     *
     * @var float
     */
    private const TAX_BALANCER_LECTOR = 0.11;

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
        dump($partnersWithExpense);
        die;

        foreach ($provisionData->getPartnerDatas() as $partnerData) {
            $partnerProfit = $this->resolvePartnerProfit($profit, $partnerData);
            $partnerData->changeProfit($partnerProfit);
        }
    }

    private function resolvePartnerProfit(int $profit, PartnerData $partnerData): int
    {
        $result = $profit * $partnerData->getProvisionRate();

        // to cover his or her taxes payment from original income
        if ($partnerData->isOfficialInvoicer() === false) {
            $result *= (1 - self::TAX_BALANCER_LECTOR);
        }

        // cover his or her expenses
        return (int) $result + $partnerData->getExpenses();
    }
}
