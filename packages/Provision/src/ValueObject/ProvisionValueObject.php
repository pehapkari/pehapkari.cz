<?php

declare(strict_types=1);

namespace Pehapkari\Provision\ValueObject;

final class ProvisionValueObject
{
    /**
     * @var int
     */
    private $previouslyFinishedTrainingCount;

    /**
     * @var float
     */
    private $income;

    /**
     * @var float
     */
    private $expense;

    /**
     * @var float
     */
    private $ownerExpense;

    /**
     * @var float
     */
    private $trainerExpense;

    /**
     * @var float
     */
    private $trainerProvisionRate;

    /**
     * @var float
     */
    private $trainerProvision;

    /**
     * @var float
     */
    private $trainerProvisionWithExpense;

    /**
     * @var float
     */
    private $ownerProvisionRate;

    /**
     * @var float
     */
    private $ownerProvision;

    /**
     * @var float
     */
    private $profit;

    public function __construct(
        int $previouslyFinishedTrainingCount,
        float $income,
        float $expense,
        float $ownerExpense,
        float $trainerExpense
    ) {
        $this->profit = $income - $expense;
        $this->previouslyFinishedTrainingCount = $previouslyFinishedTrainingCount;
        $this->income = $income;
        $this->expense = $expense;
        $this->ownerExpense = $ownerExpense;
        $this->trainerExpense = $trainerExpense;

        // calculated
        $this->trainerProvisionRate = $previouslyFinishedTrainingCount >= 5 ? ProvisionRate::MORE_THAN_5_TRAININGS_INCLUDED : ProvisionRate::UNDER_5_TRAININGS;

        // be nice to the trainer with "ceil" :)
        $this->trainerProvision = ceil($this->profit * $this->trainerProvisionRate / 100.0);
        $this->trainerProvisionWithExpense =
            $this->trainerProvision + $trainerExpense;

        $this->ownerProvisionRate = 100.0 - $this->trainerProvisionRate;
        $this->ownerProvision = $this->profit - $this->trainerProvision;
    }

    public function getPreviouslyFinishedTrainingCount(): int
    {
        return $this->previouslyFinishedTrainingCount;
    }

    public function getIncome(): float
    {
        return $this->income;
    }

    public function getExpense(): float
    {
        return $this->expense;
    }

    public function getOwnerExpense(): float
    {
        return $this->ownerExpense;
    }

    public function getTrainerExpense(): float
    {
        return $this->trainerExpense;
    }

    public function getTrainerProvisionRate(): float
    {
        return $this->trainerProvisionRate;
    }

    public function getTrainerProvision(): float
    {
        return $this->trainerProvision;
    }

    public function getTrainerProvisionWithExpense(): float
    {
        return $this->trainerProvisionWithExpense;
    }

    public function getOwnerProvisionRate(): float
    {
        return $this->ownerProvisionRate;
    }

    public function getOwnerProvision(): float
    {
        return $this->ownerProvision;
    }

    public function getProfit(): float
    {
        return $this->profit;
    }
}
